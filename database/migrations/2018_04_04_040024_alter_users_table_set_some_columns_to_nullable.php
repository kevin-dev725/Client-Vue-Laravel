<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTableSetSomeColumnsToNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $columns = ['first_name', 'last_name', 'name', 'password', 'phone_number', 'street_address', 'city', 'state', 'postal_code'];
        foreach ($columns as $column) {
            DB::statement('ALTER TABLE users MODIFY ' . $column . ' VARCHAR(' . \Illuminate\Database\Schema\Builder::$defaultStringLength . ') NULL');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
