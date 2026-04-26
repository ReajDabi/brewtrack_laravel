<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    // Settings table has no auto-increment id as primary key
    protected $primaryKey = 'setting_key';
    public    $incrementing = false;
    protected $keyType = 'string';

    // Only one timestamp column in this table
    public    $timestamps = false;

    protected $fillable = [
        'setting_key',
        'setting_value',
        'description',
    ];

    // Easy static helper to get a value
    // Usage: Setting::get('shop_name', 'Default Name')
    public static function get(string $key, $default = null)
    {
        $setting = static::where('setting_key', $key)->first();
        return $setting ? $setting->setting_value : $default;
    }

    // Easy static helper to set a value
    // Usage: Setting::set('shop_name', 'My Coffee Shop')
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['setting_key'   => $key],
            ['setting_value' => $value]
        );
    }
}