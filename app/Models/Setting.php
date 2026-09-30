<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    private const CACHE_KEY = 'settings.all';

    /**
     * Récupère la valeur d'un réglage (avec cache) ou son défaut.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return static::cached()->get($key, $default);
    }

    /**
     * Enregistre un réglage et invalide le cache.
     */
    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return \Illuminate\Support\Collection<string, string|null>
     */
    protected static function cached(): \Illuminate\Support\Collection
    {
        if (! Schema::hasTable('settings')) {
            return collect();
        }

        // On ne met en cache qu'un tableau de scalaires (jamais un objet) :
        // sérialiser une Collection peut échouer à la relecture selon le driver
        // (__PHP_Incomplete_Class).
        $data = Cache::rememberForever(self::CACHE_KEY, fn () => static::pluck('value', 'key')->all());

        return collect(is_array($data) ? $data : []);
    }
}
