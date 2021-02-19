<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyDepartmentRequest;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Imports\DepartmentsImport;
use App\Models\Category;
use App\Models\City;
use App\Models\Department;
use App\Models\DepartmentExcel;
use App\Models\Helpers\UploadExcel;
use App\Models\SubCategory;
use App\Repositories\GateRepository;
use Dotenv\Exception\ValidationException;
use Gate;
use Illuminate\Http\Request;
use Excel;
use finfo;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Exception;
use Spatie\MediaLibrary\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Trader;

class DepartmentsController extends Controller
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
        //abort_if(Gate::denies('department_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->repo->user = auth()->user();

        if ($request->ajax()) {
            $query = Department::with(['city', 'category', 'trader', 'sub_category'])
                ->orderBy('item_number')
                ->select(sprintf('%s.*', (new Department)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $parameters = [
                    $row->category->name,
                    $row->category->type,
                    $row->sub_category->name,
                ];

                $viewGate = $this->repo->get_gate($parameters, 'department', '_show');
                $editGate = $this->repo->get_gate($parameters, 'department', '_edit');
                $deleteGate = $this->repo->get_gate($parameters, 'department', '_delete');

                $crudRoutePart = 'departments';

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

            $table->editColumn('item_number', function ($row) {
                return $row->item_number;
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : "";
            });
            $table->editColumn('show_in_main_page', function ($row) {
                return $row->show_in_main_page;
            });
            $table->editColumn('show_in_main_departments_page', function ($row) {
                return $row->show_in_main_departments_page;
            });
            $table->editColumn('logo', function ($row) {
                if ($photo = $row->logo) {
                    return sprintf(
                        '<a href="%s" target="_blank"><img src="%s" width="50px" height="50px"></a>',
                        $photo->url,
                        $photo->thumbnail
                    );
                }

                return '';
            });
            $table->editColumn('about', function ($row) {
                return $row->about ? $row->about : "";
            });
            $table->addColumn('city_name', function ($row) {
                return $row->city ? $row->city->name : '';
            });

            $table->editColumn('phone_number', function ($row) {
                return $row->phone_number ? $row->phone_number : "";
            });

            $table->addColumn('category_name', function ($row) {
                return $row->category ? $row->category->name : '';
            });
            $table->addColumn('sub_category_name', function ($row) {
                return $row->sub_category ? $row->sub_category->name : '';
            });
            $table->addColumn('sub_category_name', function ($row) {
                return $row->sub_category ? $row->sub_category->name : '';
            });
            $table->addColumn('trader_name', function ($row) {
                return $row->trader ? $row->trader->name : '';
            });
            $table->rawColumns(['actions', 'placeholder', 'logo', 'city', 'category']);

            return $table->make(true);
        }

        $cities = City::get();
        $categories = Category::get();
        $sub_categories = SubCategory::get();
        $traders = Trader::get();


        $category = new \ReflectionClass(new Category);
        $constants = $category->getConstants();
        $constants = $constants['TYPE_RADIO'];

        $constants_flips = array_flip($constants);
        $department_excel_not_read_count = DepartmentExcel::where('is_read', 0)->count();


        return view('admin.departments.index', compact('sub_categories', 'cities', 'categories', 'constants_flips', 'traders', 'department_excel_not_read_count', 'constants'));
    }

    public function create()
    {
        //abort_if(Gate::denies('department_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $cities = City::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $categories = Category::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $traders = Trader::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.departments.create', compact('cities', 'categories', 'traders'));
    }

    public function store(StoreDepartmentRequest $request)
    {
        $request['show_in_main_departments_page'] = $request->show_in_main_departments_page ? 1 : 0;

        $request['show_in_main_page'] = $request->show_in_main_page ? 1 : 0;

        if (!$request->item_number) {
            $last_item_number = Department::latest()->first()->item_number;
            $request['item_number'] = $last_item_number->item_number + 1;
        }

        $department = Department::create($request->all());


        if ($request->input('logo', false)) {
            $department->addMedia(storage_path('tmp/uploads/' . $request->input('logo')))->toMediaCollection('logo');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $department->id]);
        }

        return redirect()->route('admin.departments.index');
    }

    public function edit(Department $department)
    {
        //abort_if(Gate::denies('department_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $cities = City::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $categories = Category::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $department->load('city', 'category');

        $traders = Trader::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $sub_category_id = $department->sub_category->id;

        return view('admin.departments.edit', compact('cities', 'categories', 'department', 'traders','sub_category_id'));
    }

    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        $request['show_in_main_departments_page'] = $request->show_in_main_departments_page ? 1 : 0;

        $request['show_in_main_page'] = $request->show_in_main_page ? 1 : 0;

        $department->update($request->all());

        if ($request->input('logo', false)) {
            if (!$department->logo || $request->input('logo') !== $department->logo->file_name) {
                if ($department->logo) {
                    $department->logo->delete();
                }

                $department->addMedia(storage_path('tmp/uploads/' . $request->input('logo')))->toMediaCollection('logo');
            }
        } elseif ($department->logo) {
            $department->logo->delete();
        }

        return redirect()->route('admin.departments.index');
    }

    public function show(Department $department)
    {
        //abort_if(Gate::denies('department_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $department->load('city', 'category');

        return view('admin.departments.show', compact('department'));
    }

    public function destroy(Department $department)
    {
        //abort_if(Gate::denies('department_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $department->delete();

        return back();
    }

    public function massDestroy(MassDestroyDepartmentRequest $request)
    {
        Department::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        //abort_if(Gate::denies('department_create') && Gate::denies('department_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model = new Department();
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

        if (!$request->file('excel_file')) {
            return redirect()->back()->withErrors(['error' => 'يُرجى اختيار ملف أولا']);
        }

        if (in_array(1, $user->roles()->pluck('role_id')->toArray())) {

            list($departmentExcel, $departmentExcelMedia, $file) = UploadExcel::prepareFileForExcelUpload($id, $request, new DepartmentExcel);

            try {

                UploadExcel::executeUploadExcel(DepartmentsImport::class, $file, $departmentExcel, $departmentExcelMedia);

                return back()->with('success', 'تم الإضافة');

            } catch (ValidationException $e) {
                DB::rollback();
                return back()->with('error', $e->getMessage());
            }

        } else {
            $departmentExcel = DepartmentExcel::create([
                'user_id' => $user->id
            ]);
            $departmentExcel->addMedia($request->file('excel_file'))->toMediaCollection('file');

            return back()->with('success', 'تم الإضافة، بإنتظار مراجعة الأدمن');
        }
    }

}
