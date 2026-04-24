<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ibu extends Model
{
    use SoftDeletes;

    protected $table = 'ibus';

    protected $fillable = [
        'nik',
        'nama',
        'tanggal_lahir',
        'alamat',
        'status',
        'pendidikan',
        'pekerjaan',
        'no_hp',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date'
    ];

    /// 🔥 RELASI
    public function kehamilan()
    {
        return $this->hasMany(Kehamilan::class);
    }

    public function anak()
    {
        return $this->hasMany(Anak::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
