<?php

namespace Database\Seeders;

use App\Models\SettingApp;
use Illuminate\Database\Seeder;

class SettingAppSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Identitas & tampilan
            ['setting_key' => 'app_name',      'setting_value' => 'Anda Petshop'],
            ['setting_key' => 'app_image',     'setting_value' => null], // 1 foto untuk semua

            // Auth panel kanan (login)
            ['setting_key' => 'auth_title_login',    'setting_value' => 'Selamat Datang Kembali!'],
            ['setting_key' => 'auth_subtitle_login',  'setting_value' => 'Masukkan detail pribadi Anda untuk menggunakan semua fitur situs'],

            // Auth panel kanan (register)
            ['setting_key' => 'auth_title_register',   'setting_value' => 'Halo, Kawan!'],
            ['setting_key' => 'auth_subtitle_register', 'setting_value' => 'Daftarkan detail pribadi Anda untuk menggunakan semua fitur situs'],

            // Informasi toko
            ['setting_key' => 'store_address', 'setting_value' => null],
            ['setting_key' => 'store_phone',   'setting_value' => null],
        ];

        foreach ($settings as $s) {
            SettingApp::updateOrCreate(
                ['setting_key' => $s['setting_key']],
                ['setting_value' => $s['setting_value']]
            );
        }

        $this->command->info('Setting apps berhasil di-seed!');
    }
}