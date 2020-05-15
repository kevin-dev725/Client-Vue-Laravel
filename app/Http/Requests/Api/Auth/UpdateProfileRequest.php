<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\FormRequest;
use App\User;

class UpdateProfileRequest extends FormRequest
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
            'first_name' => 'sometimes|required|string|max:20',
            'middle_name' => 'nullable|string|max:20',
            'last_name' => 'sometimes|required|string|max:20',
            /*'account_type' => [
                'required',
                Rule::in([User::ACCOUNT_TYPE_INDIVIDUAL, User::ACCOUNT_TYPE_COMPANY]),
            ],*/
            'company_name' => [
                'nullable',
                'required_if:account_type,==,' . User::ACCOUNT_TYPE_COMPANY,
                'string',
                'max:40',
            ],
            'description' => 'nullable|string|max:300',
            'email' => 'sometimes|required|string|email|unique:users,email,' . $this->user()->id,
            'phone_number' => 'sometimes|required|phone:AUTO,US',
            'phone_number_ext' => 'nullable|string|max:10',
            'alt_phone_number' => 'nullable|phone:AUTO,US',
            'alt_phone_number_ext' => 'nullable|string|max:10',
            'city' => 'sometimes|required|string|max:40',
            'state' => 'sometimes|required|string|max:3',
            'business_url' => 'nullable|url',
            'facebook_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'country_id' => 'nullable|exists:countries,id',
            'overview' => 'nullable|string|max:65535',
            'license_number' => 'nullable|string|max:255',
            'is_insured' => 'nullable|boolean',
            'skills' => 'nullable|string|max:65535',
        ];
    }
}
