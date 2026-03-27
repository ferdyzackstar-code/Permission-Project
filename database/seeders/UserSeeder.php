<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Super Admin Ferdy',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'), // Silakan ganti passwordnya nanti
        ]);

        $admin->assignRole('admin');
    }
}