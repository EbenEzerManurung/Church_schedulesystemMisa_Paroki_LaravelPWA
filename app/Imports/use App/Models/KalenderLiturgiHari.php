<?php


namespace App\Imports;

use App\Models\KalenderLiturgiHari;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;

class KalenderLiturgiImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable;

    private $errors = [];

    public function model(array $row)
    {
        return new KalenderLiturgiHari([
            'tanggal' => $row['tanggal'],
            'keterangan_hari' => $row['keterangan_hari'],
            'warna_liturgi' => $row['warna_liturgi'],
            'bacaan1' => $row['bacaan1'] ?? null,
            'mazmur_tanggapan' => $row['mazmur_tanggapan'] ?? null,
            'bait_pengantarinjil' => $row['bait_pengantarinjil'] ?? null,
            'bacaan_injil' => $row['bacaan_injil'] ?? null,
            'catatan' => $row['catatan'] ?? null,
            'is_active' => isset($row['is_active']) ? (bool)$row['is_active'] : true,
        ]);
    }

    public function rules(): array
    {
        return [
            'tanggal' => 'required|date|unique:kalender_liturgi_hari,tanggal',
            'keterangan_hari' => 'required|string|max:255',
            'warna_liturgi' => 'required|in:putih,merah,ungu,hijau,kuning,hitam,pink,biru',
            'bacaan1' => 'nullable|string',
            'mazmur_tanggapan' => 'nullable|string',
            'bait_pengantarinjil' => 'nullable|string',
            'bacaan_injil' => 'nullable|string',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ];
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }
}