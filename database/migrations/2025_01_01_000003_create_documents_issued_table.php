<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('documents_issued', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->constrained();
            $table->string('document_type');
            $table->string('control_number')->unique();
            $table->foreignId('issued_by')->nullable()->constrained('users');
            $table->timestamp('issued_at');
            $table->text('purpose')->nullable();
            $table->text('remarks')->nullable();
            $table->string('qr_token')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('documents_issued'); }
};
