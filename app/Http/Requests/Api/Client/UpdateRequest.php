<?php

namespace App\Http\Requests\Api\Client;

use App\Client;
use App\Http\Requests\FormRequest;
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
        return $this->user()
            ->can('update', $this->route('client'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'basic_info' => 'nullable|boolean',
            'client_type' => [
                'required',
                Rule::in([Client::CLIENT_TYPE_INDIVIDUAL, Client::CLIENT_TYPE_ORGANIZATION]),
            ],
            'organization_name' => [
                'required_if:client_type,==,' . Client::CLIENT_TYPE_ORGANIZATION,
                'nullable',
                'string',
                'max:50',
            ],
            'first_name' => 'required|string|max:191',
            'middle_name' => 'nullable|string|max:191',
            'last_name' => 'required|string|max:191',
            'phone_number' => 'required|phone:AUTO,US',
            'phone_number_ext' => 'nullable|string|max:10',
            'alt_phone_number' => 'nullable|phone:AUTO,US',
            'alt_phone_number_ext' => 'nullable|string|max:10',
            'street_address' => 'required|string|max:80',
            'street_address2' => 'nullable|string|max:80',
            'city' => 'required|string|max:40',
            'state' => 'required|string|max:3',
            'postal_code' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:191',
            'billing_first_name' => [
                'nullable',
                'required_if:client_type,==,' . Client::CLIENT_TYPE_ORGANIZATION,
                'string',
                'max:191',
            ],
            'billing_middle_name' => 'nullable|string|max:191',
            'billing_last_name' => [
                'nullable',
                'required_if:client_type,==,' . Client::CLIENT_TYPE_ORGANIZATION,
                'string',
                'max:191',
            ],
            'billing_phone_number' => [
                'nullable',
                'required_if:client_type,==,' . Client::CLIENT_TYPE_ORGANIZATION,
                'phone:AUTO,US',
            ],
            'billing_phone_number_ext' => 'nullable|string|max:10',
            'billing_street_address' => [
                'nullable',
                'required_if:client_type,==,' . Client::CLIENT_TYPE_ORGANIZATION,
                'string',
                'max:80',
            ],
            'billing_street_address2' => 'nullable|string|max:80',
            'billing_city' => [
                'nullable',
                'required_if:client_type,==,' . Client::CLIENT_TYPE_ORGANIZATION,
                'string',
                'max:40',
            ],
            'billing_state' => [
                'nullable',
                'required_if:client_type,==,' . Client::CLIENT_TYPE_ORGANIZATION,
                'string',
                'max:3',
            ],
            'billing_postal_code' => 'nullable|string|max:20',
            'billing_email' => 'nullable|email|max:191',
            'initial_star_rating' => 'nullable|numeric|min:1|max:5',
            'country_id' => 'nullable|exists:countries,id'
        ];

        if ($this->basicInfoOnly()) {
            $rules = array_only($rules, [
                'first_name',
                'middle_name',
                'last_name',
                'phone_number',
                'phone_number_ext',
                'street_address',
                'city',
                'state',
                'postal_code',
                'email',
                'organization_name'
            ]);
        }

        return $rules;
    }

    public function basicInfoOnly()
    {
        return $this->getBoolean('basic_info');
    }
}
