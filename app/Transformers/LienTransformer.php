<?php

namespace App\Transformers;

use App\Lien;
use League\Fractal\TransformerAbstract;

class LienTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param Lien $lien
     * @return array
     */
    public function transform(Lien $lien)
    {
        return $lien->attributesToArray();
    }
}
