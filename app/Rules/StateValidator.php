<?php

namespace App\Rules;

use App\Services\States;
use Illuminate\Contracts\Validation\Rule;

class StateValidator implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return in_array(strtoupper(States::stripNonAlphabets($value)), States::validValues());
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'State name is unrecognized.';
    }
}
