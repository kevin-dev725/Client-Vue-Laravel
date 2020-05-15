<?php

namespace App\Http\Requests\Api\Contractor;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
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
            'skills' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'required_with:city|nullable|string'
            // 'nearby.radius' => 'nullable|numeric',
            // 'nearby.lat' => 'required_with:nearby.lng|nullable|numeric',
            // 'nearby.lng' => 'required_with:nearby.lat|nullable|numeric',
        ];
    }
}
