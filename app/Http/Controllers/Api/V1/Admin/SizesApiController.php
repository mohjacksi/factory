<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSizeRequest;
use App\Http\Requests\UpdateSizeRequest;
use App\Http\Resources\Admin\SizeResource;
use App\Models\Size;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SizesApiController extends Controller
{
    public function index()
    {
        //abort_if(Gate::denies('size_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SizeResource(Size::all());
    }

    public function store(StoreSizeRequest $request)
    {
        $size = Size::create($request->all());

        return (new SizeResource($size))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show($size)
    {
        //abort_if(Gate::denies('size_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SizeResource(Size::findOrFail($size));
    }

    public function update(UpdateSizeRequest $request, Size $size)
    {
        $size->update($request->all());

        return (new SizeResource($size))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Size $size)
    {
        //abort_if(Gate::denies('size_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $size->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
