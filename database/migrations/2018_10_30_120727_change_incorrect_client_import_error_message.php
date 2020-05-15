<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeIncorrectClientImportErrorMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('client_imports')
            ->whereJsonContains('errors', "Error importing csv file. This is probably due to invalid csv template.")
            ->update([
                'errors' => \GuzzleHttp\json_encode(['Error importing CSV. This is probably due to invalid names in the header row.'])
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
