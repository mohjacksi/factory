<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\City;
use App\Models\Department;
use App\Models\Helpers\ExtractImageFromExcelHelper;
use App\Models\SubCategory;
use App\Models\Trader;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Excel;
use Illuminate\Validation\ValidationException;

class DepartmentsImport implements ToModel, WithHeadingRow
{
    /**
     * the excel file uploaded
     * @var null
     */
    private $excel_file;

    /**
     * it contains the number of current rows
     * @var int
     */
    private $rows;
    /**
     * @var int
     */
    private $cnt_of_headers_and_rows_filled_with_data;

    /**
     * DepartmentsImport constructor.
     * @param null $excel_file
     */
    public function __construct($excel_file = null)
    {
        $this->rows = 0;
        $this->excel_file = $excel_file;
        if ($this->excel_file) {
            $this->cnt_of_headers_and_rows_filled_with_data = ExtractImageFromExcelHelper::get_header_and_rows_count($excel_file);
        }
    }

    /**
     * @param array $row
     *
     * @return Department
     * @throws ValidationException
     */
    public function model(array $row)
    {
        if ($this->rows == $this->cnt_of_headers_and_rows_filled_with_data - 1) {
            return null;
        } // it exceeds the rows that have data
        $city = City::where('name', $row[trans("cruds.department.fields.city")])->first();
        $trader = Trader::where('name', $row[trans("cruds.department.fields.trader")])->first();
        $category = Category::where('name', $row[trans("cruds.department.fields.category")])->first();
        $sub_category = SubCategory::where('name', $row[trans("cruds.department.fields.sub_category")])->first();


        if (!$city || !$trader || !$category || !$sub_category) {
            throw ValidationException::withMessages(['field_name' => 'This value is incorrect']);
        }
        $department = Department::firstOrCreate([
            'name' => $row[trans("cruds.department.fields.name")],
            'about' => $row[trans('cruds.department.fields.about')],
            'phone_number' => $row[trans('cruds.department.fields.phone_number')],
            'item_number' => $row[trans('cruds.department.fields.item_number')],
            'show_in_main_page' => $row[trans('cruds.department.fields.show_in_main_page')],
            'show_in_main_departments_page' => $row[trans('cruds.department.fields.show_in_main_departments_page')],
            'city_id' => $city ? $city->id : '',
            'trader_id' => $trader ? $trader->id : '',
            'category_id' => $category ? $category->id : '',
            'sub_category_id' => $sub_category ? $sub_category->id : '',
        ]);

        $this->rows++;


        if ($this->excel_file) {
            ExtractImageFromExcelHelper::importImage($department, 'logo', $this->excel_file, $this->rows);
        }

        return $department;
    }
}
