<?php

namespace App\Http\Requests\Api\License;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'number' => 'required|string|max:255',
            'expiration' => 'nullable|date',
            'is_insured' => 'boolean',
            'photos' => 'array',
            'photos.*.photo' => 'required|image',
            'certs' => 'array',
            'certs.*.photo' => 'required|image',
        ];
    }
}
