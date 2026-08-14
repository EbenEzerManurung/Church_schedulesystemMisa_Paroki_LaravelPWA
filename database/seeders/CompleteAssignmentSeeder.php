<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Duty;
use App\Models\Schedule;
use App\Models\DutyAssignment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompleteAssignmentSeeder extends Seeder
{
    public function run()
    {
        // Nonaktifkan foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DutyAssignment::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $this->command->info('========================================');
        $this->command->info('📋 MEMBUAT DATA PENUGASAN LENGKAP');
        $this->command->info('========================================');
        
        // 1. Ambil atau buat user Jhonny
        $jhonny = User::firstOrCreate(
            ['email' => 'jhonny@example.com'],
            [
                'name' => 'Jhonny',
                'password' => bcrypt('password'),
                'phone' => '08123456789',
                'address' => 'Jl. Katedral No. 1, Bogor',
                'is_active' => true,
                'level_akses' => 'user',
                'keuskupan_id' => 1,
                'gereja_id' => 1,
            ]
        );
        
        // 2. Ambil semua duties
        $duties = Duty::all();
        $dutyMap = [];
        foreach ($duties as $duty) {
            $dutyMap[$duty->name] = $duty->id;
        }
        
        // 3. Assign duty_id ke user yang belum punya
        $users = User::where('level_akses', 'user')->get();
        $dutyNames = ['Lektor', 'Pemazmur', 'Misdinar', 'Prodiakon', 'Koor/Paduan Suara', 'Organis/Musisi Gereja', 'Komentator', 'Sakristan'];
        
        $this->command->info("\n📌 STEP 1: Assign tugas tetap ke user");
        $this->command->info('----------------------------------------');
        
        $i = 0;
        foreach ($users as $user) {
            if (!$user->duty_id) {
                $dutyName = $dutyNames[$i % count($dutyNames)];
                $dutyId = $dutyMap[$dutyName] ?? null;
                if ($dutyId) {
                    $user->update(['duty_id' => $dutyId]);
                    $this->command->info("   ✅ {$user->name} -> {$dutyName}");
                }
                $i++;
            } else {
                $this->command->info("   ✅ {$user->name} sudah memiliki tugas");
            }
        }
        
        // Pastikan Jhonny dapat tugas Prodiakon
        $prodiakonId = $dutyMap['Prodiakon'] ?? null;
        if ($prodiakonId) {
            $jhonny->update(['duty_id' => $prodiakonId]);
            $this->command->info("   ✅ Jhonny dipastikan sebagai Prodiakon");
        }
        
        // 4. Ambil semua schedule
        $schedules = Schedule::where('status', 'active')->get();
        
        if ($schedules->isEmpty()) {
            $this->command->error('Tidak ada schedule!');
            return;
        }
        
        $this->command->info("\n📌 STEP 2: Membuat penugasan untuk setiap schedule");
        $this->command->info('----------------------------------------');
        
        // 5. Buat penugasan untuk setiap user ke setiap schedule
        $assignmentsCreated = 0;
        $assignmentData = [];
        
        foreach ($schedules as $schedule) {
            foreach ($users as $user) {
                if ($user->duty_id) {
                    // Random status dengan probabilitas
                    $rand = rand(1, 100);
                    
                    if ($rand <= 30) {
                        $status = 'pending';
                        $respondedAt = null;
                        $rejectionReason = null;
                    } elseif ($rand <= 55) {
                        $status = 'accepted';
                        $respondedAt = now()->subDays(rand(1, 7));
                        $rejectionReason = null;
                    } elseif ($rand <= 70) {
                        $status = 'rejected';
                        $respondedAt = now()->subDays(rand(1, 7));
                        $rejectionReason = 'Berhalangan hadir karena ada acara keluarga';
                    } elseif ($rand <= 85) {
                        $status = 'completed';
                        $respondedAt = now()->subDays(rand(1, 3));
                        $rejectionReason = null;
                    } else {
                        $status = 'cancelled';
                        $respondedAt = now()->subDays(rand(1, 2));
                        $rejectionReason = null;
                    }
                    
                    $assignmentData[] = [
                        'schedule_id' => $schedule->id,
                        'duty_id' => $user->duty_id,
                        'user_id' => $user->id,
                        'status' => $status,
                        'notes' => 'Penugasan untuk ' . $schedule->name,
                        'rejection_reason' => $rejectionReason,
                        'responded_at' => $respondedAt,
                        'created_at' => now()->subDays(rand(1, 30)),
                        'updated_at' => now(),
                    ];
                    
                    $assignmentsCreated++;
                }
            }
        }
        
        // 6. Buat penugasan khusus untuk Jhonny dengan status ACCEPTED untuk minggu depan
        $nextSundaySchedule = Schedule::where('day', 'minggu')
            ->orderBy('time')
            ->first();
        
        if ($nextSundaySchedule && $prodiakonId) {
            // Cek apakah sudah ada assignment untuk Jhonny di schedule ini
            $exists = DutyAssignment::where('schedule_id', $nextSundaySchedule->id)
                ->where('user_id', $jhonny->id)
                ->exists();
            
            if (!$exists) {
                $assignmentData[] = [
                    'schedule_id' => $nextSundaySchedule->id,
                    'duty_id' => $prodiakonId,
                    'user_id' => $jhonny->id,
                    'status' => 'accepted',
                    'notes' => 'Penugasan untuk misa minggu depan - Jhonny sudah konfirmasi bersedia',
                    'rejection_reason' => null,
                    'responded_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $assignmentsCreated++;
                $this->command->info("   ✅ KHUSUS: Jhonny -> {$nextSundaySchedule->name} (Status: ACCEPTED)");
            }
        }
        
        // 7. Insert batch
        if (!empty($assignmentData)) {
            $chunks = array_chunk($assignmentData, 100);
            foreach ($chunks as $chunk) {
                DutyAssignment::insert($chunk);
            }
        }
        
        // 8. Tampilkan ringkasan
        $this->command->newLine();
        $this->command->info('========================================');
        $this->command->info('📊 RINGKASAN PENUGASAN');
        $this->command->info('========================================');
        $this->command->info("Total Penugasan: {$assignmentsCreated}");
        $this->command->info('');
        
        $statusCounts = [
            'pending' => DutyAssignment::where('status', 'pending')->count(),
            'accepted' => DutyAssignment::where('status', 'accepted')->count(),
            'rejected' => DutyAssignment::where('status', 'rejected')->count(),
            'completed' => DutyAssignment::where('status', 'completed')->count(),
            'cancelled' => DutyAssignment::where('status', 'cancelled')->count(),
        ];
        
        $this->command->info('📈 Statistik Berdasarkan Status:');
        $this->command->info("   🟡 PENDING (Menunggu)   : {$statusCounts['pending']}");
        $this->command->info("   🟢 ACCEPTED (Diterima) : {$statusCounts['accepted']}");
        $this->command->info("   🔴 REJECTED (Ditolak)  : {$statusCounts['rejected']}");
        $this->command->info("   🔵 COMPLETED (Selesai) : {$statusCounts['completed']}");
        $this->command->info("   ⚪ CANCELLED (Batal)   : {$statusCounts['cancelled']}");
        
        $this->command->newLine();
        $this->command->info('✅ Complete Assignment Seeder selesai!');
        
        // 9. Tampilkan info login untuk Jhonny
        $this->command->newLine();
        $this->command->info('========================================');
        $this->command->info('🔐 AKUN JHONNY');
        $this->command->info('========================================');
        $this->command->info("Email    : jhonny@example.com");
        $this->command->info("Password : password");
        $this->command->info("Tugas    : Prodiakon");
        $this->command->info("Status   : Accepted (Sudah Konfirmasi Bersedia)");
        $this->command->info('========================================');
    }
}