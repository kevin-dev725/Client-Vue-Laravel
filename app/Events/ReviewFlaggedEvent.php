<?php

namespace App\Events;

use App\Review;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReviewFlaggedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Review
     */
    public $review;

    /**
     * Create a new event instance.
     *
     * @param Review $review
     */
    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
