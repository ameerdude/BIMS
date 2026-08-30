<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('barangay_settings', function (Blueprint $table) {
            $table->id();
            $table->string('barangay_name')->default('Sample Barangay');
            $table->string('municipality')->default('Sample Municipality');
            $table->string('province')->default('Sample Province');
            $table->string('region')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('seal_path')->nullable();
            $table->text('header_text')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('barangay_settings'); }
};
