<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;

/**
 * A place the owner sent cards from (e.g. while travelling). Coordinates
 * are encrypted at rest like contact coordinates.
 */
class Origin extends Model
{
    protected $table = 'origins';

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    protected function lat(): Attribute
    {
        return self::encryptedFloat();
    }

    protected function lng(): Attribute
    {
        return self::encryptedFloat();
    }

    private static function encryptedFloat(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value === null ? null : (float) Crypt::decryptString($value),
            set: fn ($value) => $value === null ? null : Crypt::encryptString((string) (float) $value),
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function postcards()
    {
        return $this->hasMany(Postcard::class);
    }

    /**
     * The user's saved origins, most recently used first.
     *
     * @return Collection<int, Origin>
     */
    public static function recentFor(?int $userId, int $limit = 10): Collection
    {
        return static::query()
            ->where('user_id', $userId)
            ->orderByDesc('last_used_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    /**
     * Find the user's origin with this name and coordinates, or create it,
     * and mark it as just used. Coordinates are encrypted, so matching
     * happens in PHP; a user has few origins.
     */
    public static function remember(int $userId, string $name, float $lat, float $lng): self
    {
        $name = trim($name);

        $origin = static::query()->where('user_id', $userId)->get()->first(
            fn (Origin $o) => mb_strtolower($o->name) === mb_strtolower($name)
                && round($o->lat, 5) === round($lat, 5)
                && round($o->lng, 5) === round($lng, 5)
        ) ?? new static(['user_id' => $userId, 'name' => $name, 'lat' => $lat, 'lng' => $lng]);

        $origin->last_used_at = now();
        $origin->save();

        return $origin;
    }
}
