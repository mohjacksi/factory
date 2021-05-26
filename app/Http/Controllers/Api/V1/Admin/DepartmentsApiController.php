<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Filters\DepartmentsFilter;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Resources\Admin\DepartmentResource;
use App\Models\City;
use App\Models\Department;
use App\Models\Trader;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DepartmentsApiController extends Controller
{
    use MediaUploadingTrait;

    /**
     * @var DepartmentsFilter
     */
    private $filter;

    /**
     * DepartmentsApiController constructor.
     * @param DepartmentsFilter $filter
     */
    public function __construct(DepartmentsFilter $filter)
    {
        $this->filter = $filter;
    }

    public function index(Request $request)
    {
        //abort_if(Gate::denies('department_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $departmentQueryBuilder = Department::filter($this->filter)->with('city', 'category', 'sub_category','trader');

        $city_id = $request['city_id'];

        $category_id = $request['category_id'];

        $sub_category_id = $request['sub_category_id'];

        $about = $request['about'];
        $TraderNames = $request['TraderNames'];

        if (isset($city_id)) {
            $departmentQueryBuilder = $departmentQueryBuilder->where('city_id', $city_id);
        }
        if (isset($category_id)) {
            $departmentQueryBuilder = $departmentQueryBuilder->where('category_id', $category_id);
        }
        if (isset($sub_category_id)) {
            $departmentQueryBuilder = $departmentQueryBuilder->where('sub_category_id', $sub_category_id);
        }
        if (isset($about)) {
            $departmentQueryBuilder = $departmentQueryBuilder->where('about', 'like', "%" . $about . "%");
        }

        if (isset($TraderNames)) {

            if (!is_array($TraderNames))
                $TraderNames = array($TraderNames);
            $arr = [];
            foreach ($TraderNames as $singleTrader) {
                $tradersID = Trader::where('name', 'like', '%' . $singleTrader . '%')->pluck('id')->toArray();
                foreach ($tradersID as $singleTraderId) {
                    $arr[] = $singleTraderId;
                }
            }
            $departmentQueryBuilder = $departmentQueryBuilder->WhereHas('trader', function ($q) use ($arr) {
                $q->whereIn('id', $arr);
            });
        }

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

    public function show($department)
    {
        //abort_if(Gate::denies('department_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new DepartmentResource(Department::findOrFail($department)->load(['sub_category', 'city', 'category']));
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


    public function getCityOfTrader($trader_id = null)
    {
        if ($trader_id) {
            $cities = City::select('id', 'name')->whereHas('traders', function ($q) use ($trader_id) {
                $q->where('id', $trader_id);
            })->get();
        } else {
            $cities = City::select('id', 'name')->get();
        }

        return \response()->json([
            $cities,
        ]);

    }

    public function getTradersOfCity($city_id = null)
    {
        if ($city_id) {
            $traders = Trader::select('id', 'name')->whereHas('city', function ($q) use ($city_id) {
                $q->where('id', $city_id);
            })->get();
        } else {
            $traders = Trader::select('id', 'name')->get();
        }

        return \response()->json([
            $traders,
        ]);

    }

    public function getDepartmentsOfTrader($trader_id = null)
    {
        if ($trader_id) {
            $traders = Department::select('id', 'name')->where('trader_id', $trader_id)->get();
        } else {
            $traders = Department::select('id', 'name')->get();
        }

        return \response()->json([
            $traders,
        ]);

    }

}
