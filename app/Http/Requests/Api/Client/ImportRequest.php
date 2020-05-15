<?php

namespace App\Http\Requests\Api\Client;

use App\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ImportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('import', Client::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file' => 'required|file|mimes:csv,txt'
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function (Validator $validator) {
            if (!in_array($this->file('file')->getClientOriginalExtension(), config('settings.import_allowed_extensions'))) {
                $validator->errors()
                    ->add('file', 'File is invalid, Please import only CSV file.');
            }
        });
    }
}
