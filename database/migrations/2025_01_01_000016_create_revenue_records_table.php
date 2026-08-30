<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('revenue_records', function (Blueprint $table) {
            $table->id();
            $table->string('or_number')->unique();
            $table->string('category');
            $table->string('description')->nullable();
            $table->foreignId('payer_id')->nullable()->constrained('residents');
            $table->string('payer_name');
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->string('payment_method')->default('cash');
            $table->foreignId('received_by')->nullable()->constrained('users');
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('revenue_records'); }
};
