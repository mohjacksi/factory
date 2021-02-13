<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroySizeRequest;
use App\Http\Requests\StoreSizeRequest;
use App\Http\Requests\UpdateSizeRequest;
use App\Models\Size;
use App\Repositories\SizeRepository;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class SizesController extends Controller
{
    /**
     * @var SizeRepository
     */
    private $repository;

    public function __construct(SizeRepository $repository)
    {

        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        //abort_if(Gate::denies('size_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Size::query()->select(sprintf('%s.*', (new Size)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'size_show';
                $editGate = 'size_edit';
                $deleteGate = 'size_delete';
                $crudRoutePart = 'sizes';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : "";
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : "";
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.sizes.index');
    }

    public function create()
    {
        //abort_if(Gate::denies('size_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.sizes.create');
    }

    public function store(StoreSizeRequest $request)
    {
        $size = Size::create($request->all());

        return redirect()->route('admin.sizes.index');
    }

    public function edit(Size $size)
    {
        //abort_if(Gate::denies('size_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.sizes.edit', compact('size'));
    }

    public function update(UpdateSizeRequest $request, Size $size)
    {
        $size->update($request->all());

        return redirect()->route('admin.sizes.index');
    }

    public function show(Size $size)
    {
        //abort_if(Gate::denies('size_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

//        $size->load('sizeDepartments', 'sizeNews', 'sizeJobOffers');

        return view('admin.sizes.show', compact('size'));
    }

    public function destroy(Size $size)
    {
        //abort_if(Gate::denies('size_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($this->repository->checkForExistenceInOtherModel($size)) {
            return back()->withErrors('
                لا نستطيع حذف هذا الحجم،
                لإنها مُرتبطه بقسم بالفعل!
                ' . $size->name);
        }
        $size->forcedelete();

        return back();
    }

    public function massDestroy(MassDestroySizeRequest $request)
    {
        $sizes = Size::whereIn('id', request('ids'));
        foreach ($sizes->get() as $size) {
            if ($this->repository->checkForExistenceInOtherModel($size)) {
                return back()->withErrors($size->name . '
                لا نستطيع حذف هذا الحجم،
                لإنها مُرتبطه بقسم بالفعل!
                ');
            }
        }

        $sizes->forcedelete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
