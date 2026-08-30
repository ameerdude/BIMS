<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds Philippine-standard resident fields:
 * - PhilSys/National ID: height, weight, tin_number
 * - Government contributions: philhealth, pag_ibig, sss numbers
 * - Family profiling: no_of_children, is_pregnant, is_lactating
 * - Vital records: is_deceased, date_of_death
 * - Misc: nickname/alias, government_id_photo_path
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            // PhilSys / National ID fields (standard PSA registration)
            $table->decimal('height_cm', 5, 1)->nullable()->after('sex');
            $table->decimal('weight_kg', 5, 1)->nullable()->after('height_cm');

            // Government IDs & contributions (required for employment/benefits)
            $table->string('tin_number')->nullable()->after('voter_id_number');
            $table->string('philhealth_number')->nullable()->after('tin_number');
            $table->string('pag_ibig_number')->nullable()->after('philhealth_number');
            $table->string('sss_number')->nullable()->after('pag_ibig_number');
            $table->string('gsis_number')->nullable()->after('sss_number');

            // Family profiling (per DILG household profiling form)
            $table->integer('no_of_children')->nullable()->after('relationship_to_head');
            $table->boolean('is_pregnant')->default(false)->after('is_solo_parent');
            $table->boolean('is_lactating')->default(false)->after('is_pregnant');

            // Vital records
            $table->boolean('is_deceased')->default(false)->after('is_active');
            $table->date('date_of_death')->nullable()->after('is_deceased');
            $table->string('cause_of_death')->nullable()->after('date_of_death');

            // Other
            $table->string('nickname')->nullable()->after('suffix');
            $table->string('government_id_photo_path')->nullable()->after('fingerprint_data');
        });
    }

    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropColumn([
                'height_cm', 'weight_kg',
                'tin_number', 'philhealth_number', 'pag_ibig_number', 'sss_number', 'gsis_number',
                'no_of_children', 'is_pregnant', 'is_lactating',
                'is_deceased', 'date_of_death', 'cause_of_death',
                'nickname', 'government_id_photo_path',
            ]);
        });
    }
};
