<?php
// database/migrations/2024_01_15_000004_create_menus_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    public function up()
    {
        // Cek dan buat tabel menus
        if (!Schema::hasTable('menus')) {
            Schema::create('menus', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('url')->nullable();
                $table->string('route_name')->nullable();
                $table->string('icon')->nullable();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->integer('order')->default(0);
                $table->string('permission_name')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->foreign('parent_id')->references('id')->on('menus')->onDelete('cascade');
                $table->index('route_name');
                $table->index('is_active');
            });
        }
        
        // Cek dan buat tabel menu_roles
        if (!Schema::hasTable('menu_roles')) {
            Schema::create('menu_roles', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('menu_id');
                $table->unsignedBigInteger('role_id');
                $table->boolean('can_view')->default(true);
                $table->boolean('can_create')->default(false);
                $table->boolean('can_edit')->default(false);
                $table->boolean('can_delete')->default(false);
                $table->timestamps();
                
                $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
                
                // PERBAIKAN: Cek apakah tabel roles ada sebelum membuat foreign key
                if (Schema::hasTable('roles')) {
                    $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
                } else {
                    $table->index('role_id');
                }
                
                $table->unique(['menu_id', 'role_id']);
            });
        }
    }
    
    public function down()
    {
        Schema::dropIfExists('menu_roles');
        Schema::dropIfExists('menus');
    }
}