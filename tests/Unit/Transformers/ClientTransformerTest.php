<?php

namespace Tests\Unit\Transformers;

use App\Client;
use App\Transformers\ClientTransformer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use League\Fractal\Resource\Item;
use Tests\TestCase;

class ClientTransformerTest extends TestCase
{
    use DatabaseTransactions;

    public function testIncludesUser()
    {
        $client = factory(Client::class)->states('complete')->create();
        $this->assertHasTransformerIncludes(
            new ClientTransformer(),
            $client,
            ['user'],
            [Item::class],
            true
        );
    }
}
