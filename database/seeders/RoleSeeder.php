<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['ADMIN', 'ENTERTAINER', 'CUSTOMER'];
        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r]);
        }
    }
}
