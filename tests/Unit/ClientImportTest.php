<?php

namespace Tests\Unit;

use App\ClientImport;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ClientImportTest extends TestCase
{
    use DatabaseTransactions;

    public function testHiddenAttributes()
    {
        /** @var ClientImport $client_import */
        $client_import = factory(ClientImport::class)->state('error')->create();
        $this->assertArrayNotHasKey('exception', $client_import->attributesToArray());
    }
}
