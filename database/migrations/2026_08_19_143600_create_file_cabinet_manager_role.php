<?php

use IlBronza\AccountManager\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
	public function up() : void
	{
		Role::gpc()::create([
			'name' => 'fileCabinetManager',
			'guard_name' => 'web',
		]);
	}

	public function down() : void
	{
		Role::gpc()::where('name', 'fileCabinetManager')
			->where('guard_name', 'web')
			->firstOrFail()
			->delete();
	}
};
