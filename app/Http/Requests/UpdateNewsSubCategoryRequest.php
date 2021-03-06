<?php

namespace App\Http\Requests;

use App\Models\Category;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateNewsSubCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('news_sub_category_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'news_category_id' => [
                'required',
                'exists:news_categories,id',
            ],
        ];
    }
}
