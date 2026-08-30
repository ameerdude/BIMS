<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('officials', function (Blueprint $table) {
            $table->string('contact_number')->nullable()->after('position');
            $table->string('email')->nullable()->after('contact_number');
            $table->string('photo_path')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('officials', function (Blueprint $table) {
            $table->dropColumn(['contact_number', 'email', 'photo_path']);
        });
    }
};
