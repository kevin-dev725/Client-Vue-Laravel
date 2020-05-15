<?php

namespace App\Http\Requests\Api\User;

use App\Company;
use App\Http\Requests\FormRequest;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('user'));
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
            'account_type' => [
                'required',
                Rule::in([User::ACCOUNT_TYPE_INDIVIDUAL, User::ACCOUNT_TYPE_COMPANY]),
            ],
            'company_id' => [
                'nullable',
                'required_if:account_type,==,' . User::ACCOUNT_TYPE_COMPANY,
                'exists:companies,id'
            ],
            'company_name' => [
                'nullable',
                'required_if:account_type,==,' . User::ACCOUNT_TYPE_COMPANY,
                'exists:companies,name',
                'string',
                'max:40',
            ],
            'description' => 'nullable|string|max:300',
            'phone_number' => 'required|phone:AUTO,US',
            'phone_number_ext' => 'nullable|string|max:10',
            'alt_phone_number' => 'nullable|phone:AUTO,US',
            'alt_phone_number_ext' => 'nullable|string|max:10',
            'street_address' => 'required|string|max:80',
            'street_address2' => 'nullable|string|max:80',
            'city' => 'required|string|max:40',
            'state' => 'required|string|max:3',
            'postal_code' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'business_url' => 'nullable|url|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'account_status' => [
                'required',
                Rule::in([
                    User::ACCOUNT_STATUS_ACTIVE,
                    User::ACCOUNT_STATUS_CANCELLED,
                    User::ACCOUNT_STATUS_EXPIRED,
                    User::ACCOUNT_STATUS_SUSPENDED,
                ]),
            ],
            'password' => 'nullable|string',
        ];
    }

    /**
     * @return Collection|Model|null|static|static[]
     */
    public function company()
    {
        if ($this->get('account_type') === User::ACCOUNT_TYPE_COMPANY) {
            return Company::query()->find($this->get('company_id'));
        }
        return null;
    }
}
