<?php

namespace App\Http\Requests\Api\License;

use App\License;
use Illuminate\Foundation\Http\FormRequest;
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
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /** @var License $license */
        $license = $this->route('license');
        return [
            'name' => 'nullable|string|max:255',
            'number' => 'sometimes|required|string|max:255',
            'expiration' => 'nullable|date',
            'is_insured' => 'boolean',
            'photos' => 'array',
            'photos.*.id' => [
                'nullable',
                'required_without:photos.*.photo',
                Rule::exists('media', 'id')
                    ->where('model_type', License::class)
                    ->where('model_id', $license->id)
                    ->where('collection_name', License::MEDIA_COLLECTION_PHOTOS)
            ],
            'photos.*.photo' => 'nullable|required_without:photos.*.id|image',
            'certs' => 'array',
            'certs.*.id' => [
                'nullable',
                'required_without:certs.*.photo',
                Rule::exists('media', 'id')
                    ->where('model_type', License::class)
                    ->where('model_id', $license->id)
                    ->where('collection_name', License::MEDIA_COLLECTION_CERTS)
            ],
            'certs.*.photo' => 'nullable|required_without:certs.*.id|image',
            'clear_photos' => 'boolean',
            'clear_certs' => 'boolean',
        ];
    }
}
