<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('prescriptions', function (Blueprint $table){
            $table->id();
            $table->foreignId('health_worker_id')->constrained('users');
            $table->string('patient_name');
            $table->unsignedTinyInteger('patient_age')->nullable();
            $table->enum('patient_gender',['M','F','O'])->nullable();
            $table->text('chief_complaints')->nullable();
            $table->text('on_examination')->nullable();
            $table->text('advice')->nullable();
            $table->enum('status',['DRAFT','SUBMITTED','REVIEWED'])->default('DRAFT')->index();
            $table->foreignId('reviewed_by_doctor_id')->nullable()->constrained('users');
            $table->timestamps();
        });

        Schema::create('prescription_items', function (Blueprint $table){
            $table->id();
            $table->foreignId('prescription_id')->constrained('prescriptions')->onDelete('cascade');
            $table->foreignId('medicine_id')->constrained('medicines');
            $table->string('dose')->nullable();
            $table->string('frequency')->nullable();
            $table->string('duration')->nullable();
            $table->text('instructions')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('prescription_items');
        Schema::dropIfExists('prescriptions');
    }
};
