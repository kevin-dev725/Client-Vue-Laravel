<?php

namespace App\Jobs;

use App\User;
use Geocode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GeocodeUserAddress implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * Create a new job instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $coordinates = Geocode::getCoordinatesForAddress($this->user->full_address);
        if (!$coordinates) {
            return;
        }
        $this->user->location()
            ->updateOrCreate([], [
                'lat' => $coordinates->getLat(),
                'lng' => $coordinates->getLng(),
            ]);
    }
}
