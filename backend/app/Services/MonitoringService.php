<?php

namespace App\Services;

use App\Models\Konseling;

class MonitoringService
{
    public static function hitungKepatuhan(array $bulan)
    {
        $total = count($bulan);

        $totalPatuh = collect($bulan)
            ->filter(fn($b) => (int)$b === 1)
            ->count();

        $totalTidak = $total - $totalPatuh;

        $persen = $total > 0
            ? round(($totalPatuh / $total) * 100)
            : 0;

        return [
            'total_patuh' => $totalPatuh,
            'total_tidak' => $totalTidak,
            'persen' => $persen
        ];
    }

    public static function statusKepatuhan($persen)
    {
        return match (true) {
            $persen >= 80 => 'baik',
            $persen >= 50 => 'cukup',
            default => 'kurang'
        };
    }

    public static function generateKonseling($usiaMinggu, $ruleResult)
    {
        $materi = collect($ruleResult['hasil'])
            ->groupBy('group')
            ->map(fn($items) => collect($items)->first())
            ->pluck('isi')
            ->filter()
            ->values()
            ->toArray();

        $materiUmur = Konseling::where('kategori', 'kehamilan')
            ->where(function ($q) use ($usiaMinggu) {
                $q->whereNull('min_minggu')
                    ->orWhere('min_minggu', '<=', $usiaMinggu);
            })
            ->where(function ($q) use ($usiaMinggu) {
                $q->whereNull('max_minggu')
                    ->orWhere('max_minggu', '>=', $usiaMinggu);
            })
            ->orderByDesc('priority')
            ->pluck('materi')
            ->toArray();

        $materi = array_merge($materi, $materiUmur);

        if (!empty($ruleResult['is_risk'])) {
            $materiRisiko = Konseling::where('resiko', 'tinggi')
                ->pluck('materi')
                ->toArray();

            $materi = array_merge($materi, $materiRisiko);
        }

        return array_values(array_unique($materi)); // 🔥 WAJIB RETURN
    }
}
