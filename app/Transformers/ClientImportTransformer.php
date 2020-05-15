<?php

namespace App\Transformers;

use App\ClientImport;
use League\Fractal\TransformerAbstract;

class ClientImportTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param ClientImport $clientImport
     * @return array
     */
    public function transform(ClientImport $clientImport)
    {
        return $clientImport->attributesToArray();
    }
}
