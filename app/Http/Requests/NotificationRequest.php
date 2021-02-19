<?php

namespace App\Http\Requests;

use App\Models\Category;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class NotificationRequest extends FormRequest
{
//    public function authorize()
//    {
//        return Gate::allows('coupon_edit');
//    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $k = count($this->segments());
        $endPoint = $this->segment($k);
        switch ($endPoint) {
            case 'get':
                return $this->IdValidation();
            default:
                return [];
        }
    }

    public function IdValidation()
    {
        return [
            'id' => 'exists:notifications,id',
        ];
    }

}
