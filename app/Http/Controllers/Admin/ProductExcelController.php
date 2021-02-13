<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductExcel;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;

class ProductExcelController extends Controller
{

    public function index(Request $request)
    {
        //abort_if(Gate::denies('product_excel_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ProductExcel::with('user')->select(sprintf('%s.*', (new ProductExcel)->table));
            $table = DataTables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = '';
                $editGate = '';
                $deleteGate = 'excel_delete';
                $crudRoutePart = 'product_excels';
                $approveGate = 'excel_delete';
                $excel = 1;

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'approveGate',
                    'excel',
                    'deleteGate',
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

        return view('admin.product_excels.index');
    }

    public function create()
    {
        //abort_if(Gate::denies('product_excel_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.product_excels.create');
    }

    public function store(Request $request)
    {
        $product_excel = ProductExcel::create($request->all());

        return redirect()->route('admin.product_excels.index');
    }

    public function edit(ProductExcel $product_excel)
    {
        //abort_if(Gate::denies('product_excel_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.product_excels.edit', compact('product_excel'));
    }

    public function update(Request $request, ProductExcel $product_excel)
    {
        $product_excel->update($request->all());

        return redirect()->route('admin.product_excels.index');
    }

    public function show(ProductExcel $product_excel)
    {
        //abort_if(Gate::denies('product_excel_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        return view('admin.product_excels.show', compact('product_excel'));
    }

    public function destroy(ProductExcel $product_excel)
    {
        //abort_if(Gate::denies('excel_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $product_excel->forcedelete();

        return back();
    }

    public function massDestroy(Request $request)
    {
        $product_excels = ProductExcel::whereIn('id', request('ids'));

        $product_excels->forcedelete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
