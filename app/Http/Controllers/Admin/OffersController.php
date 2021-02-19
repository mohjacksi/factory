<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyOfferRequest;
use App\Http\Requests\StoreOfferRequest;
use App\Http\Requests\UpdateOfferRequest;
use App\Imports\OffersImport;
use App\Models\Category;
use App\Models\City;
use App\Models\Helpers\UploadExcel;
use App\Models\Offer;
use App\Models\OfferExcel;
use App\Models\SubCategory;
use App\Models\Trader;
use App\Repositories\GateRepository;
use Dotenv\Exception\ValidationException;
use Gate;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class OffersController extends Controller
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
        //abort_if(Gate::denies('offer_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->repo->user = auth()->user();

        if ($request->ajax()) {
            $query = Offer::with(['category', 'sub_category', 'trader'])->select(sprintf('%s.*', (new Offer)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $parameters = [
                    $row->category->name,
                    $row->category->type,
                    $row->sub_category->name,
                ];

                $viewGate = $this->repo->get_gate($parameters, 'offer', '_show');
                $editGate = $this->repo->get_gate($parameters, 'offer', '_edit');
                $deleteGate = $this->repo->get_gate($parameters, 'offer', '_delete');

                $crudRoutePart = 'offers';

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

            $table->editColumn('city_name', function ($row) {
                return $row->city ? $row->city->name : "";
            });

            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : "";
            });

            $table->editColumn('show_in_trader_page', function ($row) {
                return $row->show_in_trader_page;
            });
            $table->editColumn('show_in_main_page', function ($row) {
                return $row->show_in_main_page;
            });

            $table->addColumn('show_in_main_offers_page', function ($row) {
                return $row->show_in_main_offers_page;
            });

            $table->addColumn('category_name', function ($row) {
                return $row->category ? $row->category->name : '';
            });

            $table->addColumn('sub_category_name', function ($row) {
                return $row->sub_category ? $row->sub_category->name : '';
            });

            $table->editColumn('phone_number', function ($row) {
                return $row->phone_number ? $row->phone_number : "";
            });
            $table->editColumn('location', function ($row) {
                return $row->location ? $row->location : "";
            });
            $table->editColumn('price', function ($row) {
                return $row->price ? $row->price : "";
            });
            $table->addColumn('trader_name', function ($row) {
                return $row->trader ? $row->trader->name : '';
            });

            $table->editColumn('images', function ($row) {
                if (!$row->images) {
                    return '';
                }

                $links = [];

                foreach ($row->images as $media) {
                    $links[] = '<a href="' . $media->getUrl() . '" target="_blank"><img src="' . $media->getUrl('thumb') . '" width="50px" height="50px"></a>';
                }

                return implode(' ', $links);
            });

            $table->rawColumns(['actions', 'placeholder', 'category', 'trader', 'images']);

            return $table->make(true);
        }

        $categories = Category::get();
        $sub_categories = SubCategory::get();
        $traders = Trader::get();
        $cities = City::get();



        $category = new \ReflectionClass(new Category);
        $constants = $category->getConstants();
        $constants = $constants['TYPE_RADIO'];

        $constants_flips = array_flip($constants);

        $offer_excel_not_read_count = OfferExcel::where('is_read', 0)->count();


        return view('admin.offers.index', compact(
            'constants','constants_flips','categories',
            'cities',
            'traders',
            'offer_excel_not_read_count',
            'sub_categories'));
    }

    public function create()
    {
        //abort_if(Gate::denies('offer_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories = Category::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $cities = City::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $sub_categories = SubCategory::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $traders = Trader::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.offers.create', compact('categories', 'traders', 'sub_categories', 'cities'));
    }

    public function store(StoreOfferRequest $request)
    {
        $request['show_in_main_page'] = $request->has('show_in_main_page') ? 1 : 0;

        $request['show_in_trader_page'] = $request->has('show_in_trader_page') ? 1 : 0;

        $request['show_in_main_offers_page'] = $request->has('show_in_main_offers_page') ? 1 : 0;

        $offer = Offer::create($request->all());

        foreach ($request->input('images', []) as $file) {
            $offer->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('images');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $offer->id]);
        }

        return redirect()->route('admin.offers.index');
    }

    public function edit(Offer $offer)
    {
        //abort_if(Gate::denies('offer_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories = Category::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $sub_categories = SubCategory::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $cities = City::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $traders = Trader::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $offer->load('category', 'trader');

        return view('admin.offers.edit', compact('categories', 'traders', 'offer', 'sub_categories', 'cities'));
    }

    public function update(UpdateOfferRequest $request, Offer $offer)
    {
        $request['show_in_main_page'] = $request->has('show_in_main_page') ? 1 : 0;

        $request['show_in_trader_page'] = $request->has('show_in_trader_page') ? 1 : 0;

        $request['show_in_main_offers_page'] = $request->has('show_in_main_offers_page') ? 1 : 0;

        $offer->update($request->all());


        if (count($offer->images) > 0) {
            foreach ($offer->images as $media) {
                if (!in_array($media->file_name, $request->input('images', []))) {
                    $media->delete();
                }
            }
        }

        $media = $offer->images->pluck('file_name')->toArray();

        foreach ($request->input('images', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $offer->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('images');
            }
        }

        return redirect()->route('admin.offers.index');
    }

    public function show(Offer $offer)
    {
        //abort_if(Gate::denies('offer_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $offer->load('category', 'trader');

        return view('admin.offers.show', compact('offer'));
    }

    public function destroy(Offer $offer)
    {
        //abort_if(Gate::denies('offer_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $offer->delete();

        return back();
    }

    public function massDestroy(MassDestroyOfferRequest $request)
    {
        Offer::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('offer_create') && Gate::denies('offer_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model = new Offer();
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

            list($offerExcel, $offerExcelMedia, $file) = UploadExcel::prepareFileForExcelUpload($id, $request, new OfferExcel);
            try {

                UploadExcel::executeUploadExcel(OffersImport::class, $file, $offerExcel, $offerExcelMedia);

                return back()->with('success', 'تم الإضافة');

            } catch (ValidationException $e) {
                DB::rollback();
                return back()->with('error', $e->getMessage());
            }

        } else {
            $offerExcel = OfferExcel::create([
                'user_id' => $user->id
            ]);
            $offerExcel->addMedia($request->file('excel_file'))->toMediaCollection('file');

            return back()->with('success', 'تم الإضافة، بإنتظار مراجعة الأدمن');
        }
    }
}
