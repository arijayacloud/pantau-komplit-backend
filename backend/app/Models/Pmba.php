<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PmbaDetail;

class Pmba extends Model
{
    protected $fillable = ['anak_id', 'tanggal', 'usia_bulan', 'frekuensi_makan', 'tekstur', 'porsi'];

    public function detail()
    {
        return $this->hasOne(PmbaDetail::class);
    }

    public function anak()
    {
        return $this->belongsTo(Anak::class, 'anak_id');
    }
}
