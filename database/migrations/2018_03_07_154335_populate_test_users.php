<?php

use App\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class PopulateTestUsers extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	    config([
	        'activitylog.enabled' => false
        ]);
        DB::table('users')
            ->insert([
                'name' => 'System Administrator',
                'email' => 'admin@user.com',
                'password' => bcrypt(123456),
                'role_id' => Role::ROLE_ADMIN,
            ]);
        DB::table('users')
            ->insert([
                'name' => 'User',
                'email' => 'test@user.com',
                'password' => bcrypt(123456),
                'role_id' => Role::ROLE_USER,
            ]);
        config([
            'activitylog.enabled' => true
        ]);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::table('users')->truncate();
	}
}
