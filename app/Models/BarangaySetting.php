<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangaySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'barangay_name',
        'municipality',
        'province',
        'region',
        'contact_number',
        'email',
        'logo_path',
        'seal_path',
        'id_validity_value',
        'id_validity_unit',
        'header_text',
        'id_card_fee',
    ];

    protected $casts = [
        'id_card_fee' => 'decimal:2',
    ];

    public static function firstOrCreateDefault(): static
    {
        return static::firstOrCreate([], [
            'barangay_name' => 'Sample Barangay',
            'municipality' => 'Sample Municipality',
            'province' => 'Sample Province',
            'id_validity_value' => 1,
            'id_validity_unit' => 'years',
        ]);
    }

    public function getIdValidityDescription(): string
    {
        $value = $this->id_validity_value ?? 1;
        $unit = $this->id_validity_unit ?? 'years';
        return $value . ' ' . str($unit)->plural($value);
    }

    public function getIdExpiryDate($issuedAt = null): ?\Carbon\Carbon
    {
        $from = $issuedAt ?? now();
        $value = $this->id_validity_value ?? 1;
        $unit = $this->id_validity_unit ?? 'years';
        return $from->copy()->add($value, $unit);
    }
}
