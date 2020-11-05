<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Resources\Admin\DepartmentResource;
use App\Models\Department;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DepartmentsApiController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        //abort_if(Gate::denies('department_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $departmentQueryBuilder = Department::with(['city', 'category']);

        $city_id = $request['city_id'];
        $category_id = $request['category_id'];

        if(isset($city_id))
            $departmentQueryBuilder = $departmentQueryBuilder->where(['city_id'=>$city_id]);
        if(isset($category_id))
            $departmentQueryBuilder = $departmentQueryBuilder->where(['category_id'=>$category_id]);

        return new DepartmentResource($departmentQueryBuilder->orderBy('created_at', 'desc')->get());
    }

    public function store(StoreDepartmentRequest $request)
    {
        $department = Department::create($request->all());

        if ($request->input('logo', false)) {
            $department->addMedia(storage_path('tmp/uploads/' . $request->input('logo')))->toMediaCollection('logo');
        }

        return (new DepartmentResource($department))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Department $department)
    {
        //abort_if(Gate::denies('department_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new DepartmentResource($department->load(['city', 'category']));
    }

    public function update(UpdateDepartmentRequest $request, Department $department)
    {
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

        return (new DepartmentResource($department))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Department $department)
    {
        //abort_if(Gate::denies('department_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $department->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
