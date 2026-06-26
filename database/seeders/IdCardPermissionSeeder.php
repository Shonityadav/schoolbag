<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class IdCardPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'idcard.view', 'guard_name' => 'web'],
            ['name' => 'idcard.edit', 'guard_name' => 'web'],
            ['name' => 'idcard.print', 'guard_name' => 'web'],
            ['name' => 'idcard.bulk_print', 'guard_name' => 'web'],
            ['name' => 'idcard.settings', 'guard_name' => 'web']
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }
    }
}
