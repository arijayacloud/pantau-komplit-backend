<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PmbaDetail extends Model
{
    protected $fillable = [
        'pmba_id',
        'karbohidrat',
        'protein_hewani',
        'protein_nabati',
        'sayur',
        'buah',
        'kacang',
        'susu',
        'telur',
        'vitamin_a',
        'asi',

        // 🔥 WAJIB ADA
        'skor',
        'status',
        'mdd_score',
        'mmf_status',
        'mad_status',
    ];

    public function pmba()
    {
        return $this->belongsTo(Pmba::class);
    }
}
