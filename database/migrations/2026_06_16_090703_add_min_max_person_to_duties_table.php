<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('duties', function (Blueprint $table) {
            $table->integer('min_person')->default(1)->after('description');
            $table->integer('max_person')->nullable()->after('min_person');
            $table->index(['min_person', 'max_person']);
        });
    }

    public function down()
    {
        Schema::table('duties', function (Blueprint $table) {
            $table->dropColumn(['min_person', 'max_person']);
        });
    }
};