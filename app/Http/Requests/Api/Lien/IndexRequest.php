<?php

namespace App\Http\Requests\Api\Lien;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
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
            'owner' => 'nullable|string',
            'state' => 'nullable|string|exists:states,iso_3166_2',
            'county' => 'nullable|string',
        ];
    }
}
