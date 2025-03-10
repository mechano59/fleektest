<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostFilterRequest extends FormRequest
{
    public function rules()
    {
        return [
            'search' => 'nullable|string|max:255',
            'category' => 'nullable|exists:categories,category_id',
            'author' => 'nullable|exists:users,id',
            'sort' => 'nullable|in:latest,oldest,title_asc,title_desc',
            'page' => 'sometimes|integer',
        ];
    }
}
