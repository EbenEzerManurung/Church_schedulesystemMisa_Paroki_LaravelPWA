<?php
// database/migrations/2024_01_15_000005_create_menu_roles_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuRolesTable extends Migration
{
    public function up()
    {
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
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
                $table->unique(['menu_id', 'role_id']);
            });
        }
    }
    
    public function down()
    {
        Schema::dropIfExists('menu_roles');
    }
}