<?php

namespace App\Http\Requests\Api\Plan;

use App\Http\Requests\FormRequest;
use App\Plan;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Plan::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:plans',
            'price' => 'required|numeric|min:0',
            'stripe_id' => 'nullable|string|max:255',
        ];
    }
}
