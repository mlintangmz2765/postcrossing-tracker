<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';

    public $timestamps = false;

    protected $guarded = ['id'];

    public function postcards()
    {
        return $this->hasMany(Postcard::class, 'country_id');
    }

    /**
     * Resolve a country typed by a user or a CSV row. Accepts the English
     * name, the Indonesian name, or the ISO 3166-1 alpha-2 code
     * (case-insensitive), so imports are not tied to Indonesian names.
     */
    public static function findByName(?string $name): ?self
    {
        $name = trim((string) $name);
        if ($name === '') {
            return null;
        }

        $lower = mb_strtolower($name);

        return static::query()->whereRaw('LOWER(nama_inggris) = ?', [$lower])->first()
            ?? static::query()->whereRaw('LOWER(nama_indonesia) = ?', [$lower])->first()
            ?? (strlen($name) === 2 ? static::query()->where('kode_iso', strtoupper($name))->first() : null);
    }

    /**
     * Name to show in the UI: English when available.
     */
    public function getDisplayNameAttribute(): ?string
    {
        return $this->nama_inggris ?: $this->nama_indonesia;
    }
}
