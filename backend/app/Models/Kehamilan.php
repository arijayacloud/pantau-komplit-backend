<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Kehamilan extends Model
{
    protected $table = 'kehamilans';

    protected $fillable = [
        'ibu_id',
        'hpht',
        'status'
    ];

    protected $appends = ['usia_kehamilan_minggu', 'trimester', 'hpl'];

    /// 🔥 RELASI
    public function ibu()
    {
        return $this->belongsTo(Ibu::class);
    }

    public function anak()
    {
        return $this->hasMany(Anak::class);
    }

    public function getTrimesterAttribute()
    {
        if (!$this->hpht) return null;

        $weeks = intdiv(Carbon::parse($this->hpht)->diffInDays(now()), 7);

        if ($weeks < 13) return "Trimester 1";
        if ($weeks < 27) return "Trimester 2";
        return "Trimester 3";
    }

    public function getUsiaKehamilanMingguAttribute()
    {
        if (!$this->hpht) return null;

        $hpht = Carbon::parse($this->hpht);
        $now = now();

        /// 🔥 TOTAL HARI (INTEGER)
        $totalDays = $hpht->diffInDays($now);

        /// 🔥 HITUNG MANUAL (ANTI DECIMAL)
        $weeks = intdiv($totalDays, 7);
        $days = $totalDays % 7;

        return "{$weeks} minggu {$days} hari";
    }

    public function getHplAttribute()
    {
        if (!$this->hpht) return null;

        return Carbon::parse($this->hpht)
            ->addDays(280)
            ->format('d M Y');
    }

    public function monitorings()
    {
        return $this->hasMany(Monitoring::class);
    }
}
