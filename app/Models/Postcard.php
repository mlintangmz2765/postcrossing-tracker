<?php

namespace App\Models;

use App\Support\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Postcard extends Model
{
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
}
