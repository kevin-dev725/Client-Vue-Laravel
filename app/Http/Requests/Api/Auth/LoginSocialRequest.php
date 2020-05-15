<?php

namespace App\Http\Requests\Api\Auth;

use App\User;
use Illuminate\Foundation\Http\FormRequest;

class LoginSocialRequest extends FormRequest
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
            'provider' => 'required|string|in:' . implode(',', [User::SOCIAL_PROVIDER_FACEBOOK, User::SOCIAL_PROVIDER_GOOGLE]),
            'access_token' => 'required|string',
        ];
    }

    /**
     * @return string
     */
    public function provider()
    {
        return $this->get('provider');
    }

    /**
     * @return string
     */
    public function access_token()
    {
        return $this->get('access_token');
    }
}
