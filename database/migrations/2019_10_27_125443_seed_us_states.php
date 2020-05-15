<?php

use Illuminate\Database\Migrations\Migration;

class SeedUsStates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('db:seed', [
            '--class' => 'StatesSeeder',
            '--force' => true,
            '-n' => true,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('states')
            ->delete();
    }
}
