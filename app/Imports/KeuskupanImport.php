<?php

namespace App\Imports;

use App\Models\Keuskupan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class KeuskupanImport implements ToModel, WithHeadingRow, WithValidation, SkipOnFailure, WithBatchInserts, WithChunkReading
{
    private $rowCount = 0;
    private $successCount = 0;
    private $failures = [];

    public function model(array $row)
    {
        $this->rowCount++;
        
        // Cek apakah data sudah ada berdasarkan kode
        $existing = Keuskupan::where('code', $row['kode'])->first();
        
        if ($existing) {
            $this->failures[] = new Failure(
                $this->rowCount,
                'kode',
                ['Kode keuskupan sudah ada'],
                $row
            );
            return null;
        }

        $this->successCount++;
        
        return new Keuskupan([
            'code' => $row['kode'],
            'name' => $row['nama_keuskupan'],
            'email' => $row['email'] ?? null,
            'phone' => $row['telepon'] ?? null,
            'description' => $row['deskripsi'] ?? null,
            'is_active' => isset($row['status']) ? ($row['status'] == 'Aktif' || $row['status'] == '1' ? 1 : 0) : 1,
        ]);
    }

    public function rules(): array
    {
        return [
            'kode' => 'required|string|max:10',
            'nama_keuskupan' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telepon' => 'nullable|string|max:20',
            'status' => 'nullable|in:Aktif,Nonaktif,1,0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'kode.required' => 'Kode keuskupan wajib diisi',
            'kode.max' => 'Kode keuskupan maksimal 10 karakter',
            'nama_keuskupan.required' => 'Nama keuskupan wajib diisi',
            'email.email' => 'Format email tidak valid',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->failures[] = $failure;
        }
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getFailures(): array
    {
        return $this->failures;
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}