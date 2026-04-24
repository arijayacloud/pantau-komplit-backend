<?php

namespace App\Services;

use App\Models\Pmba;
use App\Models\PmbaDetail;
use App\Models\Anak;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PmbaService
{
    public function store($data, $ibuId = null)
    {
        DB::beginTransaction();

        try {

            // 🔥 VALIDASI ANAK (kalau ibu)
            if ($ibuId) {
                $anak = Anak::where('id', $data['anak_id'])
                    ->where('ibu_id', $ibuId)
                    ->first();

                if (!$anak) {
                    throw new \Exception('Akses ditolak');
                }
            } else {
                $anak = Anak::findOrFail($data['anak_id']);
            }

            // 🔥 HITUNG USIA
            $usiaBulan = 0;
            if (!empty($anak->tanggal_lahir)) {
                $usiaBulan = Carbon::parse($anak->tanggal_lahir)
                    ->diffInMonths(Carbon::parse($data['tanggal']), false);

                if ($usiaBulan < 0) $usiaBulan = 0;
            }

            $tipe = $usiaBulan >= 6 ? 'mpasi' : 'pmba';

            // 🔥 NORMALIZE BOOLEAN
            $booleanFields = [
                'karbohidrat',
                'protein_hewani',
                'protein_nabati',
                'sayur',
                'buah',
                'kacang',
                'susu',
                'telur',
                'vitamin_a',
                'asi'
            ];

            foreach ($booleanFields as $f) {
                $data[$f] = filter_var($data[$f] ?? false, FILTER_VALIDATE_BOOLEAN);
            }

            $dataLengkap = array_merge($data, [
                'usia_bulan' => $usiaBulan
            ]);

            // 🔥 HITUNG SEMUA LOGIC
            $mdd = KonselingService::hitungMDD($dataLengkap);
            $mmf = KonselingService::hitungMMF($dataLengkap);
            $mad = KonselingService::hitungMAD($dataLengkap);
            $scoring = KonselingService::scoring($dataLengkap);

            // 🔥 SIMPAN
            $pmba = Pmba::create([
                'anak_id' => $data['anak_id'],
                'tanggal' => $data['tanggal'],
                'frekuensi_makan' => $data['frekuensi_makan'],
                'tekstur' => $data['tekstur'],
                'porsi' => $data['porsi'],
                'usia_bulan' => $usiaBulan,
                'tipe' => $tipe,
                'sumber_makanan' => $data['sumber_makanan'],
            ]);

            $pmba->detail()->create([
                ...collect($dataLengkap)->only([
                    'karbohidrat',
                    'protein_hewani',
                    'protein_nabati',
                    'sayur',
                    'buah',
                    'kacang',
                    'susu',
                    'telur',
                    'vitamin_a',
                    'asi'
                ]),

                'skor' => $scoring['skor'] ?? 0,
                'status' => $scoring['status'] ?? 'kurang',
                'mdd_score' => $mdd['total_group'] ?? 0,
                'mmf_status' => ($mmf['status'] ?? false) ? 1 : 0,
                'mad_status' => ($mad['status'] ?? false) ? 1 : 0,
            ]);

            $catatan = array_merge(
                KonselingService::generate($dataLengkap),
                KonselingService::fromDatabase($dataLengkap, $tipe)
            );

            DB::commit();

            return [
                'pmba' => $pmba->load('detail', 'anak'),
                'konseling' => array_values(array_unique($catatan)),
                'who' => [
                    'mdd_score' => $mdd['total_group'],
                    'mmf_label' => $mmf['status'] ? 'sesuai' : 'tidak',
                    'mad_label' => $mad['status'] ? 'terpenuhi' : 'tidak',
                ]
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function normalizeBool($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * LIST BY IBU
     */
    public function listByIbu($ibuId, $request)
    {
        return Pmba::with(['detail', 'anak'])
            ->whereHas('anak', fn($q) => $q->where('ibu_id', $ibuId))
            ->latest()
            ->paginate($request->per_page ?? 10);
    }

    /**
     * UPDATE
     */
    public function updateByIbu($ibuId, $id, $data)
    {
        $pmba = $this->findByIbu($ibuId, $id);

        if (!$pmba) throw new \Exception('Data tidak ditemukan');

        DB::beginTransaction();

        try {

            // 🔥 VALIDASI ANAK
            $anak = $pmba->anak;

            // 🔥 NORMALIZE BOOLEAN
            foreach ($this->booleanFields() as $f) {
                if (array_key_exists($f, $data)) {
                    $data[$f] = filter_var($data[$f], FILTER_VALIDATE_BOOLEAN);
                }
            }

            // 🔥 UPDATE HEADER
            $pmba->update($data);

            // 🔥 HITUNG ULANG USIA
            $usia = $this->hitungUsia($anak, $pmba->tanggal);
            $tipe = $usia >= 6 ? 'mpasi' : 'pmba';

            $pmba->update([
                'usia_bulan' => $usia,
                'tipe' => $tipe
            ]);

            // 🔥 GABUNG DATA
            $dataLengkap = array_merge(
                $pmba->toArray(),
                $pmba->detail ? $pmba->detail->toArray() : [],
                $data,
                ['usia_bulan' => $usia]
            );

            // 🔥 HITUNG ULANG LOGIC
            $mdd = KonselingService::hitungMDD($dataLengkap);
            $mmf = KonselingService::hitungMMF($dataLengkap);
            $mad = KonselingService::hitungMAD($dataLengkap);
            $scoring = KonselingService::scoring($dataLengkap);

            // 🔥 UPDATE DETAIL
            $detailData = array_merge(
                $this->extractDetail($dataLengkap),
                [
                    'skor' => $scoring['skor'] ?? 0,
                    'status' => $scoring['status'] ?? 'kurang',
                    'mdd_score' => $mdd['total_group'] ?? 0,
                    'mmf_status' => $mmf['status'] ? 1 : 0,
                    'mad_status' => $mad['status'] ? 1 : 0,
                ]
            );

            if ($pmba->detail) {
                $pmba->detail->update($detailData);
            } else {
                $pmba->detail()->create($detailData);
            }

            DB::commit();

            return $pmba->fresh()->load(['detail', 'anak']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function booleanFields()
    {
        return [
            'karbohidrat',
            'protein_hewani',
            'protein_nabati',
            'sayur',
            'buah',
            'kacang',
            'susu',
            'telur',
            'vitamin_a',
            'asi'
        ];
    }

    /**
     * DETAIL
     */
    public function detailByIbu($ibuId, $id)
    {
        return $this->findByIbu($ibuId, $id);
    }

    /**
     * DELETE
     */
    public function deleteByIbu($ibuId, $id)
    {
        $pmba = $this->findByIbu($ibuId, $id);

        if (!$pmba) return false;

        $pmba->delete();
        return true;
    }

    /**
     * HELPER
     */
    private function findByIbu($ibuId, $id)
    {
        return Pmba::with(['detail', 'anak'])
            ->where('id', $id)
            ->whereHas('anak', fn($q) => $q->where('ibu_id', $ibuId))
            ->first();
    }

    private function validateAnak($ibuId, $anakId)
    {
        $anak = Anak::where('id', $anakId)
            ->where('ibu_id', $ibuId)
            ->first();

        if (!$anak) {
            throw new \Exception('Akses ditolak');
        }

        return $anak;
    }

    private function hitungUsia($anak, $tanggal)
    {
        return Carbon::parse($anak->tanggal_lahir)
            ->diffInMonths(Carbon::parse($tanggal));
    }

    private function extractDetail($data)
    {
        return collect($data)->only([
            'karbohidrat',
            'protein_hewani',
            'protein_nabati',
            'sayur',
            'buah',
            'kacang',
            'susu',
            'telur',
            'vitamin_a',
            'asi'
        ])->toArray();
    }
}
