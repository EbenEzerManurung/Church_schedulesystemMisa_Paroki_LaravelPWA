<?php
// database/seeders/PicGroupSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Duty;
use App\Models\Schedule;
use App\Models\DutyAssignment;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class PicGroupSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📍 MEMBUAT PIC GROUP PER SCHEDULE');
        $this->command->info('========================================');

        // ============================================
        // 1. AMBIL DATA DUTY
        // ============================================
        $duties = Duty::all()->keyBy('name');
        
        if ($duties->isEmpty()) {
            $this->command->error('❌ Tidak ada duty. Jalankan seeder duty terlebih dahulu.');
            return;
        }

        // ============================================
        // 2. AMBIL DATA SCHEDULE
        // ============================================
        $schedules = Schedule::all();
        
        if ($schedules->isEmpty()) {
            $this->command->error('❌ Tidak ada schedule. Jalankan ScheduleSeeder terlebih dahulu.');
            return;
        }

        // ============================================
        // 3. AMBIL ROLE PIC GROUP
        // ============================================
        $picRole = Role::where('name', 'pic_group')->first();
        
        if (!$picRole) {
            $this->command->error('❌ Role pic_group tidak ditemukan. Jalankan RolesAndPermissionsSeeder terlebih dahulu.');
            return;
        }

        // ============================================
        // 4. DATA PIC GROUP PER DUTY DAN SCHEDULE
        // ============================================
        $picGroupData = [
            // ========================================
            // DUTY: KOOR
            // ========================================
            'Koor' => [
                [
                    'name' => 'Samuel Koor',
                    'email' => 'samuel.koor@group.com',
                    'phone' => '081234567910',
                    'schedules' => ['Misa Sabtu Sore'], // Hanya Sabtu sore
                ],
                [
                    'name' => 'Erick Koor',
                    'email' => 'erick.koor@group.com',
                    'phone' => '081234567920',
                    'schedules' => [
                        'Misa Minggu Pagi I',
                        'Misa Minggu Pagi II',
                        'Misa Minggu Siang',
                        'Misa Minggu Sore',
                        'Misa Minggu Malam'
                    ], // Minggu full
                ],
            ],
            
            // ========================================
            // DUTY: MISDINAR
            // ========================================
            'Misdinar' => [
                [
                    'name' => 'Maria Misdinar',
                    'email' => 'maria.misdinar@group.com',
                    'phone' => '081234567911',
                    'schedules' => [
                        'Misa Sabtu Sore',
                        'Misa Minggu Pagi I',
                        'Misa Minggu Pagi II'
                    ],
                ],
                [
                    'name' => 'Petrus Misdinar',
                    'email' => 'petrus.misdinar@group.com',
                    'phone' => '081234567912',
                    'schedules' => [
                        'Misa Minggu Siang',
                        'Misa Minggu Sore',
                        'Misa Minggu Malam'
                    ],
                ],
            ],
            
            // ========================================
            // DUTY: LEKTOR
            // ========================================
            'Lektor' => [
                [
                    'name' => 'Agnes Lektor',
                    'email' => 'agnes.lektor@group.com',
                    'phone' => '081234567913',
                    'schedules' => [
                        'Misa Sabtu Sore',
                        'Misa Minggu Pagi I',
                        'Misa Minggu Pagi II'
                    ],
                ],
                [
                    'name' => 'Budi Lektor',
                    'email' => 'budi.lektor@group.com',
                    'phone' => '081234567914',
                    'schedules' => [
                        'Misa Minggu Siang',
                        'Misa Minggu Sore',
                        'Misa Minggu Malam'
                    ],
                ],
            ],
            
            // ========================================
            // DUTY: ORGANIS
            // ========================================
            'Organis' => [
                [
                    'name' => 'Cindy Organis',
                    'email' => 'cindy.organis@group.com',
                    'phone' => '081234567915',
                    'schedules' => [
                        'Misa Sabtu Sore',
                        'Misa Minggu Pagi I'
                    ],
                ],
                [
                    'name' => 'Doni Organis',
                    'email' => 'doni.organis@group.com',
                    'phone' => '081234567916',
                    'schedules' => [
                        'Misa Minggu Pagi II',
                        'Misa Minggu Siang',
                        'Misa Minggu Sore',
                        'Misa Minggu Malam'
                    ],
                ],
            ],
            
            // ========================================
            // DUTY: PETUGAS KOLEKTE
            // ========================================
            'Petugas Kolekte' => [
                [
                    'name' => 'Eko Kolekte',
                    'email' => 'eko.kolekte@group.com',
                    'phone' => '081234567917',
                    'schedules' => [
                        'Misa Sabtu Sore',
                        'Misa Minggu Pagi I',
                        'Misa Minggu Pagi II'
                    ],
                ],
                [
                    'name' => 'Fina Kolekte',
                    'email' => 'fina.kolekte@group.com',
                    'phone' => '081234567918',
                    'schedules' => [
                        'Misa Minggu Siang',
                        'Misa Minggu Sore',
                        'Misa Minggu Malam'
                    ],
                ],
            ],
            
            // ========================================
            // DUTY: PEMAZMUR
            // ========================================
            'Pemazmur' => [
                [
                    'name' => 'Gita Pemazmur',
                    'email' => 'gita.pemazmur@group.com',
                    'phone' => '081234567919',
                    'schedules' => [
                        'Misa Sabtu Sore',
                        'Misa Minggu Pagi I'
                    ],
                ],
                [
                    'name' => 'Hendra Pemazmur',
                    'email' => 'hendra.pemazmur@group.com',
                    'phone' => '081234567920',
                    'schedules' => [
                        'Misa Minggu Pagi II',
                        'Misa Minggu Siang',
                        'Misa Minggu Sore',
                        'Misa Minggu Malam'
                    ],
                ],
            ],
        ];

        $totalCreated = 0;
        $totalAssignments = 0;

        foreach ($picGroupData as $dutyName => $picUsers) {
            $duty = $duties[$dutyName] ?? null;
            
            if (!$duty) {
                $this->command->warn("   ⚠️ Duty '{$dutyName}' tidak ditemukan, skip...");
                continue;
            }

            $this->command->newLine();
            $this->command->info("📌 DUTY: {$dutyName}");
            $this->command->info('----------------------------------------');

            foreach ($picUsers as $picData) {
                // Cari atau buat user
                $user = User::updateOrCreate(
                    ['email' => $picData['email']],
                    [
                        'name' => $picData['name'],
                        'password' => Hash::make('password'),
                        'phone' => $picData['phone'],
                        'level_akses' => 'pic_group',
                        'duty_id' => $duty->id,
                        'is_active' => true,
                    ]
                );
                
                $user->syncRoles(['pic_group']);
                $totalCreated++;
                
                $this->command->info("   ✅ PIC: {$picData['name']}");
                $this->command->info("      Email: {$picData['email']} / password");
                $this->command->info("      Duty: {$dutyName}");

                // Assign ke schedule yang ditentukan
                $assignedSchedules = [];
                foreach ($picData['schedules'] as $scheduleName) {
                    $schedule = $schedules->where('name', $scheduleName)->first();
                    
                    if ($schedule) {
                        // Buat duty assignment
                        $assignment = DutyAssignment::updateOrCreate(
                            [
                                'schedule_id' => $schedule->id,
                                'duty_id' => $duty->id,
                                'user_id' => $user->id,
                            ],
                            [
                                'status' => 'accepted',
                                'availability_status' => 'available',
                                'event_date' => $schedule->date ?? now()->addDays(7),
                                'notes' => "PIC Group untuk {$dutyName} di {$scheduleName}",
                            ]
                        );
                        
                        $assignedSchedules[] = $scheduleName;
                        $totalAssignments++;
                    } else {
                        $this->command->warn("      ⚠️ Schedule '{$scheduleName}' tidak ditemukan");
                    }
                }

                if (!empty($assignedSchedules)) {
                    $this->command->info("      📋 Jadwal: " . implode(', ', $assignedSchedules));
                }
                
                $this->command->newLine();
            }
        }

        // ============================================
        // 5. RINGKASAN
        // ============================================
        $this->command->newLine();
        $this->command->info('========================================');
        $this->command->info('📊 RINGKASAN PIC GROUP');
        $this->command->info('========================================');
        $this->command->info("   ✅ PIC Group dibuat: {$totalCreated} user");
        $this->command->info("   ✅ Assignment dibuat: {$totalAssignments}");
        $this->command->info("   📋 Total PIC Group: " . User::where('level_akses', 'pic_group')->count());
        $this->command->info("   📋 Total Assignment: " . DutyAssignment::where('status', 'accepted')->count());
        
        $this->command->newLine();
        $this->command->info('📋 DETAIL PER DUTY:');
        
        $dutyGroups = User::where('level_akses', 'pic_group')
                          ->with('duty')
                          ->get()
                          ->groupBy('duty.name');
        
        foreach ($dutyGroups as $dutyName => $users) {
            $this->command->info("   {$dutyName}: {$users->count()} PIC");
            foreach ($users as $user) {
                $schedules = $user->dutyAssignments()
                                  ->with('schedule')
                                  ->get()
                                  ->pluck('schedule.name')
                                  ->implode(', ');
                $this->command->info("      - {$user->name}: {$schedules}");
            }
        }
        
        $this->command->newLine();
        $this->command->info('✅ SEEDER PIC GROUP SELESAI');
    }
}