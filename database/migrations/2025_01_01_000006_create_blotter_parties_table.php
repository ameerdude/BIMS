<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('blotter_parties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blotter_record_id')->constrained()->cascadeOnDelete();
            $table->string('role');
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('contact')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('blotter_parties'); }
};
