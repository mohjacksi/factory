<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreTraderRequest;
use App\Http\Requests\UpdateTraderRequest;
use App\Http\Resources\Admin\TraderResource;
use App\Models\Trader;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TraderApiController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        //abort_if(Gate::denies('trader_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $traderQuery = Trader::with('products', 'offers');

        $details = $request['details'];
        $type = $request['type'];

        //abort_if(Gate::denies('category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (isset($type)) {
            $traderQuery = $traderQuery->where('type', $type);
        }
        if (isset($details)) {
            $traderQuery = $traderQuery->where('details', 'like', "%$details%");
        }
        return new TraderResource($traderQuery->get());
    }

    public function store(StoreTraderRequest $request)
    {
        $trader = Trader::create($request->all());

        if ($request->input('images', false)) {
            $trader->addMedia(storage_path('tmp/uploads/' . $request->input('images')))->toMediaCollection('images');
        }

        return (new TraderResource($trader))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show($trader)
    {
        //abort_if(Gate::denies('trader_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new TraderResource(Trader::findOrFail($trader)->append('main_categories','sub_categories')->load(['products', 'offers']));
    }

    public function update(Request $request, Trader $trader)
    {
		//dd($trader);
        $trader->update($request->all());

        if ($request->input('images', false)) {
            if (!$trader->images || $request->input('images') !== $trader->images->file_name) {
                if ($trader->images) {
                    $trader->images->delete();
                }

                $trader->addMedia(storage_path('tmp/uploads/' . $request->input('images')))->toMediaCollection('images');
            }
        } elseif ($trader->images) {
            $trader->images->delete();
        }

        return (new TraderResource($trader))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }
	    public function updateCity(Request $request)
    {
		    $id= $request->id;
 			$city_id= $request->city_id;
			//dd($city_id);
			$trader = User::findOrFail($id);
			        $trader->city_id = $city_id;
			        $trader->save();
			        return (new TraderResource($trader))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);

		}

    public function destroy(Trader $trader)
    {
        //abort_if(Gate::denies('trader_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $trader->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
