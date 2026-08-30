<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('mediation_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blotter_record_id')->constrained()->cascadeOnDelete();
            $table->date('scheduled_date');
            $table->string('scheduled_time');
            $table->text('notes')->nullable();
            $table->string('status')->default('scheduled');
            $table->string('outcome')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('mediation_schedules'); }
};
