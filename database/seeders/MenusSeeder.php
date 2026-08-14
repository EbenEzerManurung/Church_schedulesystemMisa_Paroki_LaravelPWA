<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use Spatie\Permission\Models\Role;

class MenusSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================
        // 1. BUAT DATA MENU
        // ============================================
        $menus = [
            ['name' => 'Dashboard', 'url' => '/dashboard', 'icon' => 'fa-tachometer-alt', 'order' => 1, 'is_active' => true],
            ['name' => 'Jadwal Ibadah', 'url' => '/schedules', 'icon' => 'fa-calendar-alt', 'order' => 2, 'is_active' => true],
            ['name' => 'Tugas Pelayanan', 'url' => '/duties', 'icon' => 'fa-tasks', 'order' => 3, 'is_active' => true],
            ['name' => 'Penugasan', 'url' => '/assignments', 'icon' => 'fa-user-check', 'order' => 4, 'is_active' => true],
            ['name' => 'Ketersediaan', 'url' => '/availability', 'icon' => 'fa-calendar-check', 'order' => 5, 'is_active' => true],
            ['name' => 'Laporan', 'url' => '/reports', 'icon' => 'fa-chart-bar', 'order' => 6, 'is_active' => true],
            ['name' => 'Kelola User', 'url' => '/users', 'icon' => 'fa-users', 'order' => 7, 'is_active' => true],
            ['name' => 'Kelola Akses', 'url' => '/permissions', 'icon' => 'fa-lock', 'order' => 8, 'is_active' => true],
            // Menu untuk PIC Group
            ['name' => 'Group Dashboard', 'url' => '/group/dashboard', 'icon' => 'fa-group', 'order' => 9, 'is_active' => true],
            ['name' => 'Group Users', 'url' => '/group/users', 'icon' => 'fa-users', 'order' => 10, 'is_active' => true],
            ['name' => 'Group Schedules', 'url' => '/group/schedules', 'icon' => 'fa-calendar', 'order' => 11, 'is_active' => true],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(
                ['url' => $menu['url']],
                $menu
            );
        }

        $this->command->info('✅ ' . count($menus) . ' Menu berhasil dibuat');

        // ============================================
        // 2. HAPUS MENU LAMA
        // ============================================
        Menu::where('url', '/substitutions')->delete();
        $this->command->info('🗑️ Menu substitutions dihapus');

        // ============================================
        // 3. AMBIL ROLE YANG TERSEDIA
        // ============================================
        $this->command->newLine();
        $this->command->info('📍 ASSIGN MENU KE ROLE');
        $this->command->info('----------------------------------------');

        $allMenus = Menu::all();

        // Super Admin
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            foreach ($allMenus as $menu) {
                $menu->roles()->syncWithoutDetaching([
                    $superAdminRole->id => [
                        'can_view' => true,
                        'can_create' => true,
                        'can_edit' => true,
                        'can_delete' => true,
                    ]
                ]);
            }
            $this->command->info('   ✅ Super Admin: semua menu (full access)');
        }

        // Admin Keuskupan
        $adminKeuskupanRole = Role::where('name', 'admin_keuskupan')->first();
        if ($adminKeuskupanRole) {
            foreach ($allMenus as $menu) {
                $menu->roles()->syncWithoutDetaching([
                    $adminKeuskupanRole->id => [
                        'can_view' => true,
                        'can_create' => true,
                        'can_edit' => true,
                        'can_delete' => true,
                    ]
                ]);
            }
            $this->command->info('   ✅ Admin Keuskupan: semua menu (full access)');
        }

        // Admin Gereja
        $adminGerejaRole = Role::where('name', 'admin_gereja')->first();
        if ($adminGerejaRole) {
            foreach ($allMenus as $menu) {
                $menu->roles()->syncWithoutDetaching([
                    $adminGerejaRole->id => [
                        'can_view' => true,
                        'can_create' => true,
                        'can_edit' => true,
                        'can_delete' => true,
                    ]
                ]);
            }
            $this->command->info('   ✅ Admin Gereja: semua menu (full access)');
        }

        // PIC Group (hanya menu tertentu)
        $picGroupRole = Role::where('name', 'pic_group')->first();
        if ($picGroupRole) {
            $picGroupUrls = [
                '/dashboard',
                '/schedules',
                '/assignments',
                '/availability',
                '/group/dashboard',
                '/group/users',
                '/group/schedules',
            ];
            
            foreach ($allMenus as $menu) {
                if (in_array($menu->url, $picGroupUrls)) {
                    $menu->roles()->syncWithoutDetaching([
                        $picGroupRole->id => [
                            'can_view' => true,
                            'can_create' => true,
                            'can_edit' => true,
                            'can_delete' => false,
                        ]
                    ]);
                }
            }
            $this->command->info('   ✅ PIC Group: menu terbatas (view, create, edit)');
        }

        // User (hanya view)
        $userRole = Role::where('name', 'user')->first();
        if ($userRole) {
            $userUrls = [
                '/dashboard',
                '/schedules',
                '/assignments',
                '/availability',
                '/group/dashboard',
                '/group/users',
                '/group/schedules',
            ];
            
            foreach ($allMenus as $menu) {
                if (in_array($menu->url, $userUrls)) {
                    $menu->roles()->syncWithoutDetaching([
                        $userRole->id => [
                            'can_view' => true,
                            'can_create' => false,
                            'can_edit' => false,
                            'can_delete' => false,
                        ]
                    ]);
                }
            }
            $this->command->info('   ✅ User: view only');
        }

        // ============================================
        // 4. RINGKASAN
        // ============================================
        $this->command->newLine();
        $this->command->info('========================================');
        $this->command->info('📊 RINGKASAN MENU');
        $this->command->info('========================================');
        $this->command->info('Total Menu: ' . Menu::count());
        
        $this->command->newLine();
        $this->command->info('✅ SEEDER MENU SELESAI');
    }
}