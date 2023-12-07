<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommissionUpdateRequest extends FormRequest
{
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
        return [
            'amount' => 'sometimes|string|max:10'
        ];
    }

    public function messages()
    {
        return [
            'amount.string' => 'Amount Must be of type string.',
            'amount.max' => 'Amount Must be under 10 digits long.',
        ];
    }
}
