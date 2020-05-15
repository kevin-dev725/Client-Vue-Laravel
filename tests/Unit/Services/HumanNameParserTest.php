<?php

namespace Tests\Unit\Services;

use App\Services\HumanNameParser;
use Tests\TestCase;

class HumanNameParserTest extends TestCase
{

    public function testParseFullName()
    {
        $parser = new HumanNameParser();
        $this->assertEquals('William', $parser->splitFullName('William Gates')['first_name']);
        $this->assertEquals('Gates', $parser->splitFullName('William Gates')['last_name']);
    }
}
