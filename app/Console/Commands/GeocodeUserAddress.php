<?php

namespace App\Console\Commands;

use App\Jobs\GeocodeUserAddress as GeocodeUserAddressJob;
use App\Role;
use App\User;
use Exception;
use Illuminate\Console\Command;

class GeocodeUserAddress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clientDomain:geocode-user-address';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Geocode user addresses.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::where('role_id', Role::ROLE_USER)
            ->whereNotNull('street_address')
            ->cursor();
        /** @var User $user */
        foreach ($users as $user) {
            try {
                dispatch_now(new GeocodeUserAddressJob($user));
            } catch (Exception $e) {
                report($e);
            }
        }
    }
}
