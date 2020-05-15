<?php

use App\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PopulateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Role::query()
			->create([
        		'id' => Role::ROLE_ADMIN,
				'name' => 'Admin'
			]);

        Role::query()
			->create([
				'id' => Role::ROLE_USER,
				'name' => 'User'
			]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Role::query()
			->whereIn('id', [Role::ROLE_ADMIN, Role::ROLE_USER])
			->delete();
    }
}
