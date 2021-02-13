<?php


namespace App\Models\Helpers;


use App\Imports\ProductsImport;
use Illuminate\Database\Eloquent\Model;
use finfo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Excel;

class UploadExcel
{

    public static function prepareFileForExcelUpload($id, $request, Model $model)
    {

        $Excel = null;
        $ExcelMedia = null;
        if (!$id) {
            $file = $request->file('excel_file');

        } else {
            $Excel = $model::findOrFail($id);
            $ExcelMedia = $Excel->getFirstMedia('file');
//            dd($Excel);
            $file_path = public_path() . '/storage/' . $ExcelMedia->id . '/' . $ExcelMedia->file_name;
            $finfo = new finfo(FILEINFO_MIME_TYPE);

            $file = new UploadedFile(
                $file_path,
                $ExcelMedia->file_name,
                $finfo->file($file_path),
                filesize($file_path),
                0,
                false
            );

        }
        return [$Excel, $ExcelMedia, $file];
    }

    /**
     * @param $model
     * @param $file
     * @param $Excel
     * @param $ExcelMedia
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function executeUploadExcel($model, $file, $Excel, $ExcelMedia)
    {

        DB::beginTransaction();

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);

        Excel::import(new $model($spreadsheet), $file);
        if ($Excel && $ExcelMedia) {

            $ExcelMedia->delete();
            $Excel->delete();
        }
        DB::commit();


    }

}
