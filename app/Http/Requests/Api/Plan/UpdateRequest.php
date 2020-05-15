<?php

namespace App\Http\Requests\Api\Plan;

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
        return $this->user()->can('update', $this->route('plan'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($this->route('plan')->id),
            ],
            'price' => 'required|numeric|min:0',
            'stripe_id' => 'nullable|string|max:255',
        ];
    }
}
