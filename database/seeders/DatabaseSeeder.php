<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Nonaktifkan foreign key checks sementara
        Schema::disableForeignKeyConstraints();
        
        try {
            // ============================================
            // 1. ROLE & PERMISSION (WAJIB PERTAMA)
            // ============================================
            $this->call(RolesAndPermissionsSeeder::class);
            
            // ============================================
            // 2. MENUS
            // ============================================
            $this->call(MenusSeeder::class);
            
            // ============================================
            // 3. MASTER DATA (Keuskupan dan Gereja)
            // ============================================
            $this->call(KeuskupanGerejaSeeder::class);
            
            // ============================================
            // 4. DUTY (harus sebelum UsersSeeder)
            // ============================================
            $this->call(DutiesSeeder::class);
            
            // ============================================
            // 5. SCHEDULE (harus sebelum UsersSeeder)
            // ============================================
            $this->call(ScheduleSeeder::class);
            
            // ============================================
            // 6. USER (setelah Duty dan Schedule)
            // ============================================
            $this->call(UsersSeeder::class);
            
            // ============================================
            // 7. KALENDER LITURGI
            // ============================================
            $this->call(KalenderLiturgiSeeder::class);
            
            // ============================================
            // 8. ASSIGNMENT (terakhir)
            // ============================================
            $this->call(AssignmentSeeder::class);
            
        } catch (\Exception $e) {
            $this->command->error('Seeder error: ' . $e->getMessage());
            throw $e;
        } finally {
            // Aktifkan kembali foreign key checks
            Schema::enableForeignKeyConstraints();
        }
        
        $this->command->info('');
        $this->command->info('✅ All seeders completed successfully!');
    }
}