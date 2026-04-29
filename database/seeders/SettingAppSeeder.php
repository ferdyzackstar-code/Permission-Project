<?php

namespace Database\Seeders;

use App\Models\SettingApp;
use Illuminate\Database\Seeder;

class SettingAppSeeder extends Seeder
{
    public function run(): void
    {   
        $settings = [
            // ─── Identitas & Tampilan ──────────────────────────────────────
            ['setting_key' => 'app_name', 'setting_value' => 'Anda Petshop'],
            ['setting_key' => 'app_image', 'setting_value' => 'uploads/settings/default-logo.jpg'],

            // ─── Auth Panel — Login ────────────────────────────────────────
            ['setting_key' => 'auth_title_login', 'setting_value' => 'Selamat Datang Kembali!'],
            ['setting_key' => 'auth_subtitle_login', 'setting_value' => 'Masukkan detail pribadi Anda untuk menggunakan semua fitur situs'],

            // ─── Auth Panel — Register ─────────────────────────────────────
            ['setting_key' => 'auth_title_register', 'setting_value' => 'Halo, Kawan!'],
            ['setting_key' => 'auth_subtitle_register', 'setting_value' => 'Daftarkan detail pribadi Anda untuk menggunakan semua fitur situs'],

            // ─── Informasi Toko ───────────────────────────────────────────
            ['setting_key' => 'store_address', 'setting_value' => 'Jl. Bakti I No.9A, RT.7/RW.9, Kemanggisan, Kec. Palmerah, Kota Jakarta Barat, Daerah Khusus Ibukota Jakarta 11480'],
            ['setting_key' => 'store_phone', 'setting_value' => '085811118962'],
        ];

        foreach ($settings as $s) {
            SettingApp::updateOrCreate(['setting_key' => $s['setting_key']], ['setting_value' => $s['setting_value']]);
        }

        $this->command->info('✅ SettingAppSeeder berhasil di-seed!');
    }
}
