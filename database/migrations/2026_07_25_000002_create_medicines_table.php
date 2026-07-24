<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('medicines', function (Blueprint $table){
            $table->id();
            $table->string('name')->index();
            $table->string('generic_name')->nullable()->index();
            $table->string('form')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('medicines'); }
};
