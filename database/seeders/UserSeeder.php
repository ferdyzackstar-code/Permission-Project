<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Admin ────────────────────────────────────────────────────────
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'image' => 'default-user.jpg',
            'bio' => 'Admin of Anda Petshop.',
        ]);
        $admin->assignRole('Admin');

        $ferdy = User::create([
            'name' => 'Farel Ferdyawan',
            'email' => 'ferdyganteng@gmail.com',
            'password' => Hash::make('password'),
            'image' => 'default-user.jpg',
            'bio' => 'Lead Developer of Anda Petshop & Ferdy Blog.',
        ]);
        $ferdy->assignRole('Admin');

        // ─── Kasir ────────────────────────────────────────────────────────
        $kasirSatu = User::create([
            'name' => 'Sir Pai',
            'email' => 'sirpai@gmail.com',
            'password' => Hash::make('password'),
            'image' => 'default-user.jpg',
            'bio' => 'Cashier of Anda Petshop.',
        ]);
        $kasirSatu->assignRole('Kasir');

        $kasirDua = User::create([
            'name' => 'Sir Alex',
            'email' => 'siralex@gmail.com',
            'password' => Hash::make('password'),
            'image' => 'default-user.jpg',
            'bio' => 'Cashier of Anda Petshop.',
        ]);
        $kasirDua->assignRole('Kasir');

        $kasirTiga = User::create([
            'name' => 'Sir Ferguson',
            'email' => 'sirferguson@gmail.com',
            'password' => Hash::make('password'),
            'image' => 'default-user.jpg',
            'bio' => 'Cashier of Anda Petshop.',
        ]);
        $kasirTiga->assignRole('Kasir');

        // ─── User ─────────────────────────────────────────────────────────
        $user = User::create([
            'name' => 'Audrel Qiano M.H.',
            'email' => 'audrel@gmail.com',
            'password' => Hash::make('password'),
            'image' => 'default-user.jpg',
            'bio' => 'Employee of Anda Petshop.',
        ]);
        $user->assignRole('User');

        $this->command->info('✅ UserSeeder berhasil di-seed!');
        $this->command->table(['Nama', 'Email', 'Role'], [['Admin', 'admin@gmail.com', 'Admin'], ['Farel Ferdyawan', 'ferdyganteng@gmail.com', 'Admin'], ['Sir Pai', 'sirpai@gmail.com', 'Kasir'], ['Sir Alex', 'siralex@gmail.com', 'Kasir'], ['Sir Ferguson', 'sirferguson@gmail.com', 'Kasir'], ['Audrel Qiano', 'audrel@gmail.com', 'User']]);
    }
}
