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
}
