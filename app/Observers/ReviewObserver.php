<?php

namespace App\Observers;

use App\Review;

class ReviewObserver
{
    public function created(Review $review)
    {
        $review->flagIfHasFlaggedPhrases();
    }
}
