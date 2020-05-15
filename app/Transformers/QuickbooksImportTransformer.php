<?php

namespace App\Transformers;

use App\QuickbooksImport;
use League\Fractal\TransformerAbstract;

class QuickbooksImportTransformer extends TransformerAbstract
{
    public function transform(QuickbooksImport $import)
    {
        return $import->attributesToArray();
    }
}