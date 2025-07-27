<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect(['staf tu', 'kasubbag up', 'sekretaris', 'kadis'])->each(function ($role) {
            Role::create(['name' => $role]);
        });
    }
}
