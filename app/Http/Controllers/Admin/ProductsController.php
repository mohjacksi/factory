<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Imports\ProductsImport;
use App\Models\Brand;
use App\Models\City;
use App\Models\Department;
use App\Models\Helpers\UploadExcel;
use App\Models\MainProductServiceType;
use App\Models\MainProductType;
use App\Models\Product;
use App\Models\ProductExcel;
use App\Models\SubProductServiceType;
use App\Models\SubProductType;
use App\Models\Trader;
use App\Repositories\GateRepository;
use Dotenv\Exception\ValidationException;
use Gate;
use Excel;
use finfo;


use File;
use Illuminate\Http\Request;

//use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Spatie\MediaLibrary\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ProductsController extends Controller
{
    use MediaUploadingTrait;

    /**
     * @var GateRepository
     */
    private $repo;


    /**
     * ProductsController constructor.
     * @param GateRepository $repo
     */
    public function __construct(GateRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $this->repo->user = auth()->user();

        //abort_if(Gate::denies('product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Product::with(['trader', 'city', 'brand', 'department', 'MainProductType', 'MainProductType'
                , 'SubProductType', 'MainProductServiceType', 'SubProductServiceType'])->select(sprintf('%s.*', (new Product)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $parameters = [
                    $row->MainProductType ? $row->MainProductType->name : "",
                    $row->SubProductType ? $row->SubProductType->name : '',
                    $row->MainProductServiceType ? $row->MainProductServiceType->name : '',
                    $row->SubProductServiceType ? $row->SubProductServiceType->name : ''
                ];

                $viewGate = $this->repo->get_gate($parameters, 'product', '_show');
                $editGate = $this->repo->get_gate($parameters, 'product', '_edit');
                $deleteGate = $this->repo->get_gate($parameters, 'product', '_delete');

                $product_excel = 1;
                $crudRoutePart = 'products';

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
            $table->editColumn('image', function ($row) {
                if ($photo = $row->image) {
                    return sprintf(
                        '<a href="%s" target="_blank"><img src="%s" width="50px" height="50px"></a>',
                        $photo->url,
                        $photo->thumbnail
                    );
                }

                return '';
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : "";
            });
            $table->addColumn('main_product_type_name', function ($row) {
                return $row->MainProductType ? $row->MainProductType->name : "";
            });
            $table->addColumn('sub_product_type_name', function ($row) {
                return $row->SubProductType ? $row->SubProductType->name : "";
            });
//            $table->addColumn('checkNull', function ($row) {
//                return  "";
//            });
            $table->addColumn('main_product_service_type_name', function ($row) {
                return $row->MainProductServiceType ? $row->MainProductServiceType->name : "";
            });
            $table->addColumn('sub_product_service_type_name', function ($row) {
                return $row->SubProductServiceType ? $row->SubProductServiceType->name : "";
            });

            /**********/
            $table->addColumn('department_name', function ($row) {
                return $row->department ? $row->department->name : "";
            });
            $table->addColumn('city_name', function ($row) {
                return $row->city ? $row->city->name : "";
            });
            $table->editColumn('show_trader_name', function ($row) {
                return $row->show_trader_name;
            });
            $table->editColumn('details', function ($row) {
                return $row->details ? $row->details : "";
            });
            $table->editColumn('detailed_title', function ($row) {
                return $row->detailed_title ? $row->detailed_title : "";
            });
            $table->editColumn('price_after_discount', function ($row) {
                return $row->price_after_discount;
            });
            $table->editColumn('product_code', function ($row) {
                return $row->product_code ? $row->product_code : "";
            });
            /*********/

            $table->editColumn('show_in_trader_page', function ($row) {
                return $row->show_in_trader_page;
            });
            $table->editColumn('show_in_main_page', function ($row) {
                return $row->show_in_main_page;
            });
            $table->editColumn('price', function ($row) {
                return $row->price;
            });
            $table->addColumn('trader_name', function ($row) {
                return $row->trader ? $row->trader->name : '';
            });
            $table->addColumn('brand_name', function ($row) {
                return $row->brand ? $row->brand->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'image', 'trader']);

            return $table->make(true);
        }

        $traders = Trader::get();
        $main_product_types = MainProductType::get();
        $main_product_service_types = MainProductServiceType::get();
        $sub_product_types = SubProductType::get();
        $sub_product_service_types = SubProductServiceType::get();
        $departments = Department::get();
        $cities = City::get();
        $brands = Brand::get();
        $product_excel_not_read_count = ProductExcel::where('is_read', 0)->count();

        return view('admin.products.index', compact(
            'traders',
            'main_product_types',
            'sub_product_types',
            'sub_product_service_types',
            'main_product_service_types',
            'cities',
            'brands',
            'product_excel_not_read_count',
            'departments'
        ));
    }

    public function create()
    {
        //abort_if(Gate::denies('product_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $traders = Trader::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $departments = Department::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $cities = City::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $main_product_types = MainProductType::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $main_product_service_types = MainProductServiceType::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $brands = Brand::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.products.create', compact('traders', 'brands', 'main_product_types', 'main_product_service_types', 'departments', 'cities'));
    }

    public function store(StoreProductRequest $request)
    {

        $request['is_available'] = $request->is_available ? 1 : 0;

        $request['show_in_main_page'] = $request->has('show_in_main_page') ? 1 : 0;

        $request['show_in_trader_page'] = $request->has('show_in_trader_page') ? 1 : 0;

        $request['show_trader_name'] = $request->has('show_trader_name') ? 1 : 0;

        $product = Product::create($request->all());

        if ($request->input('image', false)) {
            $product->addMedia(storage_path('tmp/uploads/' . $request->input('image')))->toMediaCollection('image');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $product->id]);
        }

        return redirect()->route('admin.products.variants.index', $product);
    }

    public function edit(Product $product)
    {
        //abort_if(Gate::denies('product_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $traders = Trader::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $departments = Department::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $cities = City::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $main_product_types = MainProductType::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $main_product_service_types = MainProductServiceType::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $brands = Brand::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $product_variants = $product->ProductVariants;

        $product->load('trader');

        return view('admin.products.edit', compact(
            'traders',
            'product',
            'departments',
            'cities',
            'main_product_types',
            'main_product_service_types',
            'brands',
            'product_variants'
        ));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
//        dd($request);
        $request['is_available'] = $request->is_available ? 1 : 0;

        $request['show_in_main_page'] = $request->has('show_in_main_page') ? 1 : 0;

        $request['show_in_trader_page'] = $request->has('show_in_trader_page') ? 1 : 0;

        $request['show_trader_name'] = $request->has('show_trader_name') ? 1 : 0;

        $product->update($request->all());


        if ($request->input('image', false)) {
            if (!$product->image || $request->input('image') !== $product->image->file_name) {
                if ($product->image) {
                    $product->image->delete();
                }

                $product->addMedia(storage_path('tmp/uploads/' . $request->input('image')))->toMediaCollection('image');
            }
        } elseif ($product->image) {
            $product->image->delete();
        }

        return redirect()->route('admin.products.index');
    }

    public function show(Product $product)
    {
        //abort_if(Gate::denies('product_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $product->load('trader', 'variants');

        $product_variants = $product->variants;

        return view('admin.products.show', compact('product', 'product_variants'));
    }

    public function destroy(Product $product)
    {
        //abort_if(Gate::denies('product_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $product->delete();
        $product->variants()->delete();
        return back();
    }

    public function massDestroy(MassDestroyProductRequest $request)
    {
        Product::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        //abort_if(Gate::denies('product_create') && Gate::denies('product_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model = new Product();
        $model->id = $request->input('crud_id', 0);
        $model->exists = true;
        $media = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    /**
     * upload from excel part in index blade
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadExcel(Request $request, $id = null)
    {
        $user = Auth::user();

        if (in_array(1, $user->roles()->pluck('role_id')->toArray())) {

            list($productExcel, $productExcelMedia, $file) = UploadExcel::prepareFileForExcelUpload($id, $request, new ProductExcel);
            try {

                UploadExcel::executeUploadExcel(ProductsImport::class, $file, $productExcel, $productExcelMedia);

                return back()->with('success', 'تم الإضافة');

            } catch (ValidationException $e) {
                DB::rollback();
                return back()->with('error', $e->getMessage());
            }

        } else {
            $productExcel = ProductExcel::create([
                'user_id' => $user->id
            ]);
            $productExcel->addMedia($request->file('excel_file'))->toMediaCollection('file');

            return back()->with('success', 'تم الإضافة، بإنتظار مراجعة الأدمن');
        }
    }

}
