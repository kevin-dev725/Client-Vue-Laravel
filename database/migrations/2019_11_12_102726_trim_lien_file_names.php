<?php

use Illuminate\Database\Migrations\Migration;

class TrimLienFileNames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        transaction(function () {
            DB::table('lien_files')
                ->whereRaw('trim(file_name) = ?', [''])
                ->orWhereNull('file_name')
                ->delete();
            DB::update('update lien_files set file_name = trim(file_name)');
        });

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
