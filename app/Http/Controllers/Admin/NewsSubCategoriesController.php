<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyNewsSubCategoryRequest;
use App\Http\Requests\StoreNewsSubCategoryRequest;
use App\Http\Requests\UpdateNewsSubCategoryRequest;
use App\Models\Category;
use App\Models\Helpers\PermissionHelper;
use App\Models\NewsCategory;
use App\Models\NewsSubCategory;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class NewsSubCategoriesController extends Controller
{
    public function index(Request $request)
    {
        //abort_if(Gate::denies('category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = NewsSubCategory::with('news_category')->select(sprintf('%s.*', (new NewsSubCategory)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'news_sub_category_show';
                $editGate      = 'news_sub_category_edit';
                $deleteGate    = 'news_sub_category_delete';
                $crudRoutePart = 'news_sub_categories';

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
            $table->editColumn('news_category_id', function ($row) {
                return $row->news_category ? $row->news_category->name : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.news_sub_categories.index');
    }

    public function create()
    {
        //abort_if(Gate::denies('category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $news_categories = NewsCategory::pluck('name', 'id');

        return view('admin.news_sub_categories.create', compact('news_categories'));
    }

    public function store(StoreNewsSubCategoryRequest $request)
    {
        $news_sub_category = NewsSubCategory::create($request->all());

        PermissionHelper::createPermissionWithModelAttribute($news_sub_category->name);

        return redirect()->route('admin.news_sub_categories.index');
    }

    public function edit(NewsSubCategory $news_sub_category)
    {
        //abort_if(Gate::denies('category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $news_categories = NewsCategory::pluck('name', 'id');

        PermissionHelper::createPermissionWithModelAttribute($news_sub_category->name);

        return view('admin.news_sub_categories.edit', compact('news_sub_category', 'news_categories'));
    }

    public function update(UpdateNewsSubCategoryRequest $request, NewsSubCategory $news_sub_category)
    {
        $news_sub_category->update($request->all());

        PermissionHelper::createPermissionWithModelAttribute($news_sub_category->name);

        return redirect()->route('admin.news_sub_categories.index');
    }

    public function show(NewsSubCategory $news_sub_category)
    {
        //abort_if(Gate::denies('category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.news_sub_categories.show', compact('sub_category'));
    }

    public function destroy(NewsSubCategory $news_sub_category)
    {
        //abort_if(Gate::denies('category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $news_sub_category->forcedelete();

        return back();
    }

    public function massDestroy(MassDestroyNewsSubCategoryRequest $request)
    {
        NewsSubCategory::whereIn('id', request('ids'))->forcedelete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
