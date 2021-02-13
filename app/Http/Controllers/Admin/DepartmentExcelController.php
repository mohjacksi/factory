<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DepartmentExcel;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;

class DepartmentExcelController extends Controller
{

    public function index(Request $request)
    {
        //abort_if(Gate::denies('department_excel_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = DepartmentExcel::with('user')->select(sprintf('%s.*', (new DepartmentExcel)->table));
            $table = DataTables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = '';
                $editGate = '';
                $deleteGate = 'excel_delete';
                $crudRoutePart = 'department_excels';
                $approveGate = 'excel_delete';
                $department_excel = 1;
                $excel = 1;

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'approveGate',
                    'department_excel',
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

        return view('admin.department_excels.index');
    }

    public function create()
    {
        //abort_if(Gate::denies('department_excel_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.department_excels.create');
    }

    public function store(Request $request)
    {
        $department_excel = DepartmentExcel::create($request->all());

        return redirect()->route('admin.department_excels.index');
    }

    public function edit(DepartmentExcel $department_excel)
    {
        //abort_if(Gate::denies('department_excel_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.department_excels.edit', compact('department_excel'));
    }

    public function update(Request $request, DepartmentExcel $department_excel)
    {
        $department_excel->update($request->all());

        return redirect()->route('admin.department_excels.index');
    }

    public function show(DepartmentExcel $department_excel)
    {
        //abort_if(Gate::denies('department_excel_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        return view('admin.department_excels.show', compact('department_excel'));
    }

    public function destroy(DepartmentExcel $department_excel)
    {
        //abort_if(Gate::denies('excel_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $department_excel->forcedelete();

        return back();
    }

    public function massDestroy(Request $request)
    {
        $department_excels = DepartmentExcel::whereIn('id', request('ids'));

        $department_excels->forcedelete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
