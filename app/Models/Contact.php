<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'contacts';
    public $timestamps = false; // It has updated_at but not created_at, better to handle manually or disable.
    // Actually, if it has updated_at, Laravel might try to update created_at too.
    // Let's set const CREATED_AT = null;
    const CREATED_AT = null;
    const UPDATED_AT = 'updated_at';

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
