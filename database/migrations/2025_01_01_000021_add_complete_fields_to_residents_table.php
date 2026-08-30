<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            // System & Government IDs
            $table->string('resident_id_number')->nullable()->after('id')->unique();
            $table->string('barangay_card_id')->nullable()->after('resident_id_number');
            $table->string('national_id_number')->nullable()->after('barangay_card_id');
            $table->string('voters_precinct_number')->nullable()->after('national_id_number');

            // Demographics
            $table->string('birth_place')->nullable()->after('birthdate');
            $table->string('citizenship')->default('Filipino')->after('sex');
            $table->string('blood_type')->nullable()->after('citizenship');
            $table->string('religion')->nullable()->after('blood_type');

            // Address & Housing
            $table->string('street_address')->nullable()->after('purok');
            $table->string('residency_status')->default('homeowner')->after('street_address');
            $table->integer('length_of_residency_years')->nullable()->after('residency_status');
            $table->string('previous_address')->nullable()->after('length_of_residency_years');

            // Socio-Economic
            $table->string('employment_status')->nullable()->after('occupation');
            $table->string('monthly_income_range')->nullable()->after('employment_status');
            $table->string('educational_attainment')->nullable()->after('monthly_income_range');

            // Special Sector - additional
            $table->string('type_of_disability')->nullable()->after('is_pwd');
            $table->boolean('is_indigent')->default(false)->after('is_solo_parent');

            // Emergency Contact
            $table->string('emergency_contact_name')->nullable()->after('email');
            $table->string('emergency_contact_number')->nullable()->after('emergency_contact_name');
            $table->string('emergency_relationship')->nullable()->after('emergency_contact_number');

            // Digital Attachments
            $table->string('signature_path')->nullable()->after('photo_path');
            $table->string('fingerprint_data')->nullable()->after('signature_path');
        });
    }

    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropColumn([
                'resident_id_number', 'barangay_card_id', 'national_id_number', 'voters_precinct_number',
                'birth_place', 'citizenship', 'blood_type', 'religion',
                'street_address', 'residency_status', 'length_of_residency_years', 'previous_address',
                'employment_status', 'monthly_income_range', 'educational_attainment',
                'type_of_disability', 'is_indigent',
                'emergency_contact_name', 'emergency_contact_number', 'emergency_relationship',
                'signature_path', 'fingerprint_data',
            ]);
        });
    }
};
