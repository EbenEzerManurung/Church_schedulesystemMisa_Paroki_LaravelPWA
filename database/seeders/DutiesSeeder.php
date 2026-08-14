<?php

namespace Database\Seeders;

use App\Models\Duty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DutiesSeeder extends Seeder
{
    public function run()
    {
        // Nonaktifkan foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Hapus data lama (opsional - comment jika tidak ingin menghapus)
        // Duty::truncate();
        
        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $this->command->info('📋 MEMBUAT TUGAS PELAYANAN');
        $this->command->info('========================================');
        
        $duties = [
            [
                'code' => 'MIS',
                'name' => 'Misdinar',
                'description' => 'Pelayan altar yang membantu imam dalam perayaan ekaristi',
                'min_person' => 2,
                'max_person' => 6,
                'is_active' => true,
            ],
            [
                'code' => 'KHO',
                'name' => 'Khotbah',
                'description' => 'Menyampaikan khotbah atau homili pada saat misa',
                'min_person' => 1,
                'max_person' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'LEK',
                'name' => 'Lektor',
                'description' => 'Membacakan bacaan pertama, kedua, dan doa umat',
                'min_person' => 1,
                'max_person' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'KOR',
                'name' => 'Koor',
                'description' => 'Memimpin dan mengiringi lagu-lagu ibadah',
                'min_person' => 4,
                'max_person' => 12,
                'is_active' => true,
            ],
            [
                'code' => 'ORG',
                'name' => 'Organis',
                'description' => 'Memainkan organ atau alat musik selama ibadah',
                'min_person' => 1,
                'max_person' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'PKO',
                'name' => 'Petugas Kolekte',
                'description' => 'Mengumpulkan persembahan umat pada saat misa',
                'min_person' => 2,
                'max_person' => 4,
                'is_active' => true,
            ],
            [
                'code' => 'PEM',
                'name' => 'Pemazmur',
                'description' => 'Memimpin nyanyian mazmur tanggapan',
                'min_person' => 1,
                'max_person' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'PKE',
                'name' => 'Petugas Kebersihan',
                'description' => 'Menjaga kebersihan dan kerapian gereja',
                'min_person' => 1,
                'max_person' => 3,
                'is_active' => true,
            ],
            [
                'code' => 'KOM',
                'name' => 'Komentator',
                'description' => 'Memberikan pengantar dan penjelasan selama ibadah',
                'min_person' => 1,
                'max_person' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'SAK',
                'name' => 'Sakristan',
                'description' => 'Menyiapkan perlengkapan dan peralatan ibadah',
                'min_person' => 1,
                'max_person' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'PRO',
                'name' => 'Prodiakon',
                'description' => 'Membantu imam dalam perayaan ekaristi',
                'min_person' => 1,
                'max_person' => 2,
                'is_active' => true,
            ],
        ];

        $createdCount = 0;
        $updatedCount = 0;

        foreach ($duties as $dutyData) {
            // Cek apakah duty sudah ada berdasarkan code atau name
            $existing = Duty::where('code', $dutyData['code'])
                ->orWhere('name', $dutyData['name'])
                ->first();
            
            if ($existing) {
                // Update jika ada perubahan
                $existing->update([
                    'description' => $dutyData['description'],
                    'min_person' => $dutyData['min_person'],
                    'max_person' => $dutyData['max_person'],
                    'is_active' => $dutyData['is_active'],
                ]);
                $updatedCount++;
                $this->command->info("   🔄 {$dutyData['code']} - {$dutyData['name']} (diupdate)");
            } else {
                // Buat baru
                Duty::create([
                    'code' => $dutyData['code'],
                    'name' => $dutyData['name'],
                    'slug' => Str::slug($dutyData['name']),
                    'description' => $dutyData['description'],
                    'min_person' => $dutyData['min_person'],
                    'max_person' => $dutyData['max_person'],
                    'is_active' => $dutyData['is_active'],
                ]);
                $createdCount++;
                $this->command->info("   ✅ {$dutyData['code']} - {$dutyData['name']} (baru)");
            }
        }

        $this->command->newLine();
        $this->command->info('========================================');
        $this->command->info("📊 Total Tugas: " . Duty::count());
        $this->command->info("   ✅ Baru: {$createdCount}");
        $this->command->info("   🔄 Diupdate: {$updatedCount}");
        
        // Tampilkan detail tugas dengan min/max person
        $this->command->newLine();
        $this->command->info('📋 DETAIL TUGAS:');
        $this->command->info('----------------------------------------');
        
        $dutiesList = Duty::orderBy('code')->get();
        foreach ($dutiesList as $duty) {
            $min = $duty->min_person ?? 1;
            $max = $duty->max_person ?: '∞';
            $status = $duty->is_active ? '✅ Aktif' : '❌ Nonaktif';
            $this->command->info("   {$duty->code} - {$duty->name} (Min: {$min} | Max: {$max}) {$status}");
        }
        
        $this->command->newLine();
        $this->command->info('✅ DutiesSeeder selesai!');
    }
}