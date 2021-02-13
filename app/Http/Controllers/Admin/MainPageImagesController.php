<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyAdvertisementRequest;
use App\Http\Requests\StoreAdvertisementRequest;
use App\Http\Requests\UpdateAdvertisementRequest;
use App\Models\Advertisement;
use App\Models\City;
use App\Repositories\MainPageImageRepository;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class MainPageImagesController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        //abort_if(Gate::denies('advertisement_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Advertisement::with('city')->select(sprintf('%s.*', (new Advertisement)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'advertisement_show';
                $editGate = 'advertisement_edit';
                $deleteGate = 'advertisement_delete';
                $crudRoutePart = 'mainpageimages';

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

            $table->addColumn('city_name', function ($row) {
                return $row->city ? $row->city->name : "";
            });

            $table->editColumn('images', function ($row) {
                if (!$row->images) {
                    return '';
                }

                $links = [];

                foreach ($row->images as $media) {
                    $links[] = '<a href="' . $media->getUrl() . '" target="_blank"><img src="' .$media->getUrl()  . '" width="50px" height="50px"></a>';
                }

                return implode(' ', $links);
            });



            $table->rawColumns(['actions', 'placeholder', 'images']);

            return $table->make(true);
        }

        $cities = City::get();

        return view('admin.mainpageimages.index',compact('cities'));
    }

    public function create()
    {
        //abort_if(Gate::denies('advertisement_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        $cities = City::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.mainpageimages.create',compact('cities'));
    }

    public function store(StoreAdvertisementRequest $request)
    {
        $advertisement = Advertisement::create($request->all());

        if ($request->input('image', false)) {
            foreach ($request->input('images', []) as $file) {
                $advertisement->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('images');
            }
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $advertisement->id]);
        }

        return redirect()->route('admin.mainpageimages.index');
    }

    public function edit($advertisement)
    {
        //abort_if(Gate::denies('advertisement_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $advertisement = Advertisement::findOrFail($advertisement);

        $cities = City::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.mainpageimages.edit', compact('advertisement','cities'));
    }

    public function update(UpdateAdvertisementRequest $request,  $advertisement)
    {
        $advertisement = Advertisement::findOrFail($advertisement);

        $advertisement->update($request->all());

        $advertisement_repo = new MainPageImageRepository;

        if ($request->input('images', false)) {
//            dd('ss');
            $advertisement_repo->updateMedia($advertisement, $advertisement->getMedia('images'), $request->input('images'));
        } elseif ($advertisement->image) {
            $advertisement->image->delete();
        }

        return redirect()->route('admin.mainpageimages.index');
    }

    public function show($advertisement)
    {
        //abort_if(Gate::denies('advertisement_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $advertisement = Advertisement::findOrFail($advertisement);

        return view('admin.mainpageimages.show', compact('advertisement'));
    }

    public function destroy($advertisement)
    {
        //abort_if(Gate::denies('advertisement_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        Advertisement::findOrFail($advertisement)->delete();

        return back();
    }

    public function massDestroy(MassDestroyAdvertisementRequest $request)
    {
        Advertisement::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        //abort_if(Gate::denies('advertisement_create') && Gate::denies('advertisement_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model = new Advertisement();
        $model->id = $request->input('crud_id', 0);
        $model->exists = true;
        $media = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
