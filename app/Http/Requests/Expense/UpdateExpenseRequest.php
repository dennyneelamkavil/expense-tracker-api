<?php

namespace App\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation Rules.
     *
     * @return array<string, array<int,string>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'description' => [
                'required',
                'string',
                'max:1000',
            ],

            'category_id' => [
                'required',
                'exists:categories,id',
            ],

            'expense_date' => [
                'required',
                'date',
            ],
        ];
    }
}
