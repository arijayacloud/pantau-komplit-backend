<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonselingRule extends Model
{
    protected $fillable = ['kategori','kondisi','isi_konseling'];
}
