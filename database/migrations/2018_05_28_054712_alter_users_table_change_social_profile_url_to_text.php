<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTableChangeSocialProfileUrlToText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('alter table users MODIFY social_profile_url TEXT DEFAULT NULL');
        DB::statement('alter table users MODIFY facebook_url TEXT DEFAULT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('alter table users MODIFY social_profile_url VARCHAR(191) DEFAULT NULL');
        DB::statement('alter table users MODIFY facebook_url VARCHAR(191) DEFAULT NULL');
    }
}
