<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@jentexchange.test'],
            ['name' => 'Admin User', 'password' => bcrypt('password123'), 'role' => User::ROLE_ADMIN]
        );

        // Entertainers
        User::updateOrCreate(
            ['email' => 'ent1@jentexchange.test'],
            ['name' => 'Entertainer One', 'password' => bcrypt('entpass1'), 'role' => User::ROLE_ENTERTAINER]
        );

        User::updateOrCreate(
            ['email' => 'ent2@jentexchange.test'],
            ['name' => 'Entertainer Two', 'password' => bcrypt('entpass2'), 'role' => User::ROLE_ENTERTAINER]
        );

        // Customers
        User::updateOrCreate(
            ['email' => 'cust1@jentexchange.test'],
            ['name' => 'Customer One', 'password' => bcrypt('custpass1'), 'role' => User::ROLE_CUSTOMER]
        );

        User::updateOrCreate(
            ['email' => 'cust2@jentexchange.test'],
            ['name' => 'Customer Two', 'password' => bcrypt('custpass2'), 'role' => User::ROLE_CUSTOMER]
        );
    }
}
