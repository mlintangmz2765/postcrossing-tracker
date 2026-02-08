<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'contacts';

    public $timestamps = false;

    public const CREATED_AT = null;

    public const UPDATED_AT = 'updated_at';

    protected $guarded = ['id'];

    /**
     * PII fields are encrypted at rest. Accessors handle decryption fallbacks.
     */
    protected $casts = [];

    public function getAlamatAttribute($value)
    {
        try {
            return $value ? \Illuminate\Support\Facades\Crypt::decryptString($value) : $value;
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return $value; // Return as-is (plain text fallback)
        }
    }

    public function getNomorTeleponAttribute($value)
    {
        try {
            return $value ? \Illuminate\Support\Facades\Crypt::decryptString($value) : $value;
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return $value; // Return as-is (plain text fallback)
        }
    }

    public function getLatAttribute($value)
    {
        try {
            $decrypted = $value ? \Illuminate\Support\Facades\Crypt::decryptString($value) : $value;

            return is_numeric($decrypted) ? (float) $decrypted : 0;
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return is_numeric($value) ? (float) $value : 0;
        }
    }

    public function getLngAttribute($value)
    {
        try {
            $decrypted = $value ? \Illuminate\Support\Facades\Crypt::decryptString($value) : $value;

            return is_numeric($decrypted) ? (float) $decrypted : 0;
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return is_numeric($value) ? (float) $value : 0;
        }
    }

    public function setAlamatAttribute($value)
    {
        $this->attributes['alamat'] = $value ? \Illuminate\Support\Facades\Crypt::encryptString((string) $value) : $value;
    }

    public function setNomorTeleponAttribute($value)
    {
        $this->attributes['nomor_telepon'] = $value ? \Illuminate\Support\Facades\Crypt::encryptString((string) $value) : $value;
    }

    public function setLatAttribute($value)
    {
        $this->attributes['lat'] = $value ? \Illuminate\Support\Facades\Crypt::encryptString((string) $value) : $value;
    }

    public function setLngAttribute($value)
    {
        $this->attributes['lng'] = $value ? \Illuminate\Support\Facades\Crypt::encryptString((string) $value) : $value;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function postcards()
    {
        return $this->hasMany(Postcard::class);
    }
}
