<?php

namespace App\Http\Requests\Api\Review;

use App\Http\Requests\FormRequest;
use App\Review;
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
        return $this->user()->can('update', $this->route('review'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'service_date' => 'required|date|before_or_equal:' . now()->toDateString(),
            'star_rating' => 'required|numeric|min:1|max:5',
            'payment_rating' => [
                'nullable',
                Rule::in([
                    Review::REVIEW_RATING_NO_OPINION,
                    Review::REVIEW_RATING_THUMBS_DOWN,
                    Review::REVIEW_RATING_THUMBS_UP,
                ]),
            ],
            'character_rating' => [
                'nullable',
                Rule::in([
                    Review::REVIEW_RATING_NO_OPINION,
                    Review::REVIEW_RATING_THUMBS_DOWN,
                    Review::REVIEW_RATING_THUMBS_UP,
                ]),
            ],
            'repeat_rating' => [
                'nullable',
                Rule::in([
                    Review::REVIEW_RATING_NO_OPINION,
                    Review::REVIEW_RATING_THUMBS_DOWN,
                    Review::REVIEW_RATING_THUMBS_UP,
                ]),
            ],
            'comment' => 'nullable|string|max:300',
        ];
    }
}
