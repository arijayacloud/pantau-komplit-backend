<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Monitoring extends Model
{
    use HasFactory;

    protected $fillable = [
        'kehamilan_id',
        'tanggal',

        'bulan_1',
        'bulan_2',
        'bulan_3',
        'bulan_4',
        'bulan_5',
        'bulan_6',

        'total_patuh',
        'status_kepatuhan',
        'is_risk',

        // 🔥 WAJIB ADA
        'hasil_konseling',
    ];

    protected $casts = [
        'hasil_konseling' => 'array',
        'is_risk' => 'boolean',

        'bulan_1' => 'integer',
        'bulan_2' => 'integer',
        'bulan_3' => 'integer',
        'bulan_4' => 'integer',
        'bulan_5' => 'integer',
        'bulan_6' => 'integer',
    ];

    public function kehamilan(): BelongsTo
    {
        return $this->belongsTo(Kehamilan::class, 'kehamilan_id');
    }
}
