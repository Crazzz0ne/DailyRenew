<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class UtilityUpdateRequest extends FormRequest
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

    public function prepareForValidation()
    {
        //Sanitizing
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name_on_bill' => 'sometimes|required|string',
            'power_company_id' => 'sometimes|required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'name_on_bill:required' => "Name is required.",
            'name_on_bill:string' => "Name Must Be of type String.",
            'name_on_bill:alpha' => "Name Must be only characters.",

            'power_company_id:required' => "Power Company is required.",
            'power_company_id:numeric' => "Power Company ID Must be numerical.",
        ];
    }
}
