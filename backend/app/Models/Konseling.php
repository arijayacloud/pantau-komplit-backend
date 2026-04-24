<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Konseling extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'materi',
        'min_minggu',
        'max_minggu',
        'min_bulan',
        'max_bulan',
        'kategori', // 🔥 tambah
        'resiko',   // 🔥 tambah
    ];
}
