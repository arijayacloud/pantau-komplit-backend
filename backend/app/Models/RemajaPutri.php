<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RemajaPutri extends Model
{
    protected $fillable = [
        'nama',
        'tanggal_lahir',
        'no_hp',
        'sekolah',
        'kelas',
        'alamat',
        'hb',
        'berat_badan',
        'tinggi_badan',
        'sudah_menstruasi',
        'tanggal_menstruasi_terakhir',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_menstruasi_terakhir' => 'date',
        'sudah_menstruasi' => 'boolean',
    ];

    public function ttd()
    {
        return $this->hasMany(TtdRemaja::class, 'remaja_id');
    }
}
