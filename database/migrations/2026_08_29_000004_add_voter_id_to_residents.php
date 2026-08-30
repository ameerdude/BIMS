<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('residents', function (Blueprint $table) {
            $table->string('voter_id_number')->nullable()->after('voters_precinct_number');
        });
    }
    public function down(): void {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropColumn('voter_id_number');
        });
    }
};
