<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds DILG Household Profiling fields per Barangay Profile System (BPS):
 * - Head of household reference
 * - Water & sanitation (Level I-III, toilet type)
 * - Housing materials (roof, wall, floor)
 * - Utilities (electricity source, waste disposal)
 * - Economic (income, dwelling ownership, lot/floor area)
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('households', function (Blueprint $table) {
            // Head of household
            $table->foreignId('head_resident_id')->nullable()->after('id')->constrained('residents')->nullOnDelete();
            $table->string('head_name')->nullable()->after('head_resident_id');
            $table->integer('member_count')->default(1)->after('head_name');
            $table->boolean('is_4ps')->default(false)->after('member_count');

            // Water & Sanitation (per DILG/DOH classification)
            $table->string('water_source')->nullable()->after('sitio');
            // Options: level_1_faucet, level_2_faucet, level_3_faucet, deep_well, spring, rainwater, delivered, none
            $table->string('toilet_type')->nullable()->after('water_source');
            // Options: flush_to_sewer, flush_to_septic, flush_to_pit, ventilated_pit, pit_privy, none

            // Housing Materials
            $table->string('roof_material')->nullable()->after('toilet_type');
            // Options: concrete, galvanized_iron, wood, nipa, bamboo, mixed, other
            $table->string('wall_material')->nullable()->after('roof_material');
            // Options: concrete, hollow_blocks, wood, bamboo, sawali, mixed, other
            $table->string('floor_material')->nullable()->after('wall_material');
            // Options: concrete, wood, tile, dirt, bamboo, other

            // Utilities
            $table->string('electricity_source')->nullable()->after('floor_material');
            // Options: nabcor, electric_coop, private_company, generator, solar, none
            $table->string('waste_disposal')->nullable()->after('electricity_source');
            // Options: collected, burned, composted, thrown, recycled, other

            // Dwelling & Economic
            $table->string('dwelling_ownership')->nullable()->after('waste_disposal');
            // Options: owned, rented, provided, informal
            $table->decimal('lot_area_sqm', 10, 2)->nullable()->after('dwelling_ownership');
            $table->decimal('floor_area_sqm', 10, 2)->nullable()->after('lot_area_sqm');
            $table->decimal('annual_income_estimate', 12, 2)->nullable()->after('floor_area_sqm');
            $table->boolean('has_livestock')->default(false)->after('annual_income_estimate');
        });
    }

    public function down(): void
    {
        Schema::table('households', function (Blueprint $table) {
            $table->dropForeign(['head_resident_id']);
            $table->dropColumn([
                'head_resident_id', 'head_name', 'member_count', 'is_4ps',
                'water_source', 'toilet_type',
                'roof_material', 'wall_material', 'floor_material',
                'electricity_source', 'waste_disposal',
                'dwelling_ownership', 'lot_area_sqm', 'floor_area_sqm',
                'annual_income_estimate', 'has_livestock',
            ]);
        });
    }
};
