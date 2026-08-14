<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus constraint unique yang lama
        DB::statement('ALTER TABLE duty_assignments DROP INDEX uk_assignment_unique');
        
        // Tambahkan unique baru dengan event_date
        DB::statement('ALTER TABLE duty_assignments ADD UNIQUE INDEX uk_assignment_unique (schedule_id, duty_id, user_id, event_date)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE duty_assignments DROP INDEX uk_assignment_unique');
        DB::statement('ALTER TABLE duty_assignments ADD UNIQUE INDEX uk_assignment_unique (schedule_id, duty_id, user_id)');
    }
};