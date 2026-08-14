<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Gereja;
use App\Models\Keuskupan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserImport implements ToModel, WithHeadingRow, WithValidation, SkipOnFailure, WithBatchInserts, WithChunkReading
{
    private $rowCount = 0;
    private $successCount = 0;
    private $failures = [];
    private $gerejaCache = [];
    private $keuskupanCache = [];

    public function model(array $row)
    {
        $this->rowCount++;
        
        // Cari gereja berdasarkan nama atau kode
        $gereja = $this->getGereja($row['gereja'] ?? $row['nama_gereja'] ?? null);
        
        if (!$gereja && ($row['level_akses'] === 'admin_gereja' || $row['level_akses'] === 'user')) {
            $this->failures[] = new Failure(
                $this->rowCount,
                'gereja',
                ['Gereja "' . ($row['gereja'] ?? $row['nama_gereja'] ?? '') . '" tidak ditemukan'],
                $row
            );
            return null;
        }
        
        // Cari keuskupan jika diperlukan
        $keuskupan = null;
        if ($row['level_akses'] === 'admin_keuskupan') {
            $keuskupan = $this->getKeuskupan($row['keuskupan'] ?? $row['nama_keuskupan'] ?? null);
            if (!$keuskupan) {
                $this->failures[] = new Failure(
                    $this->rowCount,
                    'keuskupan',
                    ['Keuskupan "' . ($row['keuskupan'] ?? $row['nama_keuskupan'] ?? '') . '" tidak ditemukan'],
                    $row
                );
                return null;
            }
        }
        
        // Cek apakah email sudah ada
        if (User::where('email', $row['email'])->exists()) {
            $this->failures[] = new Failure(
                $this->rowCount,
                'email',
                ['Email "' . $row['email'] . '" sudah terdaftar'],
                $row
            );
            return null;
        }
        
        // Default password
        $password = isset($row['password']) && !empty($row['password']) ? $row['password'] : 'password';
        
        $this->successCount++;
        
        $keuskupanId = null;
        $gerejaId = null;
        
        if ($row['level_akses'] === 'admin_keuskupan' && $keuskupan) {
            $keuskupanId = $keuskupan->id;
        } elseif (($row['level_akses'] === 'admin_gereja' || $row['level_akses'] === 'user') && $gereja) {
            $gerejaId = $gereja->id;
            $keuskupanId = $gereja->keuskupan_id;
        }
        
        return new User([
            'name' => $row['nama'],
            'email' => $row['email'],
            'password' => Hash::make($password),
            'phone' => $row['telepon'] ?? $row['phone'] ?? null,
            'address' => $row['alamat'] ?? null,
            'level_akses' => $row['level_akses'],
            'keuskupan_id' => $keuskupanId,
            'gereja_id' => $gerejaId,
            'is_active' => isset($row['status']) ? ($row['status'] == 'Aktif' || $row['status'] == '1' ? 1 : 0) : 1,
        ]);
    }

    private function getGereja($identifier)
    {
        if (!$identifier) {
            return null;
        }
        
        if (isset($this->gerejaCache[$identifier])) {
            return $this->gerejaCache[$identifier];
        }
        
        $gereja = Gereja::where('nama', 'like', '%' . $identifier . '%')
            ->orWhere('kode', 'like', '%' . $identifier . '%')
            ->first();
        
        if ($gereja) {
            $this->gerejaCache[$identifier] = $gereja;
        }
        
        return $gereja;
    }

    private function getKeuskupan($identifier)
    {
        if (!$identifier) {
            return null;
        }
        
        if (isset($this->keuskupanCache[$identifier])) {
            return $this->keuskupanCache[$identifier];
        }
        
        $keuskupan = Keuskupan::where('name', 'like', '%' . $identifier . '%')
            ->orWhere('code', 'like', '%' . $identifier . '%')
            ->first();
        
        if ($keuskupan) {
            $this->keuskupanCache[$identifier] = $keuskupan;
        }
        
        return $keuskupan;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'email' => 'required|email',
            'level_akses' => 'required|in:super_admin,admin_keuskupan,admin_gereja,user',
            'status' => 'nullable|in:Aktif,Nonaktif,1,0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'level_akses.required' => 'Level akses wajib diisi',
            'level_akses.in' => 'Level akses tidak valid',
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