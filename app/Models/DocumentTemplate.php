<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug', 'label', 'is_active', 'fee', 'validity_value', 'validity_unit', 'sort_order',
        'header_line_1', 'header_line_2', 'header_line_3', 'header_line_4',
        'show_logo', 'show_seal',
        'body_template', 'body_paragraphs', 'footer_text',
        'prepared_by_title', 'approved_by_title',
        'show_qr_code', 'show_control_number', 'watermark_text', 'copies',
        'orientation', 'paper_size',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_logo' => 'boolean',
        'show_seal' => 'boolean',
        'show_qr_code' => 'boolean',
        'show_control_number' => 'boolean',
        'body_paragraphs' => 'array',
        'copies' => 'integer',
    ];

    /**
     * Return CSS dimensions for the paper size and orientation.
     */
    public function getPaperDimensions(): array
    {
        $sizes = [
            'letter' => ['portrait' => ['w' => '8.5in', 'h' => '11in'], 'landscape' => ['w' => '11in', 'h' => '8.5in']],
            'legal'  => ['portrait' => ['w' => '8.5in', 'h' => '14in'], 'landscape' => ['w' => '14in', 'h' => '8.5in']],
            'a4'     => ['portrait' => ['w' => '210mm', 'h' => '297mm'], 'landscape' => ['w' => '297mm', 'h' => '210mm']],
        ];
        $orientation = $this->orientation ?? 'portrait';
        $paperSize = $this->paper_size ?? 'letter';
        return $sizes[$paperSize][$orientation] ?? $sizes['letter']['portrait'];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function getValidityDescription(): string
    {
        $value = $this->validity_value ?? 6;
        $unit = $this->validity_unit ?? 'months';
        return $value . ' ' . str($unit)->plural($value);
    }

    public function getExpiryDate($issuedAt = null): ?\Carbon\Carbon
    {
        $from = $issuedAt ?? now();
        $value = $this->validity_value ?? 6;
        $unit = $this->validity_unit ?? 'months';
        return $from->copy()->add($value, $unit);
    }

    /**
     * Replace placeholder tokens in template text.
     * Available placeholders:
     *  {{full_name}}, {{first_name}}, {{last_name}},
     *  {{sex}}, {{civil_status}}, {{purok}}, {{barangay}}, {{municipality}}, {{province}},
     *  {{purpose}}, {{control_number}}, {{date_issued}}, {{date_today}},
     *  {{prepared_by}}, {{approved_by}}, {{age}}
     */
    public function renderBody(array $data = []): string
    {
        $paragraphs = $this->body_paragraphs ?? [];
        $output = '';

        foreach ($paragraphs as $paragraph) {
            $text = $paragraph['text'] ?? $paragraph;
            $text = $this->parseMarkdown($text);
            $text = $this->replacePlaceholders($text, $data);
            $output .= '<p>' . $text . '</p>';
        }

        // Also process the legacy body_template field
        if ($this->body_template && empty($paragraphs)) {
            $output = '<p>' . $this->parseMarkdown($this->body_template) . '</p>';
            $output = $this->replacePlaceholders($output, $data);
        }

        return $output;
    }

    /**
     * Parse simple markdown: **bold** → <strong>, *italic* → <em>
     * Also auto-bolds placeholder tokens like {{full_name}}
     */
    protected function parseMarkdown(string $text): string
    {
        // **bold** → <strong>
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
        // *italic* → <em>
        $text = preg_replace('/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/', '<em>$1</em>', $text);
        // Auto-bold placeholder tokens: {{token}} → <strong>{{token}}</strong>
        $text = preg_replace('/\{\{([a-z_]+)\}\}/', '<strong>{{$1}}</strong>', $text);
        return $text;
    }

    public function renderHeader(array $data = []): string
    {
        $lines = [];
        if ($this->header_line_1) $lines[] = $this->replacePlaceholders($this->header_line_1, $data);
        if ($this->header_line_2) $lines[] = $this->replacePlaceholders($this->header_line_2, $data);
        if ($this->header_line_3) $lines[] = $this->replacePlaceholders($this->header_line_3, $data);
        if ($this->header_line_4) $lines[] = $this->replacePlaceholders($this->header_line_4, $data);

        return implode("\n", $lines);
    }

    public function renderFooter(array $data = []): string
    {
        if ($this->footer_text) {
            return $this->replacePlaceholders($this->footer_text, $data);
        }
        return '';
    }

    protected function replacePlaceholders(string $text, array $data): string
    {
        $replacements = [
            '{{full_name}}' => $data['full_name'] ?? '_______________',
            '{{first_name}}' => $data['first_name'] ?? '',
            '{{last_name}}' => $data['last_name'] ?? '',
            '{{sex}}' => ucfirst($data['sex'] ?? ''),
            '{{civil_status}}' => ucfirst($data['civil_status'] ?? ''),
            '{{purok}}' => $data['purok'] ?? 'Purok ___',
            '{{barangay}}' => $data['barangay'] ?? 'Barangay ___',
            '{{municipality}}' => $data['municipality'] ?? 'Municipality',
            '{{province}}' => $data['province'] ?? 'Province',
            '{{purpose}}' => $data['purpose'] ?? '',
            '{{control_number}}' => $data['control_number'] ?? '',
            '{{date_issued}}' => $data['date_issued'] ?? date('F d, Y'),
            '{{date_today}}' => $data['date_today'] ?? date('F d, Y'),
            '{{prepared_by}}' => $data['prepared_by'] ?? '_______________',
            '{{approved_by}}' => $data['approved_by'] ?? '_______________',
            '{{age}}' => $data['age'] ?? '',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }
}
