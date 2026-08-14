<?php
// database/seeders/RolesAndPermissionsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Schema;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Cek apakah tabel permissions ada
        if (!Schema::hasTable('permissions')) {
            $this->command->error('❌ Tabel permissions tidak ditemukan!');
            $this->command->info('Jalankan: php artisan vendor:publish --provider="Spatie\\Permission\\PermissionServiceProvider" --tag="migrations"');
            $this->command->info('Kemudian: php artisan migrate');
            return;
        }

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat permissions
        $permissions = [
            'manage_group_users',
            'view_group_users',
            'assign_duty',
            'manage_schedules',
            'view_schedules',
        ];

        $this->command->info('📍 MEMBUAT PERMISSIONS');
        $this->command->info('----------------------------------------');
        
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
            $this->command->info("   ✅ Permission: {$permission}");
        }

        // Buat roles
        $this->command->newLine();
        $this->command->info('📍 MEMBUAT ROLES');
        $this->command->info('----------------------------------------');
        
        $roles = ['super_admin', 'admin_keuskupan', 'admin_gereja', 'pic_group', 'user'];
        
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $this->command->info("   ✅ Role: {$roleName}");
        }

        // Assign permissions ke roles
        $this->command->newLine();
        $this->command->info('📍 ASSIGN PERMISSIONS KE ROLE');
        $this->command->info('----------------------------------------');
        
        // PIC Group
        $picGroupRole = Role::findByName('pic_group', 'web');
        $picGroupRole->syncPermissions([
            'manage_group_users',
            'view_group_users',
            'assign_duty',
            'view_schedules',
        ]);
        $this->command->info('   ✅ PIC Group: manage_group_users, view_group_users, assign_duty, view_schedules');
        
        // User
        $userRole = Role::findByName('user', 'web');
        $userRole->syncPermissions([
            'view_group_users',
            'view_schedules',
        ]);
        $this->command->info('   ✅ User: view_group_users, view_schedules');
        
        // Super Admin dapat semua
        $superAdminRole = Role::findByName('super_admin', 'web');
        $superAdminRole->syncPermissions(Permission::all());
        $this->command->info('   ✅ Super Admin: semua permissions');
        
        // Admin Keuskupan
        $adminKeuskupanRole = Role::findByName('admin_keuskupan', 'web');
        $adminKeuskupanRole->syncPermissions([
            'manage_group_users',
            'view_group_users',
            'assign_duty',
            'manage_schedules',
            'view_schedules',
        ]);
        $this->command->info('   ✅ Admin Keuskupan: semua permissions');
        
        // Admin Gereja
        $adminGerejaRole = Role::findByName('admin_gereja', 'web');
        $adminGerejaRole->syncPermissions([
            'manage_group_users',
            'view_group_users',
            'assign_duty',
            'manage_schedules',
            'view_schedules',
        ]);
        $this->command->info('   ✅ Admin Gereja: semua permissions');
        
        $this->command->newLine();
        $this->command->info('✅ SEEDER ROLE DAN PERMISSION SELESAI');
    }
}