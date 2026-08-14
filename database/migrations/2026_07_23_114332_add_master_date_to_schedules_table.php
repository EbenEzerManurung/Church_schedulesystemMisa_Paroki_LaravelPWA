<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->date('master_date')->nullable()->after('time')->comment('Tanggal master untuk perhitungan kelipatan +7 hari');
            $table->index('master_date');
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropIndex(['master_date']);
            $table->dropColumn('master_date');
        });
    }
};