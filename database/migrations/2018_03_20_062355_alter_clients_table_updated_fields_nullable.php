<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClientsTableUpdatedFieldsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
			$table->string('phone_number')->nullable()->change();
			$table->string('street_address')->nullable()->change();
			$table->string('city')->nullable()->change();
			$table->string('state')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
			$table->string('phone_number')->nullable(false)->change();
			$table->string('street_address')->nullable(false)->change();
			$table->string('city')->nullable(false)->change();
			$table->string('state')->nullable(false)->change();
        });
    }
}
