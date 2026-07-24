<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table){
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable()->index();
            $table->string('email')->nullable()->index();
            $table->string('password');
            $table->enum('role',['HEALTH_WORKER','DOCTOR','ADMIN','SUPER_ADMIN'])->default('HEALTH_WORKER')->index();
            $table->enum('status',['PENDING','ACTIVE','SUSPENDED'])->default('PENDING')->index();
            $table->boolean('can_write_prescription')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('users');
    }
};
