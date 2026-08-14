<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Keuskupan;
use App\Models\Gereja;
use App\Models\Duty;
use App\Models\Schedule;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================
        // 1. BUAT PERMISSIONS TERLEBIH DAHULU
        // ============================================
        $this->command->info('📍 MEMBUAT PERMISSIONS');
        $this->command->info('----------------------------------------');
        
        $permissions = [
            'manage_group_users',
            'view_group_users',
            'assign_duty',
            'manage_schedules',
            'view_schedules',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
            $this->command->info("   ✅ Permission: {$permission}");
        }

        // ============================================
        // 2. BUAT SEMUA ROLE
        // ============================================
        $this->command->newLine();
        $this->command->info('📍 MEMBUAT ROLE');
        $this->command->info('----------------------------------------');
        
        $roles = ['super_admin', 'admin_keuskupan', 'admin_gereja', 'pic_group', 'user'];
        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $this->command->info("   ✅ Role: {$roleName}");
        }

        // ============================================
        // 3. ASSIGN PERMISSIONS KE ROLE
        // ============================================
        $this->command->newLine();
        $this->command->info('📍 ASSIGN PERMISSIONS KE ROLE');
        $this->command->info('----------------------------------------');
        
        // PIC Group permissions
        $picGroupRole = Role::findByName('pic_group');
        $picGroupRole->givePermissionTo([
            'manage_group_users',
            'view_group_users',
            'assign_duty',
            'view_schedules',
        ]);
        $this->command->info('   ✅ PIC Group: manage_group_users, view_group_users, assign_duty, view_schedules');
        
        // Admin Gereja permissions - BISA MELIHAT SEMUA SCHEDULE
        $adminGerejaRole = Role::findByName('admin_gereja');
        $adminGerejaRole->givePermissionTo([
            'assign_duty',
            'view_schedules',
            'view_group_users',
        ]);
        $this->command->info('   ✅ Admin Gereja: assign_duty, view_schedules, view_group_users');
        
        // User permissions
        $userRole = Role::findByName('user');
        $userRole->givePermissionTo([
            'view_group_users',
            'view_schedules',
        ]);
        $this->command->info('   ✅ User: view_group_users, view_schedules');
        
        // ============================================
        // 4. AMBIL ATAU BUAT DUTIES TERLEBIH DAHULU
        // ============================================
        $this->command->newLine();
        $this->command->info('📍 MEMASTIKAN DUTIES TERSEDIA');
        $this->command->info('----------------------------------------');
        
        $dutyList = [
            ['code' => 'MIS', 'name' => 'Misdinar', 'description' => 'Pelayan altar / putra altar'],
            ['code' => 'KHO', 'name' => 'Khotbah', 'description' => 'Menyampaikan khotbah / homili'],
            ['code' => 'LEK', 'name' => 'Lektor', 'description' => 'Membaca bacaan Kitab Suci'],
            ['code' => 'KOR', 'name' => 'Koor', 'description' => 'Paduan suara / koor'],
            ['code' => 'ORG', 'name' => 'Organis', 'description' => 'Pemain organ / musik gereja'],
            ['code' => 'PKO', 'name' => 'Petugas Kolekte', 'description' => 'Mengumpulkan persembahan'],
            ['code' => 'PEM', 'name' => 'Pemazmur', 'description' => 'Memimpin nyanyian mazmur'],
            ['code' => 'PKE', 'name' => 'Petugas Kebersihan', 'description' => 'Menjaga kebersihan gereja'],
            ['code' => 'KOM', 'name' => 'Komentator', 'description' => 'Memberikan pengantar ibadah'],
            ['code' => 'SAK', 'name' => 'Sakristan', 'description' => 'Menyiapkan perlengkapan ibadah'],
            ['code' => 'PRO', 'name' => 'Prodiakon', 'description' => 'Membantu imam dalam ibadah'],
        ];
        
        $dutyMap = [];
        foreach ($dutyList as $dutyData) {
            $duty = Duty::firstOrCreate(
                ['code' => $dutyData['code']],
                [
                    'name' => $dutyData['name'],
                    'description' => $dutyData['description'],
                    'is_active' => true,
                ]
            );
            $dutyMap[$duty->name] = $duty->id;
            $this->command->info("   ✅ Duty: {$dutyData['code']} - {$dutyData['name']}");
        }
        
        // ============================================
        // 5. AMBIL SCHEDULES
        // ============================================
        $this->command->newLine();
        $this->command->info('📍 MEMASTIKAN SCHEDULES TERSEDIA');
        $this->command->info('----------------------------------------');
        
        $scheduleMap = [];
        $schedules = Schedule::all();
        foreach ($schedules as $schedule) {
            $key = $schedule->day . '_' . date('H:i', strtotime($schedule->time));
            $scheduleMap[$key] = $schedule->id;
            $this->command->info("   ✅ Schedule: {$key} - {$schedule->name}");
        }
        
        // ============================================
        // 6. SUPER ADMIN
        // ============================================
        $this->command->newLine();
        $this->command->info('📍 MEMBUAT SUPER ADMIN');
        $this->command->info('----------------------------------------');
        
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@church.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'phone' => '081234567890',
                'address' => 'Kantor Pusat',
                'is_active' => true,
                'level_akses' => 'super_admin',
                'keuskupan_id' => null,
                'gereja_id' => null,
                'duty_id' => null,
                'schedule_id' => null,
            ]
        );
        $superAdmin->syncRoles(['super_admin']);
        $this->command->info('✅ Super Admin: admin@church.com / password');
        
        // ============================================
        // 7. AMBIL KEUSKUPAN & GEREJA
        // ============================================
        $keuskupan = Keuskupan::first();
        $gereja = Gereja::first();
        
        if (!$keuskupan || !$gereja) {
            $this->command->warn('⚠️  Tidak ada keuskupan/gereja. Jalankan KeuskupanGerejaSeeder terlebih dahulu.');
            return;
        }
        
        // ============================================
        // 8. BUAT USER BIASA DENGAN DUTY_ID
        // ============================================
        $this->command->newLine();
        $this->command->info('👤 MEMBUAT USER BIASA DENGAN DUTY_ID');
        $this->command->info('----------------------------------------');
        
        $usersData = [
            ['name' => 'Jhonny', 'email' => 'jhonny@gmail.com', 'phone' => '081234567891', 'duty_name' => 'Misdinar'],
            ['name' => 'Budi', 'email' => 'budi@gmail.com', 'phone' => '081234567892', 'duty_name' => 'Khotbah'],
            ['name' => 'Siti', 'email' => 'siti@gmail.com', 'phone' => '081234567893', 'duty_name' => 'Lektor'],
            ['name' => 'Eben', 'email' => 'ebenmanurung@gmail.com', 'phone' => '081234567894', 'duty_name' => 'Koor'],
            ['name' => 'Rina', 'email' => 'rina@gmail.com', 'phone' => '081234567895', 'duty_name' => 'Organis'],
            ['name' => 'Doni', 'email' => 'doni@gmail.com', 'phone' => '081234567896', 'duty_name' => 'Petugas Kolekte'],
            ['name' => 'Maya', 'email' => 'maya@gmail.com', 'phone' => '081234567897', 'duty_name' => 'Pemazmur'],
            ['name' => 'Rudi', 'email' => 'rudi@gmail.com', 'phone' => '081234567898', 'duty_name' => 'Petugas Kebersihan'],
            ['name' => 'Andi', 'email' => 'andi@gmail.com', 'phone' => '081234567899', 'duty_name' => 'Misdinar'],
            ['name' => 'Dewi', 'email' => 'dewi@gmail.com', 'phone' => '081234567900', 'duty_name' => 'Khotbah'],
            ['name' => 'Tono', 'email' => 'tono@gmail.com', 'phone' => '081234567901', 'duty_name' => 'Lektor'],
            ['name' => 'Linda', 'email' => 'linda@gmail.com', 'phone' => '081234567902', 'duty_name' => 'Koor'],
            ['name' => 'Hendra', 'email' => 'hendra@gmail.com', 'phone' => '081234567903', 'duty_name' => 'Organis'],
            ['name' => 'Yuni', 'email' => 'yuni@gmail.com', 'phone' => '081234567904', 'duty_name' => 'Petugas Kolekte'],
            ['name' => 'Roni', 'email' => 'roni@gmail.com', 'phone' => '081234567905', 'duty_name' => 'Pemazmur'],
            ['name' => 'Tina', 'email' => 'tina@gmail.com', 'phone' => '081234567906', 'duty_name' => 'Petugas Kebersihan'],
        ];
        
        foreach ($usersData as $userData) {
            $dutyId = $dutyMap[$userData['duty_name']] ?? null;
            
            if (!$dutyId) {
                $this->command->warn("   ⚠️ Duty '{$userData['duty_name']}' tidak ditemukan, skip...");
                continue;
            }
            
            $existingUser = User::where('email', $userData['email'])->first();
            
            if ($existingUser) {
                if ($existingUser->duty_id === null && $dutyId) {
                    $existingUser->update(['duty_id' => $dutyId]);
                    $this->command->info("   🔄 {$userData['name']} - duty_id ditambahkan");
                }
                continue;
            }
            
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make('password123'),
                'phone' => $userData['phone'],
                'level_akses' => 'user',
                'keuskupan_id' => $keuskupan->id,
                'gereja_id' => $gereja->id,
                'duty_id' => $dutyId,
                'schedule_id' => null,
                'is_active' => true,
                'address' => 'Jl. Gereja No. ' . rand(1, 100) . ', ' . $keuskupan->name,
            ]);
            
            $user->syncRoles(['user']);
            
            $this->command->info("   ✅ {$userData['name']} - {$userData['email']} - Duty: {$userData['duty_name']}");
        }
        
        // ============================================
        // 9. ADMIN KEUSKUPAN & GEREJA (TANPA DUTY_ID & SCHEDULE_ID)
        // ============================================
        $this->command->newLine();
        $this->command->info('👤 MEMBUAT ADMIN KEUSKUPAN & GEREJA');
        $this->command->info('----------------------------------------');
        
        $allKeuskupans = Keuskupan::all();
        
        if ($allKeuskupans->isNotEmpty()) {
            foreach ($allKeuskupans as $keuskupanItem) {
                $code = strtolower(str_replace([' ', 'Keuskupan', 'Agung'], '', $keuskupanItem->name));
                $code = str_replace(['_', '--'], '_', $code);
                
                // Admin Keuskupan (tanpa duty_id & schedule_id)
                $adminKeuskupan = User::updateOrCreate(
                    ['email' => "admin.{$code}@keuskupan.com"],
                    [
                        'name' => "Admin {$keuskupanItem->name}",
                        'password' => Hash::make('password123'),
                        'phone' => '0812' . rand(10000000, 99999999),
                        'address' => $keuskupanItem->address ?? $keuskupanItem->name,
                        'is_active' => true,
                        'level_akses' => 'admin_keuskupan',
                        'keuskupan_id' => $keuskupanItem->id,
                        'gereja_id' => null,
                        'duty_id' => null,
                        'schedule_id' => null,
                    ]
                );
                $adminKeuskupan->syncRoles(['admin_keuskupan']);
                $this->command->info("   ✅ Admin Keuskupan: admin.{$code}@keuskupan.com");
                
                // Admin Gereja (tanpa duty_id & schedule_id - BISA MELIHAT SEMUA SCHEDULE)
                $firstChurch = Gereja::where('keuskupan_id', $keuskupanItem->id)->first();
                if ($firstChurch) {
                    $adminGereja = User::updateOrCreate(
                        ['email' => "admin.{$code}@gereja.com"],
                        [
                            'name' => "Admin {$firstChurch->nama}",
                            'password' => Hash::make('password123'),
                            'phone' => '0813' . rand(10000000, 99999999),
                            'address' => $firstChurch->lokasi ?? $firstChurch->alamat ?? $firstChurch->nama,
                            'is_active' => true,
                            'level_akses' => 'admin_gereja',
                            'keuskupan_id' => $keuskupanItem->id,
                            'gereja_id' => $firstChurch->id,
                            'duty_id' => null, // TIDAK PUNYA DUTY_ID
                            'schedule_id' => null, // TIDAK PUNYA SCHEDULE_ID - BISA MELIHAT SEMUA
                        ]
                    );
                    $adminGereja->syncRoles(['admin_gereja']);
                    $this->command->info("   ✅ Admin Gereja: admin.{$code}@gereja.com (bisa lihat semua jadwal)");
                }
            }
        }
        
        // ============================================
        // 10. BUAT PIC GROUP DENGAN SCHEDULE_ID
        // ============================================
        $this->command->newLine();
        $this->command->info('👤 MEMBUAT PIC GROUP DENGAN SCHEDULE TERTENTU');
        $this->command->info('----------------------------------------');

        $picGroupData = [
            [
                'name' => 'Samuel',
                'email' => 'samuel.koor@group.com',
                'phone' => '081234567910',
                'duty_name' => 'Koor',
                'schedule_key' => 'sabtu_17:00',
            ],
            [
                'name' => 'Erick',
                'email' => 'erick.koor@group.com',
                'phone' => '081234567920',
                'duty_name' => 'Koor',
                'schedule_key' => 'sabtu_17:00', // ERICK KHUSUS SABTU 17:00
            ],
            [
                'name' => 'Maria',
                'email' => 'maria.misdinar@group.com',
                'phone' => '081234567911',
                'duty_name' => 'Misdinar',
                'schedule_key' => 'minggu_08:30',
            ],
            [
                'name' => 'Petrus',
                'email' => 'petrus.lektor@group.com',
                'phone' => '081234567912',
                'duty_name' => 'Lektor',
                'schedule_key' => 'minggu_11:00',
            ],
            [
                'name' => 'Agnes',
                'email' => 'agnes.organis@group.com',
                'phone' => '081234567913',
                'duty_name' => 'Organis',
                'schedule_key' => 'minggu_06:00',
            ],
            [
                'name' => 'Doni',
                'email' => 'doni.kolekte@group.com',
                'phone' => '081234567914',
                'duty_name' => 'Petugas Kolekte',
                'schedule_key' => 'minggu_16:30',
            ],
            [
                'name' => 'Maya',
                'email' => 'maya.pemazmur@group.com',
                'phone' => '081234567915',
                'duty_name' => 'Pemazmur',
                'schedule_key' => 'minggu_19:00',
            ],
            [
                'name' => 'Rudi',
                'email' => 'rudi.kebersihan@group.com',
                'phone' => '081234567916',
                'duty_name' => 'Petugas Kebersihan',
                'schedule_key' => 'sabtu_17:00',
            ],
        ];

        foreach ($picGroupData as $picData) {
            $dutyId = $dutyMap[$picData['duty_name']] ?? null;
            
            if (!$dutyId) {
                $this->command->warn("   ⚠️ Duty '{$picData['duty_name']}' tidak ditemukan, skip...");
                continue;
            }

            $scheduleId = null;
            if ($picData['schedule_key']) {
                $scheduleId = $scheduleMap[$picData['schedule_key']] ?? null;
                if (!$scheduleId) {
                    $this->command->warn("   ⚠️ Schedule '{$picData['schedule_key']}' tidak ditemukan, skip...");
                    continue;
                }
            }

            // PASTIKAN PASSWORD BENAR
            $user = User::updateOrCreate(
                ['email' => $picData['email']],
                [
                    'name' => $picData['name'],
                    'password' => Hash::make('password123'), // PASTIKAN PASSWORD = 'password'
                    'phone' => $picData['phone'],
                    'level_akses' => 'pic_group',
                    'keuskupan_id' => $keuskupan->id,
                    'gereja_id' => $gereja->id,
                    'duty_id' => $dutyId,
                    'schedule_id' => $scheduleId,
                    'is_active' => true,
                    'address' => 'Gereja ' . $gereja->nama . ' - PIC ' . $picData['duty_name'],
                ]
            );
            
            $user->syncRoles(['pic_group']);
            
            $scheduleText = $scheduleId ? 'Schedule: ' . $picData['schedule_key'] : 'Tanpa schedule spesifik';
            $this->command->info("   ✅ PIC Group: {$picData['name']} - Duty: {$picData['duty_name']} ({$scheduleText})");
            $this->command->info("      Email: {$picData['email']} / password");
        }
        
        // ============================================
        // 11. RINGKASAN
        // ============================================
        $this->command->newLine();
        $this->command->info('========================================');
        $this->command->info('📊 RINGKASAN');
        $this->command->info('========================================');
        $this->command->info('Total Users: ' . User::count());
        $this->command->info('User dengan duty_id: ' . User::whereNotNull('duty_id')->count());
        $this->command->info('User dengan schedule_id: ' . User::whereNotNull('schedule_id')->count());
        
        $this->command->newLine();
        $this->command->info('📈 DETAIL PER LEVEL AKSES:');
        $this->command->info('   Super Admin: ' . User::where('level_akses', 'super_admin')->count());
        $this->command->info('   Admin Keuskupan: ' . User::where('level_akses', 'admin_keuskupan')->count());
        $this->command->info('   Admin Gereja: ' . User::where('level_akses', 'admin_gereja')->count());
        $this->command->info('   PIC Group: ' . User::where('level_akses', 'pic_group')->count());
        $this->command->info('   User Biasa: ' . User::where('level_akses', 'user')->count());
        
        $this->command->newLine();
        $this->command->info('🔑 Semua user menggunakan password: password');
        $this->command->info('✅ SEEDER USER SELESAI');
    }
}