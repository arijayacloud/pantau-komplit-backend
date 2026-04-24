<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsiEksklusif extends Model
{
    protected $fillable = [
        'anak_id',
        'bulan_ke',
        'status_asi',
        'catatan'
    ];

    public function anak()
    {
        return $this->belongsTo(Anak::class);
    }
}
