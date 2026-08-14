<?php

namespace App\Imports;

use App\Models\Duty;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class DutyImport implements ToModel, WithHeadingRow, WithValidation, SkipOnFailure
{
    private $rowCount = 0;
    private $successCount = 0;
    private $failures = [];

    public function model(array $row)
    {
        $this->rowCount++;
        
        // Cek apakah sudah ada
        if (Duty::where('name', $row['nama'])->exists()) {
            $this->failures[] = new Failure(
                $this->rowCount,
                'nama',
                ['Tugas "' . $row['nama'] . '" sudah ada'],
                $row
            );
            return null;
        }
        
        $this->successCount++;
        
        return new Duty([
            'code' => $row['kode'] ?? Duty::generateUniqueCode(),
            'name' => $row['nama'],
            'slug' => Str::slug($row['nama']),
            'description' => $row['deskripsi'] ?? null,
            'min_person' => $row['min_person'] ?? 1,
            'max_person' => $row['max_person'] ?? null,
            'is_active' => isset($row['status']) ? ($row['status'] == 'Aktif' || $row['status'] == '1' ? 1 : 0) : 1,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:100',
            'min_person' => 'nullable|integer|min:0',
            'max_person' => 'nullable|integer|min:0',
            'status' => 'nullable|in:Aktif,Nonaktif,1,0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama.required' => 'Nama tugas wajib diisi',
            'min_person.integer' => 'Minimum petugas harus berupa angka',
            'max_person.integer' => 'Maksimum petugas harus berupa angka',
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
}