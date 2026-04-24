<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User::create([
        //     'name' => 'Admin',
        //     'email' => 'admin@gmail.com',
        //     'password' => Hash::make('password'),
        //     'role' => 'admin',
        // ]);

        User::create([
            'name' => 'Remaja',
            'email' => 'remaja@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'remaja',
        ]);

        User::create([
            'name' => 'Ibu',
            'email' => 'ibu@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'ibu',
        ]);
    }
}
