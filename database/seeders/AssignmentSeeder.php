<?php

namespace Database\Seeders;

use App\Models\DutyAssignment;
use App\Models\Schedule;
use App\Models\Duty;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AssignmentSeeder extends Seeder
{
    public function run()
    {
        // Nonaktifkan foreign key checks sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Hapus data lama
        DutyAssignment::query()->delete();
        
        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $this->command->info('📋 MEMBUAT DATA PENUGASAN');
        $this->command->info('========================================');
        
        // ============================================
        // AMBIL DATA DARI DATABASE
        // ============================================
        
        // Ambil semua schedule yang aktif
        $schedules = Schedule::where('status', 'active')->get();
        
        if ($schedules->isEmpty()) {
            $this->command->warn('⚠️  Tidak ada schedule. Jalankan ScheduleSeeder terlebih dahulu.');
            return;
        }
        
        // Ambil duties yang aktif
        $duties = Duty::where('is_active', true)->get();
        
        if ($duties->isEmpty()) {
            $this->command->warn('⚠️  Tidak ada duties. Jalankan DutiesSeeder terlebih dahulu.');
            return;
        }
        
        // Ambil semua users yang memiliki duty_id
        $users = User::whereNotNull('duty_id')
            ->where('is_active', true)
            ->with('duty')
            ->get();
        
        if ($users->isEmpty()) {
            $this->command->warn('⚠️  Tidak ada user dengan duty_id. Jalankan UsersSeeder terlebih dahulu.');
            return;
        }
        
        $this->command->info('');
        $this->command->info('📊 Data yang ditemukan:');
        $this->command->info("   - Schedule: {$schedules->count()} jadwal");
        $this->command->info("   - Duties: {$duties->count()} tugas");
        $this->command->info("   - Users: {$users->count()} petugas");
        $this->command->info('');
        
        // ============================================
        // DATA USER YANG AKAN DIBUATKAN ASSIGNMENT
        // ============================================
        
        // Definisikan user-user yang akan dibuatkan assignment
        // Format: [name, email, duty_name]
        $targetUsers = [
            ['name' => 'Jhonny', 'email' => 'jhonny@gmail.com', 'duty' => 'Misdinar'],
            ['name' => 'Budi', 'email' => 'budi@gmail.com', 'duty' => 'Khotbah'],
            ['name' => 'Siti', 'email' => 'siti@gmail.com', 'duty' => 'Lektor'],
            ['name' => 'Eben', 'email' => 'ebenmanurung@gmail.com', 'duty' => 'Koor'],
            ['name' => 'Rina', 'email' => 'rina@gmail.com', 'duty' => 'Organis'],
            ['name' => 'Doni', 'email' => 'doni@gmail.com', 'duty' => 'Petugas Kolekte'],
            ['name' => 'Maya', 'email' => 'maya@gmail.com', 'duty' => 'Pemazmur'],
            ['name' => 'Rudi', 'email' => 'rudi@gmail.com', 'duty' => 'Petugas Kebersihan'],
        ];
        
        $this->command->info('👤 TARGET USER:');
        foreach ($targetUsers as $target) {
            $this->command->info("   - {$target['name']} ({$target['duty']})");
        }
        $this->command->info('');
        
        $assignmentData = [];
        $assignmentsCreated = 0;
        
        // ============================================
        // BUAT ASSIGNMENT UNTUK SETIAP TARGET USER
        // ============================================
        
        foreach ($targetUsers as $target) {
            // Cari user berdasarkan email
            $user = $users->where('email', $target['email'])->first();
            
            if (!$user) {
                $this->command->warn("   ⚠️ User {$target['name']} tidak ditemukan, skip...");
                continue;
            }
            
            // Cari duty yang sesuai dengan user
            $userDuty = $duties->where('name', $target['duty'])->first();
            
            if (!$userDuty) {
                $this->command->warn("   ⚠️ Duty {$target['duty']} tidak ditemukan, skip...");
                continue;
            }
            
            // Jumlah assignment untuk user ini (random 2-4)
            $numAssignments = rand(2, 4);
            $userAssignments = 0;
            
            $this->command->info("   👤 {$user->name} ({$userDuty->name}) - {$numAssignments} penugasan");
            
            // Pilih schedule secara random
            $userSchedules = $schedules->shuffle()->take($numAssignments);
            
            foreach ($userSchedules as $schedule) {
                // Generate event_date (random antara 30 hari lalu sampai 60 hari ke depan)
            $eventDate = Carbon::now()->addDays(12);
                
                // Pilih status: accepted atau rejected (50:50)
                $status = rand(0, 1) == 0 ? 'accepted' : 'rejected';
                
                // Set data berdasarkan status
                if ($status == 'accepted') {
                    $respondedAt = Carbon::now()->subDays(rand(1, 10));
                    $availability_status = 'available';
                    $availabilityUpdatedAt = Carbon::now()->subDays(rand(1, 10));
                    $rejectionReason = null;
                    $unavailableReason = null;
                    $notes = 'Siap melayani pada tanggal ' . $eventDate->format('d/m/Y');
                } else {
                    $respondedAt = Carbon::now()->subDays(rand(1, 10));
                    $availability_status = 'unavailable';
                    $availabilityUpdatedAt = Carbon::now()->subDays(rand(1, 10));
                    $rejectionReason = $this->getRandomRejectionReason();
                    $unavailableReason = $rejectionReason;
                    $notes = 'Tidak dapat melayani pada tanggal ' . $eventDate->format('d/m/Y');
                }
                
                $assignmentData[] = [
                    'schedule_id' => $schedule->id,
                    'duty_id' => $userDuty->id,
                    'user_id' => $user->id,
                    'replacement_user_id' => null,
                    'event_date' => $eventDate->format('Y-m-d'),
                    'status' => $status,
                    'availability_status' => $availability_status,
                    'notes' => $notes,
                    'rejection_reason' => $rejectionReason,
                    'unavailable_reason' => $unavailableReason,
                    'replacement_request_id' => null,
                    'responded_at' => $respondedAt,
                    'availability_updated_at' => $availabilityUpdatedAt,
                    'created_at' => Carbon::now()->subDays(rand(1, 20)),
                    'updated_at' => Carbon::now(),
                ];
                
                $userAssignments++;
                $assignmentsCreated++;
                
                $statusIcon = $status == 'accepted' ? '✅' : '❌';
                $this->command->info("       {$statusIcon} {$eventDate->format('d/m/Y')} - {$schedule->name}");
            }
            
            $this->command->info("       └─ Total: {$userAssignments} penugasan");
            $this->command->info('');
        }
        
        // ============================================
        // TAMBAHKAN ASSIGNMENT UNTUK USER LAINNYA (RANDOM)
        // ============================================
        
        $this->command->info('📝 MENAMBAHKAN ASSIGNMENT UNTUK USER LAINNYA');
        $this->command->info('----------------------------------------');
        
        // Ambil user lain yang belum dapat assignment
        $existingUserEmails = array_column($targetUsers, 'email');
        $otherUsers = $users->whereNotIn('email', $existingUserEmails)->shuffle();
        
        $extraAssignments = 0;
        $maxExtraPerUser = 2;
        
        foreach ($otherUsers as $user) {
            if ($extraAssignments >= 20) break; // Maksimal 20 assignment tambahan
            
            $numAssignments = rand(1, $maxExtraPerUser);
            
            // PERBAIKAN: Cek apakah user memiliki duty
            $dutyName = 'Tidak ada duty';
            if ($user->duty) {
                $dutyName = $user->duty->name;
            }
            
            $this->command->info("   👤 {$user->name} ({$dutyName}) - {$numAssignments} penugasan");
            
            $userSchedules = $schedules->shuffle()->take($numAssignments);
            
            foreach ($userSchedules as $schedule) {
                $eventDate = Carbon::now()->addDays(rand(-30, 60));
                $status = rand(0, 1) == 0 ? 'accepted' : 'rejected';
                
                if ($status == 'accepted') {
                    $respondedAt = Carbon::now()->subDays(rand(1, 10));
                    $availability_status = 'available';
                    $availabilityUpdatedAt = Carbon::now()->subDays(rand(1, 10));
                    $rejectionReason = null;
                    $unavailableReason = null;
                    $notes = 'Siap melayani pada tanggal ' . $eventDate->format('d/m/Y');
                } else {
                    $respondedAt = Carbon::now()->subDays(rand(1, 10));
                    $availability_status = 'unavailable';
                    $availabilityUpdatedAt = Carbon::now()->subDays(rand(1, 10));
                    $rejectionReason = $this->getRandomRejectionReason();
                    $unavailableReason = $rejectionReason;
                    $notes = 'Tidak dapat melayani pada tanggal ' . $eventDate->format('d/m/Y');
                }
                
                $assignmentData[] = [
                    'schedule_id' => $schedule->id,
                    'duty_id' => $user->duty_id,
                    'user_id' => $user->id,
                    'replacement_user_id' => null,
                    'event_date' => $eventDate->format('Y-m-d'),
                    'status' => $status,
                    'availability_status' => $availability_status,
                    'notes' => $notes,
                    'rejection_reason' => $rejectionReason,
                    'unavailable_reason' => $unavailableReason,
                    'replacement_request_id' => null,
                    'responded_at' => $respondedAt,
                    'availability_updated_at' => $availabilityUpdatedAt,
                    'created_at' => Carbon::now()->subDays(rand(1, 20)),
                    'updated_at' => Carbon::now(),
                ];
                
                $extraAssignments++;
                $assignmentsCreated++;
                
                $statusIcon = $status == 'accepted' ? '✅' : '❌';
                $this->command->info("       {$statusIcon} {$eventDate->format('d/m/Y')} - {$schedule->name}");
            }
            
            $this->command->info('');
        }
        
        // ============================================
        // INSERT KE DATABASE
        // ============================================
        
        if (!empty($assignmentData)) {
            // Insert per chunk
            $chunks = array_chunk($assignmentData, 50);
            foreach ($chunks as $chunk) {
                DutyAssignment::insert($chunk);
            }
        }
        
        $this->command->newLine();
        $this->command->info('========================================');
        $this->command->info("📊 TOTAL PENUGASAN: {$assignmentsCreated}");
        $this->command->info('========================================');
        
        // ============================================
        // STATISTIK
        // ============================================
        
        $this->command->newLine();
        $this->command->info('📈 STATISTIK:');
        
        $acceptedCount = DutyAssignment::where('status', 'accepted')->count();
        $rejectedCount = DutyAssignment::where('status', 'rejected')->count();
        
        $this->command->info("   ✅ Diterima: {$acceptedCount}");
        $this->command->info("   ❌ Ditolak: {$rejectedCount}");
        
        // Statistik per user
        $this->command->newLine();
        $this->command->info('📊 STATISTIK PER USER:');
        
        $userStats = DutyAssignment::with('user')
            ->select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->orderBy('total', 'desc')
            ->get();
        
        foreach ($userStats as $stat) {
            $user = User::find($stat->user_id);
            if ($user) {
                $accepted = DutyAssignment::where('user_id', $user->id)->where('status', 'accepted')->count();
                $rejected = DutyAssignment::where('user_id', $user->id)->where('status', 'rejected')->count();
                $this->command->info("   👤 {$user->name}: {$stat->total} total (✅ {$accepted} | ❌ {$rejected})");
            }
        }
        
        // Statistik per tanggal
        $this->command->newLine();
        $this->command->info('📅 STATISTIK PER TANGGAL (5 terbaru):');
        
        $dateStats = DutyAssignment::select(DB::raw('DATE(event_date) as date'), DB::raw('count(*) as total'))
            ->whereNotNull('event_date')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();
        
        foreach ($dateStats as $stat) {
            $this->command->info("   📅 " . Carbon::parse($stat->date)->format('d/m/Y') . ": {$stat->total} penugasan");
        }
        
        $this->command->newLine();
        $this->command->info('✅ Seeder Assignment selesai!');
    }
    
    /**
     * Get random rejection reason
     */
    private function getRandomRejectionReason()
    {
        $reasons = [
            'Berhalangan hadir karena ada acara keluarga',
            'Sedang sakit dan tidak bisa melayani',
            'Ada keperluan mendadak yang tidak bisa ditinggalkan',
            'Bertugas di gereja lain pada waktu yang sama',
            'Harus menemani anggota keluarga yang sakit',
            'Tidak bisa melayani karena sedang dinas di luar kota',
            'Ada kegiatan gereja lain yang harus dihadiri',
            'Sedang cuti dan tidak berada di kota',
            'Kesehatan sedang kurang baik',
            'Ada acara pernikahan keluarga yang harus dihadiri',
        ];
        
        return $reasons[array_rand($reasons)];
    }
}