<?php

namespace App\Http\Requests\Frontend\Lead;

use Illuminate\Foundation\Http\FormRequest;

class StoreFileRequest extends FormRequest
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
            'files' => 'required|mimes:tiff,jpg,jpeg,bmp,png,pdf',
        ];
    }

    public function messages()
    {
        return [
            'files.required' => 'A file is required in order to upload a file...',
            'files.mimes' => 'Invalid file type.',
        ];
    }
}
