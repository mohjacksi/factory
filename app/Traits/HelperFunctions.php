<?php
/**
 * Created by PhpStorm.
 * User: amir
 * Date: 04/10/20
 * Time: 02:48 م
 */

namespace App\Traits;

use Illuminate\Support\Facades\File;

trait HelperFunctions
{
    public function checkUniqueValues(array $array, string $column, $value)
    {
        if (!is_array($array[0]))
            return false;

        $collection = collect($array);
        $count = $collection->where($column, $value)->count();
        return ($count <= 1) ? true : false;
    }

    public function checkDuplicate(array $array, string $column)
    {
        if (!is_array($array[0]))
            return false;
        $collection = collect($array);
        $collection = collect($collection->map(function ($arr) {
            return collect($arr);
        }));
        $unique = $collection->unique($column);
        $dupes = $collection->diff($unique);
        return ($dupes->count() == 0) ? true : false;
    }

    static function removeImage($image)
    {
        $file = str_replace(url('/'), '', $image);
        $path = public_path($file);
        File::delete($path);
    }

    static function makeSlug($string = null, $lettersCount = 100, $separator = "-")
    {
        if (is_null($string)) {
            return "";
        }
        // Remove spaces from the beginning and from the end of the string
        $string = trim($string);
        // Lower case everything
        $string = mb_strtolower($string, "UTF-8");
        // letters and numbers only with arabic letters too
        $string = preg_replace("/[^a-z0-9_\s\-ءاأإآؤئبتثجحخدذرزسشصضطظعغفقكلمنهويةى]/u", "", $string);
        // Remove multiple dashes or whitespaces
        $string = preg_replace("/[\s\-]+/", " ", $string);
        // Convert whitespaces and underscore to the given separator
        $string = preg_replace("/[\s_]/", $separator, $string);
        //limit slug to  $lettersCount = (100)

        return str_limit($string, $lettersCount, '');
    }
}
