<?php

namespace App\Support;

use App\Models\Origin;
use App\Traits\CalculatesDistance;

/**
 * A named point: home (from config) or a saved origin.
 */
final class Place
{
    use CalculatesDistance;

    public function __construct(
        public readonly string $name,
        public readonly float $lat,
        public readonly float $lng,
        public readonly bool $isHome = false,
    ) {}

    /**
     * The configured home location (HOME_LAT / HOME_LNG / OWNER_LOCATION).
     */
    public static function home(): self
    {
        return new self(
            (string) (config('app.owner_location') ?: 'Home'),
            (float) config('app.home_lat'),
            (float) config('app.home_lng'),
            true,
        );
    }

    public static function fromOrigin(Origin $origin): self
    {
        return new self($origin->name, (float) $origin->lat, (float) $origin->lng);
    }

    /**
     * False for the 0,0 placeholder used when HOME_LAT / HOME_LNG are unset.
     */
    public function hasCoordinates(): bool
    {
        return $this->lat != 0.0 || $this->lng != 0.0;
    }

    /**
     * Rounded coordinates for public pages (1 decimal is roughly 11 km), so
     * recipients see the area a card came from, not the owner's address.
     *
     * @return array{lat: float, lng: float}
     */
    public function coarse(int $decimals = 1): array
    {
        return ['lat' => round($this->lat, $decimals), 'lng' => round($this->lng, $decimals)];
    }

    /**
     * Distance for public pages: measured from the coarse point and rounded
     * to the nearest 10 km. An exact distance would let a recipient, who
     * knows their own address, narrow the coarse point back down.
     */
    public function publicDistanceTo(float $lat, float $lng): float
    {
        $from = $this->coarse();

        return round($this->calculateDistance($from['lat'], $from['lng'], $lat, $lng) / 10) * 10;
    }
}
