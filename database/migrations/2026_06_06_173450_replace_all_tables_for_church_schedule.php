<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // ==============================================
        // DROP EXISTING TABLES IF THEY EXIST
        // ==============================================
        $tables = [
            'assignment_logs',
            'notifications',
            'menu_role',
            'menus',
            'substitutions',
            'duty_assignments',
            'duties',
            'schedules',
            'services',
            'model_has_roles',
            'roles',
            'users',
            'keuskupans',      // Tambahkan
            'gerejas',         // Tambahkan
        ];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::drop($table);
                echo "Dropped table: {$table}\n";
            }
        }
        
        // ==============================================
        // CREATE KEUSKUPANS TABLE (sebelum users)
        // ==============================================
        DB::statement("
            CREATE TABLE `keuskupans` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NOT NULL,
                `code` VARCHAR(50) NULL,
                `address` TEXT NULL,
                `phone` VARCHAR(20) NULL,
                `email` VARCHAR(255) NULL,
                `is_active` BOOLEAN DEFAULT TRUE,
                `created_at` TIMESTAMP NULL,
                `updated_at` TIMESTAMP NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_keuskupans_name` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created table: keuskupans\n";
// ==============================================
// CREATE GEREJAS TABLE (sebelum users)
// ==============================================
DB::statement("
    CREATE TABLE `gerejas` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `keuskupan_id` BIGINT UNSIGNED NOT NULL,
        `nama` VARCHAR(255) NOT NULL,
        `kode` VARCHAR(50) NULL,
        `alamat` TEXT NULL,
        `lokasi` TEXT NULL,
        `telepon` VARCHAR(20) NULL,
        `email` VARCHAR(255) NULL,
        `jumlah_umat` INT DEFAULT 0 COMMENT 'Jumlah umat di gereja',
        `pastor` VARCHAR(100) NULL COMMENT 'Nama pastor / imam',
        `is_active` BOOLEAN DEFAULT TRUE,
        `created_at` TIMESTAMP NULL,
        `updated_at` TIMESTAMP NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uk_gerejas_nama` (`nama`),
        UNIQUE KEY `uk_gerejas_kode` (`kode`),
        INDEX `idx_gerejas_keuskupan` (`keuskupan_id`),
        INDEX `idx_gerejas_active` (`is_active`),
        FOREIGN KEY (`keuskupan_id`) REFERENCES `keuskupans`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "Created table: gerejas\n";

// ==============================================
// CREATE PERMISSIONS TABLE
// ==============================================
DB::statement("
    CREATE TABLE `permissions` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `guard_name` VARCHAR(255) NOT NULL DEFAULT 'web',
        `created_at` TIMESTAMP NULL,
        `updated_at` TIMESTAMP NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `permissions_name_guard_name_unique` (`name`, `guard_name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "Created table: permissions\n";

// ==============================================
// CREATE MODEL HAS PERMISSIONS TABLE
// ==============================================
DB::statement("
    CREATE TABLE `model_has_permissions` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `permission_id` BIGINT UNSIGNED NOT NULL,
        `model_type` VARCHAR(255) NOT NULL,
        `model_id` BIGINT UNSIGNED NOT NULL,
        `created_at` TIMESTAMP NULL,
        `updated_at` TIMESTAMP NULL,
        PRIMARY KEY (`id`),
        INDEX `model_has_permissions_model_id_model_type_index` (`model_id`, `model_type`),
        UNIQUE KEY `model_has_permissions_permission_id_model_id_model_type_unique` (`permission_id`, `model_id`, `model_type`),
        FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "Created table: model_has_permissions\n";

// ==============================================
// CREATE ROLE HAS PERMISSIONS TABLE
// ==============================================
DB::statement("
    CREATE TABLE `role_has_permissions` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `permission_id` BIGINT UNSIGNED NOT NULL,
        `role_id` BIGINT UNSIGNED NOT NULL,
        `created_at` TIMESTAMP NULL,
        `updated_at` TIMESTAMP NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `role_has_permissions_permission_id_role_id_unique` (`permission_id`, `role_id`),
        FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
        FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "Created table: role_has_permissions\n";

        // ==============================================
        // CREATE DUTIES TABLE (sebelum users)
        // ==============================================
        DB::statement("
            CREATE TABLE `duties` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `code` VARCHAR(20) NOT NULL UNIQUE COMMENT 'Kode unik tugas',
                `name` VARCHAR(100) NOT NULL,
                `slug` VARCHAR(100) NOT NULL UNIQUE,
                `description` TEXT NULL,
                `is_active` BOOLEAN DEFAULT TRUE,
                `created_at` TIMESTAMP NULL,
                `updated_at` TIMESTAMP NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_duties_code` (`code`),
                UNIQUE KEY `uk_duties_slug` (`slug`),
                INDEX `idx_duties_active` (`is_active`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created table: duties\n";

        // ==============================================
        // CREATE SERVICES TABLE
        // ==============================================
        DB::statement("
            CREATE TABLE `services` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NOT NULL,
                `code` VARCHAR(50) NULL,
                `description` TEXT NULL,
                `is_active` BOOLEAN DEFAULT TRUE,
                `created_at` TIMESTAMP NULL,
                `updated_at` TIMESTAMP NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_services_name` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created table: services\n";

      // ==============================================
// CREATE USERS TABLE
// ==============================================
DB::statement("
    CREATE TABLE `users` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) NOT NULL UNIQUE,
        `email_verified_at` TIMESTAMP NULL,
        `password` VARCHAR(255) NOT NULL,
        `phone` VARCHAR(20) NULL,
        `address` TEXT NULL,
        `photo` VARCHAR(255) NULL,
        `is_active` BOOLEAN DEFAULT TRUE,
        `level_akses` ENUM('super_admin', 'admin_keuskupan', 'admin_gereja', 'pic_group', 'user') DEFAULT 'user',
        `keuskupan_id` BIGINT UNSIGNED NULL,
        `gereja_id` BIGINT UNSIGNED NULL,
        `duty_id` BIGINT UNSIGNED NULL,
        `schedule_id` BIGINT UNSIGNED NULL COMMENT 'Jadwal spesifik untuk PIC Group',
        `remember_token` VARCHAR(100) NULL,
        `created_at` TIMESTAMP NULL,
        `updated_at` TIMESTAMP NULL,
        PRIMARY KEY (`id`),
        INDEX `idx_users_email` (`email`),
        INDEX `idx_users_active` (`is_active`),
        INDEX `idx_users_name` (`name`),
        INDEX `idx_users_level_akses` (`level_akses`),
        INDEX `idx_users_duty_id` (`duty_id`),
        INDEX `idx_users_schedule_id` (`schedule_id`),
        FOREIGN KEY (`keuskupan_id`) REFERENCES `keuskupans`(`id`) ON DELETE SET NULL,
        FOREIGN KEY (`gereja_id`) REFERENCES `gerejas`(`id`) ON DELETE SET NULL,
        FOREIGN KEY (`duty_id`) REFERENCES `duties`(`id`) ON DELETE SET NULL,
        FOREIGN KEY (`schedule_id`) REFERENCES `schedules`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "Created table: users\n";
        // ==============================================
        // CREATE ROLES TABLE
        // ==============================================
        DB::statement("
            CREATE TABLE `roles` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NOT NULL,
                `guard_name` VARCHAR(255) NOT NULL DEFAULT 'web',
                `created_at` TIMESTAMP NULL,
                `updated_at` TIMESTAMP NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `roles_name_guard_name_unique` (`name`, `guard_name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created table: roles\n";
        
        // ==============================================
        // CREATE MODEL HAS ROLES TABLE
        // ==============================================
        DB::statement("
            CREATE TABLE `model_has_roles` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `role_id` BIGINT UNSIGNED NOT NULL,
                `model_type` VARCHAR(255) NOT NULL,
                `model_id` BIGINT UNSIGNED NOT NULL,
                `created_at` TIMESTAMP NULL,
                `updated_at` TIMESTAMP NULL,
                PRIMARY KEY (`id`),
                INDEX `model_has_roles_model_id_model_type_index` (`model_id`, `model_type`),
                UNIQUE KEY `model_has_roles_role_id_model_id_model_type_unique` (`role_id`, `model_id`, `model_type`),
                FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created table: model_has_roles\n";
        
        // ==============================================
        // CREATE SCHEDULES TABLE
        // ==============================================
        DB::statement("
            CREATE TABLE `schedules` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `gereja_id` BIGINT UNSIGNED NOT NULL,
                `service_id` BIGINT UNSIGNED NULL,
                `day` ENUM('sabtu', 'minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat') NOT NULL COMMENT 'Hari ibadah',
                `date` DATE NULL COMMENT 'Tanggal ibadah',
                `time` TIME NOT NULL COMMENT 'Jam ibadah',
                `name` VARCHAR(255) NOT NULL COMMENT 'Nama ibadah',
                `schedule_type` ENUM('morning', 'afternoon', 'evening', 'weekday', 'special') DEFAULT 'morning',
                `status` ENUM('active', 'inactive', 'cancelled', 'completed') NOT NULL DEFAULT 'active' COMMENT 'Status jadwal',
                `description` TEXT NULL COMMENT 'Keterangan',
                `created_at` TIMESTAMP NULL,
                `updated_at` TIMESTAMP NULL,
                PRIMARY KEY (`id`),
                INDEX `idx_schedules_day` (`day`),
                INDEX `idx_schedules_date` (`date`),
                INDEX `idx_schedules_status` (`status`),
                INDEX `idx_schedules_gereja` (`gereja_id`),
                FOREIGN KEY (`gereja_id`) REFERENCES `gerejas`(`id`) ON DELETE CASCADE,
                FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created table: schedules\n";
        
        // ==============================================
        // CREATE DUTY ASSIGNMENTS TABLE
        // ==============================================
        DB::statement("
            CREATE TABLE `duty_assignments` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `schedule_id` BIGINT UNSIGNED NOT NULL,
                `duty_id` BIGINT UNSIGNED NOT NULL,
                `user_id` BIGINT UNSIGNED NOT NULL,
                `replacement_user_id` BIGINT UNSIGNED NULL,
                
                -- Status Penugasan
                `status` ENUM('pending', 'accepted', 'rejected', 'completed', 'cancelled') DEFAULT 'pending',
                
                -- Status Ketersediaan
                `availability_status` ENUM('pending', 'available', 'unavailable', 'replaced') DEFAULT 'pending',
                
                -- Tanggal Event (di-copy dari schedule untuk kemudahan query)
                `event_date` DATE NULL,
                
                -- Catatan dan Alasan
                `notes` TEXT NULL,
                `rejection_reason` TEXT NULL,
                `unavailable_reason` TEXT NULL,
                
                -- Replacement Request
                `replacement_request_id` BIGINT UNSIGNED NULL,
                
                -- Timestamp
                `responded_at` TIMESTAMP NULL,
                `availability_updated_at` TIMESTAMP NULL,
                `created_at` TIMESTAMP NULL,
                `updated_at` TIMESTAMP NULL,
                
                -- Primary Key
                PRIMARY KEY (`id`),
                
                -- Unique Key untuk mencegah duplikasi assignment
                UNIQUE KEY `uk_assignment_unique` (`schedule_id`, `duty_id`, `user_id`),
                
                -- Index untuk performa query
                INDEX `idx_assignments_status` (`status`),
                INDEX `idx_assignments_availability` (`availability_status`),
                INDEX `idx_assignments_event_date` (`event_date`),
                INDEX `idx_assignments_schedule_user` (`schedule_id`, `user_id`),
                INDEX `idx_assignments_duty_user` (`duty_id`, `user_id`),
                INDEX `idx_assignments_replacement` (`replacement_user_id`),
                INDEX `idx_assignments_responded` (`responded_at`),
                INDEX `idx_assignments_created` (`created_at`),
                
                -- Foreign Keys
                CONSTRAINT `fk_duty_assignments_schedule` 
                    FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`) ON DELETE CASCADE,
                CONSTRAINT `fk_duty_assignments_duty` 
                    FOREIGN KEY (`duty_id`) REFERENCES `duties` (`id`) ON DELETE CASCADE,
                CONSTRAINT `fk_duty_assignments_user` 
                    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
                CONSTRAINT `fk_duty_assignments_replacement_user` 
                    FOREIGN KEY (`replacement_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
                CONSTRAINT `fk_duty_assignments_replacement_request` 
                    FOREIGN KEY (`replacement_request_id`) REFERENCES `duty_assignments` (`id`) ON DELETE SET NULL
                
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created table: duty_assignments\n";
        
        // ==============================================
        // CREATE MENUS TABLE
        // ==============================================
        DB::statement("
            CREATE TABLE `menus` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(50) NOT NULL,
                `url` VARCHAR(100) NOT NULL,
                `icon` VARCHAR(50) NULL,
                `parent_id` BIGINT UNSIGNED NULL,
                `order` INT DEFAULT 0,
                `is_active` BOOLEAN DEFAULT TRUE,
                `created_at` TIMESTAMP NULL,
                `updated_at` TIMESTAMP NULL,
                PRIMARY KEY (`id`),
                INDEX `idx_menus_active` (`is_active`),
                INDEX `idx_menus_order` (`order`),
                FOREIGN KEY (`parent_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created table: menus\n";
        
        // ==============================================
        // CREATE MENU ROLE TABLE
        // ==============================================
        DB::statement("
            CREATE TABLE `menu_role` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `menu_id` BIGINT UNSIGNED NOT NULL,
                `role_id` BIGINT UNSIGNED NOT NULL,
                `can_view` BOOLEAN DEFAULT TRUE,
                `can_create` BOOLEAN DEFAULT FALSE,
                `can_edit` BOOLEAN DEFAULT FALSE,
                `can_delete` BOOLEAN DEFAULT FALSE,
                `created_at` TIMESTAMP NULL,
                `updated_at` TIMESTAMP NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_menu_role` (`menu_id`, `role_id`),
                FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE,
                FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created table: menu_role\n";
        
        // ==============================================
        // CREATE NOTIFICATIONS TABLE
        // ==============================================
        DB::statement("
            CREATE TABLE `notifications` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` BIGINT UNSIGNED NOT NULL,
                `title` VARCHAR(255) NOT NULL,
                `message` TEXT NOT NULL,
                `type` ENUM('substitution', 'reminder', 'alert', 'info') DEFAULT 'info',
                `is_read` BOOLEAN DEFAULT FALSE,
                `read_at` TIMESTAMP NULL,
                `created_at` TIMESTAMP NULL,
                `updated_at` TIMESTAMP NULL,
                PRIMARY KEY (`id`),
                INDEX `idx_notifications_user_read` (`user_id`, `is_read`),
                FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created table: notifications\n";
        
        // ==============================================
        // CREATE ASSIGNMENT LOGS TABLE
        // ==============================================
        DB::statement("
            CREATE TABLE `assignment_logs` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `assignment_id` BIGINT UNSIGNED NOT NULL,
                `old_status` VARCHAR(50) NULL,
                `new_status` VARCHAR(50) NULL,
                `changed_by` BIGINT UNSIGNED NULL,
                `changed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `notes` TEXT NULL,
                PRIMARY KEY (`id`),
                INDEX `idx_assignment_logs_assignment` (`assignment_id`),
                INDEX `idx_assignment_logs_changed_by` (`changed_by`),
                FOREIGN KEY (`assignment_id`) REFERENCES `duty_assignments` (`id`) ON DELETE CASCADE,
                FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created table: assignment_logs\n";
        
        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        echo "\n========================================\n";
        echo "✅ All tables created successfully!\n";
        echo "========================================\n";
    }

    public function down(): void
    {
        // This is destructive, so we don't provide a down method
    }
};