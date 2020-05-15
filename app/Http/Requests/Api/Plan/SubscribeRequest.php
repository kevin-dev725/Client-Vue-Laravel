<?php

namespace App\Http\Requests\Api\Plan;

use App\Http\Requests\FormRequest;

class SubscribeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('subscribe', $this->route('plan'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'card_token' => 'required|string|max:255'
        ];
    }
}
