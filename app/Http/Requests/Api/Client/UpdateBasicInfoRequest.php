<?php

namespace App\Http\Requests\Api\Client;

use App\Http\Requests\FormRequest;

class UpdateBasicInfoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('client'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string|max:20',
            'middle_name' => 'nullable|string|max:20',
            'last_name' => 'required|string|max:20',
            'phone_number' => 'required|phone:AUTO,US',
            'phone_number_ext' => 'nullable|string|max:10',
            'street_address' => 'required|string|max:80',
            'city' => 'required|string|max:40',
            'state' => 'required|string|max:3',
            'postal_code' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ];
    }
}
