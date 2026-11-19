<?php

namespace App\Models;

use App\Support\Money;
use App\Support\Place;
use App\Traits\CalculatesDistance;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Postcard extends Model
{
    use CalculatesDistance;

    protected $table = 'postcards';

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_kirim' => 'date',
        'tanggal_terima' => 'date',
        'biaya_prangko' => 'decimal:2',
        'nilai_asal' => 'decimal:2',
        'exchange_rate' => 'decimal:10',
        'notif_read' => 'boolean',
    ];

    protected static function booted(): void
    {
        // Record which currency biaya_prangko is in, so a later change of
        // APP_BASE_CURRENCY cannot relabel this card's cost.
        static::creating(function (Postcard $postcard) {
            if (empty($postcard->base_currency)) {
                $postcard->base_currency = Money::base();
            }
        });
    }

    /**
     * Limit to cards whose cost was recorded in the given (default: configured) base currency.
     */
    public function scopeInBaseCurrency(Builder $query, ?string $currency = null): Builder
    {
        return $query->where('base_currency', $currency ?: Money::base());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stamps()
    {
        return $this->hasMany(PostcardStamp::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Where a sent card was mailed from; null means home.
     */
    public function origin()
    {
        return $this->belongsTo(Origin::class);
    }

    /**
     * The owner's end of the journey: the card's origin for sent cards
     * (home when none is set), and home for received cards.
     */
    public function ownerPlace(): Place
    {
        if ($this->type === 'sent' && $this->origin) {
            return Place::fromOrigin($this->origin);
        }

        return Place::home();
    }

    /**
     * Distance between the owner's end and the contact, or null when the
     * contact has no coordinates.
     */
    public function distanceKm(): ?float
    {
        $lat = $this->contact?->lat;
        $lng = $this->contact?->lng;

        if (! $lat || ! $lng || ! is_numeric($lat) || ! is_numeric($lng)) {
            return null;
        }

        $from = $this->ownerPlace();

        return $this->calculateDistance($from->lat, $from->lng, (float) $lat, (float) $lng);
    }
}
