<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->nullable()->constrained();
            $table->string('relationship_to_head')->default('other');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->date('birthdate');
            $table->string('sex');
            $table->string('civil_status')->default('single');
            $table->string('occupation')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('purok')->nullable();
            $table->boolean('is_registered_voter')->default(false);
            $table->boolean('is_pwd')->default(false);
            $table->boolean('is_senior_citizen')->default(false);
            $table->boolean('is_4ps_beneficiary')->default(false);
            $table->boolean('is_solo_parent')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['last_name', 'first_name']);
            $table->index('purok');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};
