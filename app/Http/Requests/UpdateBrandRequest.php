<?php

namespace App\Http\Requests;

use App\Models\City;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateBrandRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('brand_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
                'unique:brands,name,'.$this->id,
            ],
        ];
    }
}
