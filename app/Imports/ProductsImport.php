<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Category;
use App\Models\City;
use App\Models\Color;
use App\Models\Department;
use App\Models\Helpers\ExtractImageFromExcelHelper;
use App\Models\MainProductServiceType;
use App\Models\MainProductType;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\SubCategory;
use App\Models\SubProductServiceType;
use App\Models\SubProductType;
use App\Models\Trader;
use App\Models\Variant;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Excel;
use Illuminate\Validation\ValidationException;

class ProductsImport implements ToModel, WithHeadingRow
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
     * it counts number of variants in products only
     * @var int
     */
    private $number_of_variants;
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
        $this->number_of_variants = 1;
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

        $this->number_of_variants = 0;
        $city = City::where('name', $row[trans("cruds.product.fields.city_name")])->first();
        $trader = Trader::where('name', $row[trans("cruds.product.fields.trader")])->first();
        $main_product_type_name = MainProductType::where('name', $row[trans("cruds.product.fields.main_product_type_name")])->first();
        $sub_product_type_name = SubProductType::where('name', $row[trans("cruds.product.fields.sub_product_type_name")])->first();
        $main_product_service_type_name = MainProductServiceType::where('name', $row[trans("cruds.product.fields.main_product_service_type_name")])->first();
        $sub_product_service_type_name = SubProductServiceType::where('name', $row[trans("cruds.product.fields.sub_product_service_type_name")])->first();
        $department = Department::where('name', $row[trans("cruds.product.fields.department_name")])->first();

    
        $brand_id = Brand::firstOrCreate([
            'name' => $row[trans("cruds.product.fields.brand_name")]
        ])->id;

        if (!$city || !$brand_id || !$trader || ($sub_product_type_name && $sub_product_service_type_name) || ($main_product_service_type_name && $main_product_type_name) || !$department) {
            throw ValidationException::withMessages(['field_name' => 'This value is incorrect']);
        }
//        var_dump($trader);



        $product = Product::firstOrCreate([
            'brand_id' => $brand_id,
            'details' => $row[trans('cruds.product.fields.details')],
            'show_trader_name' => $row[trans('cruds.product.fields.show_trader_name')],
            'detailed_title' => $row[trans('cruds.product.fields.detailed_title')],
            'price_after_discount' => $row[trans('cruds.product.fields.price_after_discount')],
            'product_code' => $row[trans('cruds.product.fields.product_code')],
            'name' => $row[trans('cruds.product.fields.name')],
            'show_in_trader_page' => $row[trans('cruds.product.fields.show_in_trader_page')],
            'show_in_main_page' => $row[trans('cruds.product.fields.show_in_main_page')],
            'price' => $row[trans('cruds.product.fields.price')],

            'city_id' => $city ? $city->id : '',
            'main_product_type_id' => $main_product_type_name ? $main_product_type_name->id : null,
            'main_product_service_type_id' => $main_product_service_type_name ? $main_product_service_type_name->id : null,
            'sub_product_type_id' => $sub_product_type_name ? $sub_product_type_name->id : null,
            'sub_product_service_type_id' => $sub_product_service_type_name ? $sub_product_service_type_name->id : null,
            'trader_id' => $trader->id,
            'department_id' => $department->id,
        ]);



        $this->rows++;


        if ($this->excel_file) {
            $product = ExtractImageFromExcelHelper::importImage($product, 'image', $this->excel_file, $this->rows, 0, $product = 1);
        }

        /********************************/

        $excel = [];
        $sheet = $this->excel_file->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        //  Loop through each row of the worksheet in turn
        for ($row = 1; $row <= $highestRow; $row++) {
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                NULL, TRUE, FALSE);

            $excel[] = $rowData[0];
        }

        $lol = [];
        foreach ($excel[0] as $key => $value) {

            if ($value == 'نوع') $lol[] = $key;
        }
        /******************* end reading each row ********/

        /**********************/

        foreach ($lol as $places_of_variant_in_header => $value) {
            $value++;
            $check = $excel[$this->rows][$value + 1] ??
                $excel[$this->rows][$value + 2] ??
                $excel[$this->rows][$value + 3] ??
                $excel[$this->rows][$value + 4] ?? null;

            if ($check) {
                $color = Color::where('name', $excel[$this->rows][$value + 1])->first();

                $size = Size::where('name', $excel[$this->rows][$value + 3])->first();
            
            
        if (!$color || !$size) {
            throw ValidationException::withMessages(['field_name' => 'This value is incorrect']);
        }
            
                $variant = Variant::firstOrCreate([
                    'color_id' => $color->id,
                    'price' => $excel[$this->rows][$value + 2],
                    'size_id' => $size->id,
                    'count' => $excel[$this->rows][$value + 4],
                ]);
                $this->number_of_variants++;


                ProductVariant::firstOrCreate([
                    'variant_id' => $variant->id,
                    'product_id' => $product->id,
                    'is_available' => 1,
                ]);


                if ($this->excel_file) {
                    ExtractImageFromExcelHelper::importImage($variant, 'image', $this->excel_file, $this->rows, $this->number_of_variants, 1);
                }

            }
        }


        /*************************/

        return $product;
    }
}
