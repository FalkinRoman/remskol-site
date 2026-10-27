<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    private const CACHE_KEY = 'settings.all';

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = Cache::rememberForever(self::CACHE_KEY, fn () => static::query()->pluck('value', 'key')->all());

        if (! array_key_exists($key, $all) || $all[$key] === null) {
            return $default;
        }

        $decoded = json_decode($all[$key], true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $all[$key];
    }

    public static function put(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => json_encode($value, JSON_UNESCAPED_UNICODE)]);
        Cache::forget(self::CACHE_KEY);
    }

    public static function forget(string $key): void
    {
        static::query()->whereKey($key)->delete();
        Cache::forget(self::CACHE_KEY);
    }
}
