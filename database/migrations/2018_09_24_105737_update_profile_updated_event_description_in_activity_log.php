<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProfileUpdatedEventDescriptionInActivityLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * @var \Spatie\Activitylog\Models\Activity$log
         */
        foreach (\Spatie\Activitylog\Models\Activity::query()->where('subject_type', \App\User::class)->where('description', 'like', '%profile were updated%')->cursor() as $log) {
            $log->description = str_replace('profile were updated', 'profile was updated', $log->description);
            $log->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
