<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyColorRequest;
use App\Http\Requests\StoreColorRequest;
use App\Http\Requests\UpdateColorRequest;
use App\Models\Color;
use App\Repositories\ColorRepository;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ColorsController extends Controller
{
    /**
     * @var ColorRepository
     */
    private $repository;

    public function __construct(ColorRepository $repository)
    {

        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        //abort_if(Gate::denies('color_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Color::query()->select(sprintf('%s.*', (new Color)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'color_show';
                $editGate = 'color_edit';
                $deleteGate = 'color_delete';
                $crudRoutePart = 'colors';

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

        return view('admin.colors.index');
    }

    public function create()
    {
        //abort_if(Gate::denies('color_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.colors.create');
    }

    public function store(StoreColorRequest $request)
    {
        $color = Color::create($request->all());

        return redirect()->route('admin.colors.index');
    }

    public function edit(Color $color)
    {
        //abort_if(Gate::denies('color_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.colors.edit', compact('color'));
    }

    public function update(UpdateColorRequest $request, Color $color)
    {
        $color->update($request->all());

        return redirect()->route('admin.colors.index');
    }

    public function show(Color $color)
    {
        //abort_if(Gate::denies('color_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

//        $color->load('colorDepartments', 'colorNews', 'colorJobOffers');

        return view('admin.colors.show', compact('color'));
    }

    public function destroy(Color $color)
    {
        //abort_if(Gate::denies('color_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($this->repository->checkForExistenceInOtherModel($color)) {
            return back()->withErrors('
                لا نستطيع حذف هذا اللون،
                لإنها مُرتبطه بقسم بالفعل!
                ' . $color->name);
        }
        $color->forcedelete();

        return back();
    }

    public function massDestroy(MassDestroyColorRequest $request)
    {
        $colors = Color::whereIn('id', request('ids'));
        foreach ($colors->get() as $color) {
            if ($this->repository->checkForExistenceInOtherModel($color)) {
                return back()->withErrors($color->name . '
                لا نستطيع حذف هذه المدينة،
                لإنها مُرتبطه بقسم بالفعل!
                ');
            }
        }

        $colors->forcedelete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
