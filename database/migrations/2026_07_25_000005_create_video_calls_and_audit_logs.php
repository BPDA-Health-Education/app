<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('video_call_requests', function (Blueprint $table){
            $table->id();
            $table->foreignId('requester_id')->constrained('users');
            $table->foreignId('receiver_id')->constrained('users');
            $table->text('note')->nullable();
            $table->enum('status',['PENDING','ACCEPTED','DECLINED'])->default('PENDING')->index();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table){
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('users');
            $table->string('action');
            $table->string('target_entity_type')->nullable();
            $table->unsignedBigInteger('target_entity_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('video_call_requests');
    }
};
