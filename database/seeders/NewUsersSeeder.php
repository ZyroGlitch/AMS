<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NewUsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Michael Santos',
                'email' => 'michael@gmail.com',
            ],
            [
                'name' => 'Sarah Reyes',
                'email' => 'sarah@gmail.com',
            ],
            [
                'name' => 'James Dela Cruz',
                'email' => 'james@gmail.com',
            ],
            [
                'name' => 'Maria Garcia',
                'email' => 'maria@gmail.com',
            ],
            [
                'name' => 'Daniel Mendoza',
                'email' => 'daniel@gmail.com',
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    // 'role' => $user['role'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('12345678'),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
