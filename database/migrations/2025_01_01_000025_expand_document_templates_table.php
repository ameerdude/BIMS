<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('document_templates', function (Blueprint $table) {
            $table->text('header_line_1')->nullable()->after('label'); // "Republic of the Philippines"
            $table->text('header_line_2')->nullable()->after('header_line_1'); // Municipality
            $table->text('header_line_3')->nullable()->after('header_line_2'); // Province
            $table->text('header_line_4')->nullable()->after('header_line_3'); // "Barangay Name" or star-decorated
            $table->boolean('show_logo')->default(true)->after('header_line_4');
            $table->boolean('show_seal')->default(true)->after('show_logo');
            $table->text('body_paragraphs')->nullable()->after('body_template'); // JSON array of paragraphs with placeholders
            $table->text('prepared_by_title')->nullable()->default('Barangay Staff')->after('footer_text');
            $table->text('approved_by_title')->nullable()->default('Punong Barangay')->after('prepared_by_title');
            $table->boolean('show_qr_code')->default(true)->after('approved_by_title');
            $table->boolean('show_control_number')->default(true)->after('show_qr_code');
            $table->text('watermark_text')->nullable()->after('show_control_number');
            $table->integer('copies')->default(1)->after('watermark_text'); // number of copies to print
        });
    }
    public function down(): void {
        Schema::table('document_templates', function (Blueprint $table) {
            $table->dropColumn([
                'header_line_1', 'header_line_2', 'header_line_3', 'header_line_4',
                'show_logo', 'show_seal', 'body_paragraphs',
                'prepared_by_title', 'approved_by_title',
                'show_qr_code', 'show_control_number', 'watermark_text', 'copies'
            ]);
        });
    }
};
