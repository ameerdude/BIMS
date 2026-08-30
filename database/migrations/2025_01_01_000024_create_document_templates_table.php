<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // barangay_clearance, certificate_of_residency, etc.
            $table->string('label'); // Human-readable name
            $table->boolean('is_active')->default(true);
            $table->text('body_template')->nullable(); // Customizable document body text
            $table->text('footer_text')->nullable(); // Custom footer
            $table->string('fee')->nullable(); // e.g., "₱50.00"
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('document_templates'); }
};
