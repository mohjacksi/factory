<?php


namespace App\Repositories;

use App\Models\City;
use App\Models\Color;
use App\Models\Coupon;
use App\Models\Permission;

class ColorRepository
{

    /**
     * check if it has relation in other model before deleting
     * @param $color
     */
    public function checkForExistenceInOtherModel(Color $color)
    {
        $check = 0;

        if ($color->variants()->exists()) {
            $check = 1;
        }
        return $check;
    }
}
