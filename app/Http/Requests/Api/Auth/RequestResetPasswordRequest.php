<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RequestResetPasswordRequest extends FormRequest
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
        // return [
        //     'email' => [
        //         'required',
        //         Rule::exists('users')->where(function ($query) {
        //             return $query->where('social_provider', null);
        //         })
        //     ]
        // ];
        return [
            'email' => [ 'required' ]
        ];
    }

    public function messages()
    {
        return [
            'exists' => 'Email does not exist.'
        ];
    }
}
