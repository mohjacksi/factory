<?php


namespace App\Repositories;

use App\Models\City;
use App\Models\Brand ;
use App\Models\Coupon;
use App\Models\Permission;

class BrandRepository
{

    /**
     * check if it has relation in other model before deleting
     * @param $brand
     */
    public function checkForExistenceInOtherModel(Brand $brand)
    {
        $check = 0;

        if ($brand->variants()->exists()) {
            $check = 1;
        }
        return $check;
    }
}
