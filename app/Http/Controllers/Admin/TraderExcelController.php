<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TraderExcel;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;

class TraderExcelController extends Controller
{

    public function index(Request $request)
    {
        //abort_if(Gate::denies('trader_excel_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = TraderExcel::with('user')->select(sprintf('%s.*', (new TraderExcel)->table));
            $table = DataTables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = '';
                $editGate = '';
                $deleteGate = 'excel_delete';
                $crudRoutePart = 'trader_excels';
                $approveGate = 'excel_delete';
                $trader_excel = 1;
                $excel = 1;

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'approveGate',
                    'trader_excel',
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

        return view('admin.trader_excels.index');
    }

    public function create()
    {
        //abort_if(Gate::denies('trader_excel_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    }

    public function store(Request $request)
    {
        $trader_excel = TraderExcel::create($request->all());

    }

    public function edit(TraderExcel $trader_excel)
    {
        //abort_if(Gate::denies('trader_excel_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    }

    public function update(Request $request, TraderExcel $trader_excel)
    {
    }

    public function show(TraderExcel $trader_excel)
    {
    }

    public function destroy(TraderExcel $trader_excel)
    {
        //abort_if(Gate::denies('excel_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $trader_excel->delete();

        return back();
    }

    public function massDestroy(Request $request)
    {
        $trader_excels = TraderExcel::whereIn('id', request('ids'));

        $trader_excels->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
