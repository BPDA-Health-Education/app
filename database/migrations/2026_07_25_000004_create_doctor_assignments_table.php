<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('doctor_assignments', function (Blueprint $table){
            $table->id();
            $table->foreignId('doctor_id')->constrained('users');
            $table->foreignId('health_worker_id')->constrained('users');
            $table->timestamp('assigned_at')->useCurrent();
            $table->unique(['health_worker_id']); // ensure one-to-one active assignment
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('doctor_assignments'); }
};
