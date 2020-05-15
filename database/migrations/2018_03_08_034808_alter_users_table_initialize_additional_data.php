<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTableInitializeAdditionalData extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function (Blueprint $table)
		{
			$table->string('first_name')->nullable();
			$table->string('middle_name')->nullable();
			$table->string('last_name')->nullable();
			$table->enum('account_type', ['individual', 'company'])->default('individual')->nullable()->index();
			$table->string('company_name')->nullable();
			$table->text('description')->nullable();
			$table->string('phone_number')->nullable();
			$table->string('phone_number_ext')->nullable();
			$table->string('alt_phone_number')->nullable();
			$table->string('alt_phone_number_ext')->nullable();
			$table->string('street_address')->nullable();
			$table->string('street_address2')->nullable();
			$table->string('city')->nullable();
			$table->string('state')->nullable();
			$table->string('postal_code')->nullable();
			$table->string('business_url')->nullable();
			$table->string('facebook_url')->nullable();
			$table->string('twitter_url')->nullable();
			$table->date('expiry_date')->nullable();
			$table->enum('account_status', ['active', 'suspended', 'expired', 'cancelled'])->default('active')->nullable()->index();
			$table->dateTimeTz('last_app_signin')->nullable();
			$table->dateTimeTz('last_site_signin')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function (Blueprint $table)
		{
			$table->dropColumn([
				'first_name',
				'middle_name',
				'last_name',
				'account_type',
				'company_name',
				'description',
				'phone_number',
				'phone_number_ext',
				'alt_phone_number',
				'alt_phone_number_ext',
				'street_address',
				'street_address2',
				'city',
				'state',
				'postal_code',
				'business_url',
				'facebook_url',
				'twitter_url',
				'expiry_date',
				'account_status',
				'last_app_signin',
				'last_site_signin',
			]);
		});
	}
}
