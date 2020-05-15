<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhoneCleanFunction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec("CREATE FUNCTION `phone_clean`(
	`phone_number` VARCHAR(50)
)
RETURNS VARCHAR(100)
LANGUAGE SQL
NOT DETERMINISTIC
CONTAINS SQL
SQL SECURITY DEFINER
COMMENT ''
BEGIN RETURN
REPLACE(
REPLACE(
REPLACE(
REPLACE(
REPLACE(phone_number, '+', ''), ' ', ''), '(', ''), ')', ''), '-', ''); END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getPdo()->exec('DROP FUNCTION `phone_clean`;');
    }
}
