<?php
// database/migrations/2024_01_15_000001_add_keuskupan_fields_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeuskupanFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Kolom untuk hierarki organisasi
            $table->string('keuskupan_code', 50)->nullable()->after('email');
            $table->string('keuskupan_name', 100)->nullable()->after('keuskupan_code');
            $table->string('church_code', 50)->nullable()->after('keuskupan_name');
            $table->string('church_name', 100)->nullable()->after('church_code');
            
            // Kolom tambahan sesuai permintaan
            $table->string('companycd')->nullable()->after('church_name');
            $table->string('plantcd')->nullable()->after('companycd');
            $table->string('bacd')->nullable()->after('plantcd');
            $table->string('salespointcd')->nullable()->after('bacd');
            
            // Index untuk performa query
            $table->index('keuskupan_code');
            $table->index('church_code');
            $table->index('plantcd');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'keuskupan_code', 'keuskupan_name',
                'church_code', 'church_name',
                'companycd', 'plantcd', 'bacd', 'salespointcd'
            ]);
        });
    }
}