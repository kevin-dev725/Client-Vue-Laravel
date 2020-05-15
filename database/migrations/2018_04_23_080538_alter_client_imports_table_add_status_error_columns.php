<?php

use App\ClientImport;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClientImportsTableAddStatusErrorColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_imports', function (Blueprint $table) {
            $table->string('status', 20)->default(ClientImport::STATUS_PENDING);
            $table->json('errors')->nullable();
            $table->json('invalid_row')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_imports', function (Blueprint $table) {
            $table->dropColumn(['errors', 'status', 'invalid_row']);
        });
    }
}
