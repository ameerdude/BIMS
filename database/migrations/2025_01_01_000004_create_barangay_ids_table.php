<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('barangay_ids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->constrained();
            $table->string('id_number')->unique();
            $table->integer('version')->default(1);
            $table->string('reason')->nullable();
            $table->string('qr_token')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('issued_at');
            $table->foreignId('issued_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('barangay_ids'); }
};
