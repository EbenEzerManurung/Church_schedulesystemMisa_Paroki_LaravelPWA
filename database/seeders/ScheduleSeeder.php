<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\Gereja;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleSeeder extends Seeder
{
    public function run()
    {
        // Matikan foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Hapus data lama
        Schedule::query()->delete();
        
        // Aktifkan foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $this->command->info('📋 MEMBUAT JADWAL IBADAH TETAP');
        $this->command->info('========================================');
        
        // ============================================
        // 1. AMBIL GEREJA PERTAMA
        // ============================================
        $gereja = Gereja::first();
        
        if (!$gereja) {
            $this->command->error('❌ Tidak ada gereja. Jalankan KeuskupanGerejaSeeder terlebih dahulu.');
            return;
        }

        $this->command->info("📍 Gereja: {$gereja->nama} (ID: {$gereja->id})");
        $this->command->newLine();
        
        // ============================================
        // 2. AMBIL ATAU BUAT SERVICE
        // ============================================
        $service = Service::first();
        
        if (!$service) {
            $service = Service::create([
                'name' => 'Misa',
                'code' => 'MISA',
                'description' => 'Perayaan Ekaristi Kudus',
                'is_active' => true,
            ]);
            $this->command->info("✅ Service 'Misa' berhasil dibuat");
        }
        
        // ============================================
        // 3. DATA JADWAL DENGAN MASTER_DATE YANG BENAR
        // ============================================
        $schedules = [
            [
                'day' => 'sabtu',
                'time' => '17:00:00',
                'name' => 'Misa Sabtu Sore',
                'schedule_type' => 'evening',
                'status' => 'active',
                'description' => 'Misa Hari Sabtu pukul 17:00 WIB',
                'gereja_id' => $gereja->id,
                'service_id' => $service->id,
                'master_date' => '2026-07-25', // SABTU, 25 JULI 2026
                'date' => null,
            ],
            [
                'day' => 'minggu',
                'time' => '06:00:00',
                'name' => 'Misa Minggu Pagi I',
                'schedule_type' => 'morning',
                'status' => 'active',
                'description' => 'Misa Hari Minggu pukul 06:00 WIB',
                'gereja_id' => $gereja->id,
                'service_id' => $service->id,
                'master_date' => '2026-07-26', // MINGGU, 26 JULI 2026
                'date' => null,
            ],
            [
                'day' => 'minggu',
                'time' => '08:30:00',
                'name' => 'Misa Minggu Pagi II',
                'schedule_type' => 'morning',
                'status' => 'active',
                'description' => 'Misa Hari Minggu pukul 08:30 WIB',
                'gereja_id' => $gereja->id,
                'service_id' => $service->id,
                'master_date' => '2026-07-26',
                'date' => null,
            ],
            [
                'day' => 'minggu',
                'time' => '11:00:00',
                'name' => 'Misa Minggu Siang',
                'schedule_type' => 'afternoon',
                'status' => 'active',
                'description' => 'Misa Hari Minggu pukul 11:00 WIB',
                'gereja_id' => $gereja->id,
                'service_id' => $service->id,
                'master_date' => '2026-07-26',
                'date' => null,
            ],
            [
                'day' => 'minggu',
                'time' => '16:30:00',
                'name' => 'Misa Minggu Sore',
                'schedule_type' => 'evening',
                'status' => 'active',
                'description' => 'Misa Hari Minggu pukul 16:30 WIB',
                'gereja_id' => $gereja->id,
                'service_id' => $service->id,
                'master_date' => '2026-07-26',
                'date' => null,
            ],
            [
                'day' => 'minggu',
                'time' => '19:00:00',
                'name' => 'Misa Minggu Malam',
                'schedule_type' => 'evening',
                'status' => 'active',
                'description' => 'Misa Hari Minggu pukul 19:00 WIB',
                'gereja_id' => $gereja->id,
                'service_id' => $service->id,
                'master_date' => '2026-07-26',
                'date' => null,
            ],
        ];

        $createdCount = 0;
        $updatedCount = 0;

        foreach ($schedules as $schedule) {
            // Cek apakah sudah ada
            $existing = Schedule::where('day', $schedule['day'])
                                ->where('time', $schedule['time'])
                                ->where('name', $schedule['name'])
                                ->where('gereja_id', $schedule['gereja_id'])
                                ->first();
            
            if ($existing) {
                $existing->update($schedule);
                $updatedCount++;
                $this->command->info("   🔄 {$schedule['name']} - master_date: {$schedule['master_date']}");
            } else {
                Schedule::create($schedule);
                $createdCount++;
                $this->command->info("   ✅ {$schedule['name']} - master_date: {$schedule['master_date']}");
            }
        }

        // ============================================
        // 4. RINGKASAN
        // ============================================
        $this->command->newLine();
        $this->command->info('========================================');
        $this->command->info('📊 RINGKASAN');
        $this->command->info('========================================');
        $this->command->info("   ✅ {$createdCount} jadwal baru berhasil dibuat");
        $this->command->info("   🔄 {$updatedCount} jadwal sudah ada (diperbarui)");
        $this->command->info('   📋 Total jadwal: ' . Schedule::count());
        
        $this->command->newLine();
        $this->command->info('📋 Daftar Jadwal dengan Master Date:');
        $schedules = Schedule::orderByRaw("FIELD(day, 'sabtu', 'minggu')")->orderBy('time')->get();
        foreach ($schedules as $s) {
            $masterDate = $s->master_date ? date('d/m/Y', strtotime($s->master_date)) : '-';
            $this->command->info("   {$s->day} {$s->time} - {$s->name} - Master: {$masterDate}");
        }
        $this->command->newLine();
        $this->command->info('✅ ScheduleSeeder selesai!');
    }
}