<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PertumbuhanAnak extends Model
{
    use HasFactory;

    protected $table = 'pertumbuhan_anaks';

    protected $fillable = [
        'anak_id',
        'tanggal',
        'berat_badan',
        'tinggi_badan',
        'lingkar_kepala',
        'z_score_bb',
        'z_score_tb',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'berat_badan' => 'float',
        'tinggi_badan' => 'float',
        'lingkar_kepala' => 'float',
    ];

    protected $appends = [
        'umur_bulan',
        'status_gizi',
    ];

    /**
     * 🔗 RELASI KE ANAK
     */
    public function anak()
    {
        return $this->belongsTo(Anak::class);
    }

    /**
     * 🎂 HITUNG UMUR SAAT PENGUKURAN (bulan)
     */
    public function getUmurBulanAttribute()
    {
        if (!$this->anak || !$this->anak->tanggal_lahir || !$this->tanggal) {
            return null;
        }

        $lahir = Carbon::parse($this->anak->tanggal_lahir);
        $ukur = Carbon::parse($this->tanggal);

        return $lahir->diffInMonths($ukur);
    }

    /**
     * 🟢 STATUS GIZI SEDERHANA (dummy rule)
     * nanti bisa upgrade pakai WHO z-score
     */
    public function getStatusGiziAttribute()
    {
        if (!$this->berat_badan || !$this->tinggi_badan) {
            return '-';
        }

        $imt = $this->berat_badan / pow(($this->tinggi_badan / 100), 2);

        if ($imt < 14) return 'Kurus';
        if ($imt < 18) return 'Normal';
        return 'Gemuk';
    }
}
