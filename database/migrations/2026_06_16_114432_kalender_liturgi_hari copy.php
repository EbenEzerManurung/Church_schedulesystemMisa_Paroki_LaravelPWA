<?php
// database/migrations/2026_01_15_create_kalender_liturgi_hari_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kalender_liturgi_hari', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->string('keterangan_hari');
            $table->string('warna_liturgi');
            $table->text('bacaan1')->nullable();
            $table->text('mazmur_tanggapan')->nullable();
            $table->text('bait_pengantarinjil')->nullable();
            $table->text('bacaan_injil')->nullable();
            $table->text('catatan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kalender_liturgi_hari');
    }
};