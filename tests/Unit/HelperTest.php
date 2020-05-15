<?php

namespace Tests\Unit;

use Tests\TestCase;

class HelperTest extends TestCase
{

    public function testStrStripNonAlphabets()
    {
        $this->assertFunctionExists('str_strip_non_alphabets');
        $this->assertEquals('FL', str_strip_non_alphabets('FL.>?>/,\'1234567890!@#$%^&*()_+'));
    }
}
