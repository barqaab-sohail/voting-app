<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Sohail Afzal',
            'email' => 'sohail@example.com',
            'phone' => '1234567890',
            'is_admin' => true,
            'is_judge_eligible' => true,
            'password' => Hash::make('password'),
        ]);

        // Add other members
        $members = [
            ['name' => 'Hashim Khan', 'email' => 'hashim@example.com', 'phone' => '1234567891', 'is_judge_eligible' => true],
            ['name' => 'Muhammad Taufeeq', 'email' => 'taufeeq@example.com', 'phone' => '1234567892', 'is_judge_eligible' => true],
            // Add all other members similarly
        ];

        foreach ($members as $member) {
            User::create(array_merge($member, [
                'is_admin' => false,
                'password' => Hash::make('password'),
            ]));
        }
    }
}
