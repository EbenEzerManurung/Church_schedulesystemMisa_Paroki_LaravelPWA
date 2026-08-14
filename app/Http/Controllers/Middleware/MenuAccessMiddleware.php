<?php
// database/seeders/MenusSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use Spatie\Permission\Models\Role;

class MenusSeeder extends Seeder
{
    public function run()
    {
        // Hapus data lama
        Menu::truncate();
        
        // Definisikan semua menu
        $menus = [
            ['name' => 'Dashboard', 'url' => '/dashboard', 'route_name' => 'dashboard', 'icon' => 'fas fa-tachometer-alt', 'order' => 1],
            ['name' => 'Keuskupan', 'url' => '/keuskupans', 'route_name' => 'keuskupans.index', 'icon' => 'fas fa-church', 'order' => 2],
            ['name' => 'Gereja', 'url' => '/churches', 'route_name' => 'churches.index', 'icon' => 'fas fa-building', 'order' => 3],
            ['name' => 'Jadwal', 'url' => '/schedules', 'route_name' => 'schedules.index', 'icon' => 'fas fa-calendar-alt', 'order' => 4],
            ['name' => 'Tugas', 'url' => '/duties', 'route_name' => 'duties.index', 'icon' => 'fas fa-tasks', 'order' => 5],
            ['name' => 'Penugasan', 'url' => '/assignments', 'route_name' => 'assignments.index', 'icon' => 'fas fa-user-check', 'order' => 6],
            ['name' => 'Ketersediaan', 'url' => '/availability', 'route_name' => 'availability.index', 'icon' => 'fas fa-clock', 'order' => 7],
            ['name' => 'User Management', 'url' => '/users', 'route_name' => 'users.index', 'icon' => 'fas fa-users', 'order' => 8],
            ['name' => 'Permissions', 'url' => '/permissions', 'route_name' => 'permissions.index', 'icon' => 'fas fa-lock', 'order' => 9],
            ['name' => 'Laporan', 'url' => '/reports', 'route_name' => 'reports.index', 'icon' => 'fas fa-chart-bar', 'order' => 10],
            ['name' => 'Profile', 'url' => '/profile', 'route_name' => 'profile.index', 'icon' => 'fas fa-user-circle', 'order' => 11],
        ];
        
        foreach ($menus as $menu) {
            Menu::create($menu);
        }
        
        // Assign semua menu ke Super Admin
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $allMenus = Menu::all();
            foreach ($allMenus as $menu) {
                $menu->roles()->syncWithoutDetaching([$superAdminRole->id => [
                    'can_view' => true,
                    'can_create' => true,
                    'can_edit' => true,
                    'can_delete' => true
                ]]);
            }
        }
        
        $this->command->info('Menus seeded successfully!');
    }
}