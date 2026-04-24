<?php

namespace App\Services;

use App\Models\AsiEksklusif;
use App\Models\Anak;

class AsiService
{
    public function analyze($anakId, $bulanKe, $engine)
    {
        $anak = Anak::find($anakId);

        $riwayat = AsiEksklusif::where('anak_id', $anakId)
            ->where('bulan_ke', '<=', $bulanKe)
            ->orderBy('bulan_ke')
            ->pluck('status_asi')
            ->map(fn($v) => $v == 1)
            ->toArray();

        return $this->runAnalysis($anak, $anakId, $bulanKe, $riwayat, $engine);
    }

    private function runAnalysis($anak, $anakId, $bulanKe, $riwayat, $engine)
    {
        $totalData = count($riwayat);
        $totalAsi  = collect($riwayat)->filter(fn($v) => $v)->count();

        $konsistensi = $totalData > 0
            ? round($totalAsi / $totalData, 2)
            : 1;

        $score = 100; // bisa pakai function kamu

        $result = $engine->run('asi', [
            'anak_id' => $anakId,
            'bulan_ke' => $bulanKe,
            'riwayat_asi' => $riwayat,
            'score' => $score,
        ]);

        return [
            'status' => $score >= 80 ? 'Aman' : 'Risiko',
            'score' => $score,
            'risk_score' => $result['risk_score'] ?? 0,
            'konseling' => collect($result['hasil'])->pluck('isi'),
        ];
    }
}
