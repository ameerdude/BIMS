<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('document_templates', function (Blueprint $table) {
            $table->string('orientation')->default('portrait')->after('copies'); // portrait or landscape
            $table->string('paper_size')->default('letter')->after('orientation'); // letter, legal, a4
        });

        Schema::table('barangay_ids', function (Blueprint $table) {
            $table->string('barcode_number')->nullable()->after('id_number');
        });
    }

    public function down(): void {
        Schema::table('document_templates', function (Blueprint $table) {
            $table->dropColumn(['orientation', 'paper_size']);
        });
        Schema::table('barangay_ids', function (Blueprint $table) {
            $table->dropColumn('barcode_number');
        });
    }
};
