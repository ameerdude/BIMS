<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add resident_id to blotter_records for direct resident-blotter attachment
        Schema::table('blotter_records', function (Blueprint $table) {
            $table->foreignId('resident_id')->nullable()->after('blotter_number')->constrained('residents')->nullOnDelete();
        });

        // Fix blotter_parties: add resident_id, rename contact -> contact_number
        Schema::table('blotter_parties', function (Blueprint $table) {
            $table->foreignId('resident_id')->nullable()->after('blotter_record_id')->constrained('residents')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('blotter_records', function (Blueprint $table) {
            $table->dropForeign(['resident_id']);
            $table->dropColumn('resident_id');
        });
        Schema::table('blotter_parties', function (Blueprint $table) {
            $table->dropForeign(['resident_id']);
            $table->dropColumn('resident_id');
        });
    }
};
