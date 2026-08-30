<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('barangay_settings', function (Blueprint $table) {
            $table->integer('id_validity_value')->default(1)->after('seal_path');
            $table->string('id_validity_unit')->default('years')->after('id_validity_value');
        });
    }
    public function down(): void {
        Schema::table('barangay_settings', function (Blueprint $table) {
            $table->dropColumn(['id_validity_value', 'id_validity_unit']);
        });
    }
};
