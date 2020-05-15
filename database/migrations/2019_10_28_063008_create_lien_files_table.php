<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLienFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('lien_files');
        Schema::create('lien_files', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lien_id');
            $table->foreign('lien_id')->references('id')->on('liens')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lien_files');
    }
}
