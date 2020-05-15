<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordRequest extends FormRequest
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
            'email' => 'required|email|exists:users,email',
            'token' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $passwordReset = DB::table('password_resets')
                        ->where('email', $this->get('email'))
                        ->first();

                    if ($passwordReset) {
                        if (!Hash::check($value, $passwordReset->token)) {
                            return $fail('Invalid password reset token.');
                        }
                    }
                }
            ],
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'email.exists' => 'Email does not exist.'
        ];
    }
}
