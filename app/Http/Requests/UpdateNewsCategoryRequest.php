<?php

namespace App\Http\Requests;

use App\Models\Category;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateNewsCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('news_category_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ]
        ];
    }
}
