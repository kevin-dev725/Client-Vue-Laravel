<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeIndexLiens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('liens', function (Blueprint $table) {
            //
            $table->string("owner", 255)->change();
            $table->dropUnique(array('instrument_id', 'site','state','county', 'lienor'));
            $table->unique(array('instrument_id', 'site','state','county', 'owner'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('liens', function (Blueprint $table) {
            //
        });
    }
}
