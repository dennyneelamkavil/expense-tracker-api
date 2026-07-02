<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

class ReportFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [
            'from' => [
                'nullable',
                'date',
                'required_with:to',
            ],

            'to' => [
                'nullable',
                'date',
                'after_or_equal:from',
            ],

            'category_id' => [
                'nullable',
                'exists:categories,id',
            ],
        ];
    }
}
