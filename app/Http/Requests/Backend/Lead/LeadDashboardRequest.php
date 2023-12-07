<?php

namespace App\Http\Requests\Backend\Lead;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LeadDashboardRequest  extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
//        return auth()
//            ->user()
//            ->isAdmin();
        return true;
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'search' => 'nullable|string',
            'officeId' => 'nullable|integer',
            'userId' => 'nullable|integer',
            'length' => 'required|integer',

            'dir' => 'required|string',
            'lowUsage' => 'nullable|boolean',
            'passedIntegrations' => 'nullable|boolean',
            'jij' => 'nullable|boolean',
            'sat' => 'nullable|boolean',
            'closed' => 'nullable|boolean',
            'creditPass' => 'nullable|boolean',
            'status' => 'array',
            'selectedUser' => 'nullable|integer',
            'appointment' => 'nullable|boolean',
            'projectManager' => 'nullable|boolean',
            'callCenter' => 'nullable|boolean',
            'inHouse' => 'nullable|boolean',
            'regionId' => 'nullable|integer',
            'appointmentSet' => 'nullable|boolean',
            'permitApproved' => 'nullable|boolean',
            'stale' => 'nullable|boolean',
        ];
    }
}
