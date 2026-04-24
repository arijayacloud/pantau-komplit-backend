<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TtdRemaja extends Model
{
    protected $fillable = [
        'remaja_id',
        'tanggal',
        'jumlah_minum',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function remaja()
    {
        return $this->belongsTo(RemajaPutri::class, 'remaja_id');
    }
}
