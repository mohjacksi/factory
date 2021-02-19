<?php

namespace App\Traits;

use App\ThirdParty\SmsMasr;
use Symfony\Component\HttpFoundation\Response;


trait HelpersTrait
{
    /**
     * check if keys in array
     * @param array $keys
     * @param array $arr
     * @return bool
     */
    function array_keys_exists(array $keys, array $arr)
    {
        return !array_diff_key(array_flip($keys), $arr);
    }

    /**
     * send sms message to verify user phone
     * @param $phone
     * @param $token
     * @param $msg
     * @return bool
     */
    public function sendSmsMessage($phone, $token, $msg = ' :كود التفعيل ')
    {
        $message = $token . $msg;
        $smsMasr = new SmsMasr($message, [$phone]);
        $smsMasr->sendMessage();
        return true;
    }

}
