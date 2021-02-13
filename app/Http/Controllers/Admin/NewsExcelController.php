<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsExcel;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;

class NewsExcelController extends Controller
{

    public function index(Request $request)
    {
        //abort_if(Gate::denies('news_excel_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = NewsExcel::with('user')->select(sprintf('%s.*', (new NewsExcel)->table));
            $table = DataTables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = '';
                $editGate = '';
                $deleteGate = 'excel_delete';
                $crudRoutePart = 'news_excels';
                $approveGate = 'excel_delete';
                $news_excel = 1;
                $excel = 1;

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'approveGate',
                    'news_excel',
                    'deleteGate',
                    'excel',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : "";
            });
            $table->addColumn('user_name', function ($row) {
                return $row->user ? $row->user->name : "";
            });

            $table->editColumn('file', function ($row) {
                if ($file = $row->file) {
                    return sprintf(
                        '<a href="%s" target="_blank">تحميل</a>',
                        $file->url
                    );
                }

                return '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->escapeColumns([])->make(true);
        }

        return view('admin.news_excels.index');
    }

    public function create()
    {
        //abort_if(Gate::denies('news_excel_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.news_excels.create');
    }

    public function store(Request $request)
    {
        $news_excel = NewsExcel::create($request->all());

        return redirect()->route('admin.news_excels.index');
    }

    public function edit(NewsExcel $news_excel)
    {
        //abort_if(Gate::denies('news_excel_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.news_excels.edit', compact('news_excel'));
    }

    public function update(Request $request, NewsExcel $news_excel)
    {
        $news_excel->update($request->all());

        return redirect()->route('admin.news_excels.index');
    }

    public function show(NewsExcel $news_excel)
    {
        //abort_if(Gate::denies('news_excel_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        return view('admin.news_excels.show', compact('news_excel'));
    }

    public function destroy(NewsExcel $news_excel)
    {
        //abort_if(Gate::denies('excel_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $news_excel->delete();

        return back();
    }

    public function massDestroy(Request $request)
    {
        $news_excels = NewsExcel::whereIn('id', request('ids'));

        $news_excels->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
