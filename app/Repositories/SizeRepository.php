<?php


namespace App\Repositories;

use App\Models\City;
use App\Models\Color;
use App\Models\Coupon;
use App\Models\Permission;
use App\Models\Size;

class SizeRepository
{

    /**
     * check if it has relation in other model before deleting
     * @param $size
     */
    public function checkForExistenceInOtherModel(Size $size)
    {
        $check = 0;

        if ($size->variants()->exists()) {
            $check = 1;
        }
        return $check;
    }
}
