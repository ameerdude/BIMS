<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('document_templates', function (Blueprint $table) {
            $table->integer('validity_value')->default(6)->after('fee');
            $table->string('validity_unit')->default('months')->after('validity_value');
        });
    }
    public function down(): void {
        Schema::table('document_templates', function (Blueprint $table) {
            $table->dropColumn(['validity_value', 'validity_unit']);
        });
    }
};
