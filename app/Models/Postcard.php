<?php

namespace App\Models;

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
        'kurs_idr' => 'decimal:2',
        'notif_read' => 'boolean',
    ];

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
