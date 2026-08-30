<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('blotter_records', function (Blueprint $table) {
            $table->id();
            $table->string('blotter_number')->unique();
            $table->string('incident_type');
            $table->string('location');
            $table->datetime('incident_datetime');
            $table->text('narrative');
            $table->string('status')->default('pending');
            $table->foreignId('recorded_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('blotter_records'); }
};
