<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin BrewNest',
            'email' => 'admin@brewnest.com',
            'phone' => '081200000001',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Bos BrewNest',
            'email' => 'bos@brewnest.com',
            'phone' => '081200000002',
            'password' => Hash::make('password123'),
            'role' => 'bos',
        ]);

        User::create([
            'name' => 'IT BrewNest',
            'email' => 'it@brewnest.com',
            'phone' => '081200000003',
            'password' => Hash::make('password123'),
            'role' => 'it',
        ]);
    }
}