<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOnTrialUsersToFreeForever extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * @var \App\User $user
         */
        foreach (\App\User::query()->whereNotNull('trial_ends_at')
                     ->where('trial_ends_at', '>', now()->toDateTimeString())
                     ->cursor() as $user) {
            if ($user->is_on_trial) {
                $user->update([
                    'trial_ends_at' => null,
                    'is_free_account' => true,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /**
         * @var \App\User $user
         */
        foreach (\App\User::query()->where('is_free_account', true)
                     ->cursor() as $user) {
            $user->update([
                'trial_ends_at' => now()->addDays(config('settings.subscription.trial_days')),
                'is_free_account' => false,
            ]);
        }
    }
}
