<?php

namespace App\Imports;

use App\Models\DutyAssignment;
use App\Models\Schedule;
use App\Models\Duty;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AssignmentImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    use Importable;

    private $successCount = 0;
    private $failureCount = 0;
    private $failures = [];
    private $errors = [];

    public function model(array $row)
    {
        try {
            // Cari data berdasarkan nama
            $schedule = Schedule::where('name', $row['jadwal_ibadah'])
                ->orWhere('display', $row['jadwal_ibadah'])
                ->first();
            
            $duty = Duty::where('name', $row['tugas_pelayanan'])
                ->orWhere('code', $row['tugas_pelayanan'])
                ->first();
            
            $user = User::where('email', $row['email_petugas'])->first();

            if (!$schedule) {
                $this->failures[] = [
                    'row' => $row,
                    'errors' => "Jadwal Ibadah '{$row['jadwal_ibadah']}' tidak ditemukan"
                ];
                $this->failureCount++;
                return null;
            }

            if (!$duty) {
                $this->failures[] = [
                    'row' => $row,
                    'errors' => "Tugas Pelayanan '{$row['tugas_pelayanan']}' tidak ditemukan"
                ];
                $this->failureCount++;
                return null;
            }

            if (!$user) {
                $this->failures[] = [
                    'row' => $row,
                    'errors' => "Email Petugas '{$row['email_petugas']}' tidak ditemukan"
                ];
                $this->failureCount++;
                return null;
            }

            // Parse tanggal
            $eventDate = null;
            if (isset($row['tanggal_penugasan'])) {
                try {
                    $eventDate = \Carbon\Carbon::createFromFormat('d/m/Y', $row['tanggal_penugasan'])->format('Y-m-d');
                } catch (\Exception $e) {
                    $this->failures[] = [
                        'row' => $row,
                        'errors' => "Format tanggal '{$row['tanggal_penugasan']}' tidak valid. Gunakan format d/m/Y"
                    ];
                    $this->failureCount++;
                    return null;
                }
            } else {
                $this->failures[] = [
                    'row' => $row,
                    'errors' => "Tanggal Penugasan wajib diisi"
                ];
                $this->failureCount++;
                return null;
            }

            // Cek duplikasi
            $exists = DutyAssignment::where('schedule_id', $schedule->id)
                ->where('duty_id', $duty->id)
                ->where('user_id', $user->id)
                ->where('event_date', $eventDate)
                ->exists();

            if ($exists) {
                $this->failures[] = [
                    'row' => $row,
                    'errors' => "Penugasan sudah ada: {$row['jadwal_ibadah']} - {$row['tugas_pelayanan']} - {$user->name} pada tanggal {$row['tanggal_penugasan']}"
                ];
                $this->failureCount++;
                return null;
            }

            $this->successCount++;

            return new DutyAssignment([
                'schedule_id' => $schedule->id,
                'duty_id' => $duty->id,
                'user_id' => $user->id,
                'event_date' => $eventDate,
                'status' => 'pending',
                'notes' => $row['catatan'] ?? null,
            ]);

        } catch (\Exception $e) {
            $this->failures[] = [
                'row' => $row,
                'errors' => 'Error: ' . $e->getMessage()
            ];
            $this->failureCount++;
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'tanggal_penugasan' => 'required',
            'jadwal_ibadah' => 'required|string',
            'tugas_pelayanan' => 'required|string',
            'email_petugas' => 'required|email',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'tanggal_penugasan.required' => 'Tanggal Penugasan wajib diisi',
            'jadwal_ibadah.required' => 'Jadwal Ibadah wajib diisi',
            'tugas_pelayanan.required' => 'Tugas Pelayanan wajib diisi',
            'email_petugas.required' => 'Email Petugas wajib diisi',
            'email_petugas.email' => 'Format Email Petugas tidak valid',
        ];
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getFailureCount()
    {
        return $this->failureCount;
    }

    public function getFailures()
    {
        return $this->failures;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    // Batch insert untuk performa lebih baik
    public function batchSize(): int
    {
        return 100;
    }

    // Chunk reading untuk handle file besar
    public function chunkSize(): int
    {
        return 100;
    }
}