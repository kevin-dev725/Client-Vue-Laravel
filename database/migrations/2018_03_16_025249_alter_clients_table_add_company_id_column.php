<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClientsTableAddCompanyIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::table('clients', function (Blueprint $table) {
			$table->dropColumn('client_type');
		});
        Schema::table('clients', function (Blueprint $table) {
			$table->string('client_type');
			$table->unsignedInteger('user_id')->nullable()->change();
            $table->unsignedInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
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
			$table->unsignedInteger('user_id')->nullable(false)->change();
			$table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
			$table->dropColumn('client_type');
		});
		Schema::table('clients', function (Blueprint $table) {
			$table->enum('client_type', ['individual', 'organization']);
		});
    }
}
