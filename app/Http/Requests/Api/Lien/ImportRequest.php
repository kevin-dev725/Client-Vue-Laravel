<?php

namespace App\Http\Requests\Api\Lien;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImportRequest extends FormRequest
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
            'state' => [
                'required',
                Rule::exists('states', 'iso_3166_2')
                    ->where('country_code', 'US')
            ],
            'county' => 'required|string|max:191',
            'file' => 'required|file|mimes:xls,xlsx',
        ];
    }
}
