<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyBrandRequest;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Models\Brand;
use App\Repositories\BrandRepository;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class BrandsController extends Controller
{
    /**
     * @var BrandRepository
     */
    private $repository;

    public function __construct(BrandRepository $repository)
    {

        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        //abort_if(Gate::denies('brand_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Brand::query()->select(sprintf('%s.*', (new Brand)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'brand_show';
                $editGate = 'brand_edit';
                $deleteGate = 'brand_delete';
                $crudRoutePart = 'brands';

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

        return view('admin.brands.index');
    }

    public function create()
    {
        //abort_if(Gate::denies('brand_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.brands.create');
    }

    public function store(StoreBrandRequest $request)
    {
        $brand = Brand::create($request->all());

        return redirect()->route('admin.brands.index');
    }

    public function edit(Brand $brand)
    {
        //abort_if(Gate::denies('brand_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.brands.edit', compact('brand'));
    }

    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        $brand->update($request->all());

        return redirect()->route('admin.brands.index');
    }

    public function show(Brand $brand)
    {
        //abort_if(Gate::denies('brand_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

//        $brand->load('brandDepartments', 'brandNews', 'brandJobOffers');

        return view('admin.brands.show', compact('brand'));
    }

    public function destroy(Brand $brand)
    {
        //abort_if(Gate::denies('brand_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($this->repository->checkForExistenceInOtherModel($brand)) {
            return back()->withErrors('
                لا نستطيع حذف هذا اللون،
                لإنها مُرتبطه بقسم بالفعل!
                ' . $brand->name);
        }
        $brand->delete();

        return back();
    }

    public function massDestroy(MassDestroyBrandRequest $request)
    {
        $brands = Brand::whereIn('id', request('ids'));
        foreach ($brands->get() as $brand) {
            if ($this->repository->checkForExistenceInOtherModel($brand)) {
                return back()->withErrors($brand->name . '
                لا نستطيع حذف هذه المدينة،
                لإنها مُرتبطه بقسم بالفعل!
                ');
            }
        }

        $brands->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
