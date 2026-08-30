<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('meeting_minutes', function (Blueprint $table) {
            $table->id();
            $table->string('meeting_number')->unique();
            $table->string('type')->default('regular');
            $table->date('meeting_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('venue')->nullable();
            $table->text('agenda');
            $table->text('minutes_content');
            $table->text('resolutions')->nullable();
            $table->text('attendees')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('meeting_minutes'); }
};
