<?php

namespace App\Services;

use Carbon\Carbon;

class KonselingService
{
    public static function generate($data)
    {
        $detail = $data['detail'] ?? [];
        $data = array_merge($data, $detail);

        $catatan = [];

        // 🔥 USIA
        $usiaBulan = 0;
        if (!empty($data['anak']['tanggal_lahir'])) {
            $usiaBulan = Carbon::parse($data['anak']['tanggal_lahir'])
                ->diffInMonths(now());
        }

        $data['usia_bulan'] = $usiaBulan;

        // 🔥 HITUNG SEKALI
        $mdd = self::hitungMDD($data);
        $mmf = self::hitungMMF($data);
        $mad = [
            'status' => $mdd['status'] && $mmf['status']
        ];

        // 🔴 RULE WHO
        if (!$mdd['status']) {
            $catatan[] = "Keragaman makanan belum memenuhi standar WHO";
        }

        if (!$mmf['status']) {
            $catatan[] = "Frekuensi makan kurang dari standar WHO";
        }

        if (!$mad['status']) {
            $catatan[] = "Minimum Acceptable Diet belum tercapai";
        }

        // 🔴 NUTRISI
        if (empty($data['protein_hewani'])) {
            $catatan[] = "Protein hewani penting untuk pertumbuhan";
        }

        if (empty($data['vitamin_a'])) {
            $catatan[] = "Tambahkan makanan kaya vitamin A";
        }

        // 🟡 BEHAVIOR
        switch ($data['sumber_makanan'] ?? null) {
            case 'instan':
                $catatan[] = "Kurangi makanan instan";
                break;

            case 'campuran':
                $catatan[] = "Perbanyak makanan rumahan";
                break;

            case 'rumahan':
                $catatan[] = $mad['status']
                    ? "Pertahankan pola makan sehat"
                    : "Variasi makanan perlu ditingkatkan";
                break;
        }

        return array_values(array_unique($catatan));
    }

    public static function scoring($data)
    {
        $score = 0;

        $fields = [
            'karbohidrat',
            'protein_hewani',
            'protein_nabati',
            'sayur',
            'buah',
            'kacang',
            'susu',
            'telur',
            'vitamin_a'
        ];

        foreach ($fields as $f) {
            if (!empty($data[$f])) {
                $score++;
            }
        }

        $status = match (true) {
            $score >= 7 => 'baik',
            $score >= 4 => 'cukup',
            default => 'kurang',
        };

        return [
            'skor' => $score,
            'status' => $status
        ];
    }

    public static function hitungMDD($data)
    {
        $data = array_map(fn($v) => (bool)$v, $data);
        $groups = 0;

        if (!empty($data['asi'])) $groups++;
        if (!empty($data['karbohidrat'])) $groups++;
        if (!empty($data['protein_nabati'])) $groups++;
        if (!empty($data['susu'])) $groups++;
        if (!empty($data['protein_hewani'])) $groups++;
        if (!empty($data['telur'])) $groups++;
        if (!empty($data['vitamin_a'])) $groups++;
        if (!empty($data['buah'])) $groups++;

        return [
            'total_group' => $groups,
            'status' => $groups >= 5
        ];
    }

    public static function hitungMMF($data)
    {
        // 🔥 NORMALISASI DATA (LETTAKAN DI SINI)
        $usia = max(0, (int)($data['usia_bulan'] ?? 0));
        $freq = max(0, (int)($data['frekuensi_makan'] ?? 0));

        if ($usia >= 6 && $usia <= 8) {
            return [
                'min' => 2,
                'status' => $freq >= 2
            ];
        }

        if ($usia >= 9) {
            return [
                'min' => 3,
                'status' => $freq >= 3
            ];
        }

        return [
            'min' => 0,
            'status' => true
        ];
    }

    public static function hitungMAD($data)
    {
        $mdd = self::hitungMDD($data);
        $mmf = self::hitungMMF($data);

        return [
            'status' => $mdd['status'] && $mmf['status']
        ];
    }

    /**
     * Ambil konseling dari database rule engine
     */
    public static function fromDatabase($data, $kategori = 'pmba')
    {
        try {
            $engine = new DecisionRuleEngine();
            $result = $engine->run($kategori, $data);

            if (!isset($result['hasil']) || !is_array($result['hasil'])) {
                return [];
            }

            return collect($result['hasil'])
                ->pluck('isi')
                ->filter() // 🔥 buang null
                ->values()
                ->toArray();
        } catch (\Throwable $e) {
            return []; // 🔥 jangan pernah return null
        }
    }

    public static function generateAsi($data)
    {
        $catatan = [];

        if (empty($data['status_asi'])) {
            $catatan[] = "ASI eksklusif belum terpenuhi, perlu edukasi kepada ibu.";
        }

        if (($data['bulan_ke'] ?? 0) <= 6 && empty($data['status_asi'])) {
            $catatan[] = "Usia 0-6 bulan dianjurkan ASI eksklusif.";
        }

        return array_values(array_unique($catatan));
    }
}
