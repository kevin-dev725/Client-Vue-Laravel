<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class FixIncorrectClientImportErrorMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('client_imports')
            ->whereJsonContains('errors', "The header record must be empty or a flat array with unique string values")
            ->update([
                'errors' => \GuzzleHttp\json_encode(['Error importing csv file. This is probably due to invalid csv template.'])
            ]);
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
