<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Duty;
use Illuminate\Database\Seeder;

class UpdateUserDutySeeder extends Seeder
{
    public function run()
    {
        // Ambil semua duties
        $duties = Duty::all();
        $dutyMap = [];
        foreach ($duties as $duty) {
            $dutyMap[$duty->name] = $duty->id;
        }
        
        $this->command->info('📋 UPDATE DUTY_ID UNTUK USER');
        $this->command->info('========================================');
        
        // Daftar tugas yang akan diberikan
        $dutyAssignments = [
            // User untuk Keuskupan Jakarta
            'umat.jakarta@gmail.com' => 'Prodiakon',
            'umat.bogor@gmail.com' => 'Lektor',
            'umat.bandung@gmail.com' => 'Pemazmur',
            'umat.semarang@gmail.com' => 'Misdinar',
            'umat.surabaya@gmail.com' => 'Koor/Paduan Suara',
            
            // User untuk gereja-gereja di Bogor
            'umat.gereja_katedral_bogor@gmail.com' => 'Sakristan',
            'umat.gereja_st_alfonsus@gmail.com' => 'Komentator',
            'umat.gereja_st_albertus@gmail.com' => 'Organis/Musisi Gereja',
            'umat.gereja_st_yakobus@gmail.com' => 'Prodiakon',
            'umat.gereja_st_fransiskus_assisi@gmail.com' => 'Lektor',
            'umat.gereja_st_petrus_dan_paulus@gmail.com' => 'Pemazmur',
            'umat.gereja_st_theresia@gmail.com' => 'Misdinar',
            'umat.gereja_st_mikael@gmail.com' => 'Koor/Paduan Suara',
        ];
        
        $updatedCount = 0;
        
        foreach ($dutyAssignments as $email => $dutyName) {
            $user = User::where('email', $email)->first();
            $dutyId = $dutyMap[$dutyName] ?? null;
            
            if ($user && $dutyId) {
                $user->update(['duty_id' => $dutyId]);
                $updatedCount++;
                $this->command->info("   ✅ {$user->name} ({$email}) -> Tugas: {$dutyName}");
            } else {
                $this->command->warn("   ⚠️ User tidak ditemukan: {$email} atau tugas {$dutyName} tidak ada");
            }
        }
        
        // Update semua user biasa yang belum punya duty_id
        $usersWithoutDuty = User::where('level_akses', 'user')
            ->whereNull('duty_id')
            ->get();
        
        $dutyNames = array_keys($dutyMap);
        $index = 0;
        
        foreach ($usersWithoutDuty as $user) {
            $dutyName = $dutyNames[$index % count($dutyNames)];
            $dutyId = $dutyMap[$dutyName];
            $user->update(['duty_id' => $dutyId]);
            $updatedCount++;
            $this->command->info("   ✅ {$user->name} ({$user->email}) -> Tugas: {$dutyName}");
            $index++;
        }
        
        $this->command->newLine();
        $this->command->info("📊 Total user yang diupdate: {$updatedCount}");
        $this->command->info('✅ Update User Duty selesai!');
    }
}