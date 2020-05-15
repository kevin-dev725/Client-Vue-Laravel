<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\FormRequest;
use App\Rules\StripeCouponCodeRule;
use App\User;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
        $rules = [
            'account_type' => [
                'required',
                Rule::in([User::ACCOUNT_TYPE_INDIVIDUAL, User::ACCOUNT_TYPE_COMPANY]),
            ],
            'company_id' => [
                'nullable',
                'required_if:account_type,==,' . User::ACCOUNT_TYPE_COMPANY,
                'exists:companies,id',
            ],
            'company_name' => [
                'nullable',
                'required_if:account_type,==,' . User::ACCOUNT_TYPE_COMPANY,
                'exists:companies,name',
                'string',
                'max:40',
            ],
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'first_name' => 'required|string|max:20',
            'middle_name' => 'nullable|string|max:20',
            'last_name' => 'required|string|max:20',
            'phone_number' => 'required|phone:AUTO,US',
            'phone_number_ext' => 'nullable|string|max:10',
            'alt_phone_number' => 'nullable|phone:AUTO,US',
            'alt_phone_number_ext' => 'nullable|string|max:10',
            'street_address' => 'required|string|max:80',
            'street_address2' => 'nullable|string|max:80',
            'city' => 'required|string|max:40',
            'state' => 'required|string|max:3',
            'postal_code' => 'required|string|max:20',
            'country_id' => 'nullable|exists:countries,id',
        ];
        if (!config('settings.free_account_on_register_enabled')) {
            $rules = array_merge($rules, [
                'card_token' => 'required|string|max:255',
                'country_id' => 'nullable|exists:countries,id',
                'plan_interval' => 'required|string|in:monthly,yearly',
                'coupon_code' => [
                    'nullable',
                    'string',
                    new StripeCouponCodeRule(),
                ]
            ]);
        }
        return $rules;
    }
}
