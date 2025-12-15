<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $email = 'yehudaj@gmail.com';
        $user = User::where('email', $email)->first();
        if (! $user) {
            User::create([
                'name' => 'Yehudah',
                'email' => $email,
                'password' => 'password', // will be hashed by the model cast
                'roles' => [User::ROLE_ADMIN],
            ]);
        } else {
            $user->roles = [User::ROLE_ADMIN];
            $user->save();
        }
    }
}
