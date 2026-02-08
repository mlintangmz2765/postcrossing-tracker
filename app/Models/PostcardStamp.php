<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostcardStamp extends Model
{
    protected $table = 'postcard_stamps';

    public $timestamps = false;

    protected $guarded = ['id'];

    public function postcard()
    {
        return $this->belongsTo(Postcard::class);
    }
}
