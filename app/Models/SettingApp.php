<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingApp extends Model
{
    protected $table = 'setting_apps';
    protected $fillable = ['setting_key', 'setting_value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('setting_key', $key)->first();
        return $setting ? $setting->setting_value : $default;
    }

    public static function set(string $key, mixed $value): static
    {
        return static::updateOrCreate(['setting_key' => $key], ['setting_value' => $value]);
    }

    public static function allAsArray(): array
    {
        return static::all()->pluck('setting_value', 'setting_key')->toArray();
    }
}
