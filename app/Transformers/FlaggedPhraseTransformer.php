<?php

namespace App\Transformers;

use App\FlaggedPhrase;
use League\Fractal\TransformerAbstract;

class FlaggedPhraseTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param FlaggedPhrase $flaggedPhrase
     * @return array
     */
    public function transform(FlaggedPhrase $flaggedPhrase)
    {
        return $flaggedPhrase->attributesToArray();
    }
}
