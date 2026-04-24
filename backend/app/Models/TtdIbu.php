<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TtdIbu extends Model
{
    protected $fillable = [
        'ibu_id',
        'tanggal_dapat',
        'bulan_ke',
        'jumlah_diminum',
        'catatan'
    ];

    public function ibu()
    {
        return $this->belongsTo(Ibu::class, 'ibu_id');
    }
}
