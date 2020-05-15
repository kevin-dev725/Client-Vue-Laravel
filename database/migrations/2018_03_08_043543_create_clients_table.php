<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            foreign($table, 'user_id', 'id', 'users', 'cascade');
            $table->enum('client_type', ['individual', 'organization'])->default('individual')->index();
            $table->string('organization_name')->nullable();
            $table->string('name')->index();
			$table->string('first_name');
			$table->string('middle_name')->nullable();
			$table->string('last_name');
			$table->string('phone_number')->index();
			$table->string('phone_number_ext')->nullable();
			$table->string('alt_phone_number')->nullable();
			$table->string('alt_phone_number_ext')->nullable();
			$table->string('street_address')->index();
			$table->string('street_address2')->nullable();
			$table->string('city')->index();
			$table->string('state')->index();
			$table->string('postal_code')->nullable();
			$table->string('email')->nullable()->index();
			$table->string('billing_first_name')->nullable();
			$table->string('billing_middle_name')->nullable();
			$table->string('billing_last_name')->nullable();
			$table->string('billing_phone_number')->nullable();
			$table->string('billing_phone_number_ext')->nullable();
			$table->string('billing_street_address')->nullable();
			$table->string('billing_street_address2')->nullable();
			$table->string('billing_city')->nullable();
			$table->string('billing_state')->nullable();
			$table->string('billing_postal_code')->nullable();
			$table->string('billing_email')->nullable();
			$table->unsignedTinyInteger('initial_star_rating')->nullable();
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
        Schema::dropIfExists('clients');
    }
}
