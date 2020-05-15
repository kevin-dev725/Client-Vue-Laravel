<?php

namespace Tests\Unit;

use App\FlaggedPhrase;
use App\Review;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function testFlaggedPhraseAttribute()
    {
        $flagged_phrase = factory(FlaggedPhrase::class)->create();
        $review = factory(Review::class)->states('complete')->create([
            'comment' => $flagged_phrase->phrase
        ]);
        $this->assertNotNull($review->flagged_phrase);
        $this->assertEquals($flagged_phrase->phrase, $review->flagged_phrase);
    }
}
