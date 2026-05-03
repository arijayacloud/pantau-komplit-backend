<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Anak extends Model
{
    use SoftDeletes;

    protected $table = 'anaks';

    protected $fillable = [
        'nik',
        'nama',
        'tanggal_lahir',
        'jenis_kelamin',
        'anak_ke',
        'alamat',
        'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date'
    ];

    /// 🔥 RELASI
    public function ibu()
    {
        return $this->belongsTo(Ibu::class);
    }

    public function kehamilan()
    {
        return $this->belongsTo(Kehamilan::class);
    }

    public function asi()
    {
        return $this->hasMany(AsiEksklusif::class);
    }

    public function pmba()
    {
        return $this->hasMany(Pmba::class);
    }

    public function pertumbuhan()
    {
        return $this->hasMany(PertumbuhanAnak::class)->latest();
    }

    public function getNamaIbuFinalAttribute()
    {
        return $this->ibu->nama ?? $this->nama_ibu ?? 'Tidak diketahui';
    }
}
