<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder {

    public function run(): void {
        $roles = config('site.users.roles');

        $roleData = array_map(fn($role) => ['name' => $role], $roles);

        Role::insert($roleData);
    }
}
