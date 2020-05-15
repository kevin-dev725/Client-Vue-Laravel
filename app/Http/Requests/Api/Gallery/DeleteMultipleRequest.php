<?php

namespace App\Http\Requests\Api\Gallery;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteMultipleRequest extends FormRequest
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
            'id' => 'required|array',
            'id.*' => [
                'required',
                Rule::exists('media', 'id')
                    ->where('model_type', User::class)
                    ->where('collection_name', User::MEDIA_COLLECTION_GALLERY)
            ],
        ];
    }
}
