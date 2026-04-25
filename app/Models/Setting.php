<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['setting_key', 'setting_value', 'description'];

    // The primary key is a string, not a number
    protected $primaryKey = 'setting_key';
    public    $incrementing = false;
    protected $keyType = 'string';

    // Easy static helper: Setting::get('shop_name', 'BrewTrack')
    public static function get(string $key, mixed $default = null): mixed
    {
        return static::where('setting_key', $key)->value('setting_value') ?? $default;
    }

    // Easy static helper: Setting::set('shop_name', 'My Coffee Shop')
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['setting_key' => $key],
            ['setting_value' => $value]
        );
    }
}