<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreColorRequest;
use App\Http\Requests\UpdateColorRequest;
use App\Http\Resources\Admin\ColorResource;
use App\Models\Color;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ColorsApiController extends Controller
{
    public function index()
    {
        //abort_if(Gate::denies('color_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ColorResource(Color::all());
    }

    public function store(StoreColorRequest $request)
    {
        $color = Color::create($request->all());

        return (new ColorResource($color))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show($color)
    {
        //abort_if(Gate::denies('color_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ColorResource(Color::findOrFail($color));
    }

    public function update(UpdateColorRequest $request, Color $color)
    {
        $color->update($request->all());

        return (new ColorResource($color))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Color $color)
    {
        //abort_if(Gate::denies('color_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $color->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
