<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfferExcel;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;

class OfferExcelController extends Controller
{

    public function index(Request $request)
    {
        //abort_if(Gate::denies('offer_excel_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = OfferExcel::with('user')->select(sprintf('%s.*', (new OfferExcel)->table));
            $table = DataTables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = '';
                $editGate = '';
                $deleteGate = 'excel_delete';
                $crudRoutePart = 'offer_excels';
                $approveGate = 'excel_delete';
                $offer_excel = 1;
                $excel = 1;

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'approveGate',
                    'offer_excel',
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

        return view('admin.offer_excels.index');
    }

    public function create()
    {
        //abort_if(Gate::denies('offer_excel_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    }

    public function store(Request $request)
    {
        $offer_excel = OfferExcel::create($request->all());

    }

    public function edit(OfferExcel $offer_excel)
    {
        //abort_if(Gate::denies('offer_excel_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    }

    public function update(Request $request, OfferExcel $offer_excel)
    {
    }

    public function show(OfferExcel $offer_excel)
    {
    }

    public function destroy(OfferExcel $offer_excel)
    {
        //abort_if(Gate::denies('excel_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $offer_excel->forcedelete();

        return back();
    }

    public function massDestroy(Request $request)
    {
        $offer_excels = OfferExcel::whereIn('id', request('ids'));

        $offer_excels->forcedelete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
