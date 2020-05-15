<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTableAddSocialColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
			$table->text('password_raw')->nullable();
			$table->string('avatar_path')->nullable();
			$table->string('social_provider')->nullable();
			$table->string('social_id')->nullable();
			$table->string('social_profile_url')->nullable();
			$table->string('social_image_url')->nullable();
			$table->text('social_token')->nullable();
			$table->text('social_token_secret')->nullable();
			$table->text('social_refresh_token')->nullable();
			$table->string('social_expires_in')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
			$table->dropColumn(['password_raw', 'avatar_path', 'social_provider', 'social_id', 'social_profile_url', 'social_image_url', 'social_token', 'social_token_secret', 'social_refresh_token', 'social_expires_in']);
        });
    }
}
