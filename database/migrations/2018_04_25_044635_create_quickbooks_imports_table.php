<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuickbooksImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quickbooks_imports', function (Blueprint $table) {
            $table->increments('id');
            foreign($table, 'user_id', 'id', 'users', 'cascade');
            $table->string('path');
            $table->string('status');
            $table->json('errors')->nullable();
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
        Schema::dropIfExists('quickbooks_imports');
    }
}
