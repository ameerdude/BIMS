<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Household extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'house_number', 'street', 'purok', 'zone', 'sitio', 'full_address',
        // DILG Household Profiling
        'head_resident_id', 'head_name', 'member_count', 'is_4ps',
        // Water & Sanitation
        'water_source', 'toilet_type',
        // Housing Materials
        'roof_material', 'wall_material', 'floor_material',
        // Utilities
        'electricity_source', 'waste_disposal',
        // Dwelling & Economic
        'dwelling_ownership', 'lot_area_sqm', 'floor_area_sqm',
        'annual_income_estimate', 'has_livestock',
    ];

    protected function casts(): array
    {
        return [
            'member_count' => 'integer',
            'is_4ps' => 'boolean',
            'has_livestock' => 'boolean',
            'lot_area_sqm' => 'decimal:2',
            'floor_area_sqm' => 'decimal:2',
            'annual_income_estimate' => 'decimal:2',
        ];
    }

    public function residents()
    {
        return $this->hasMany(Resident::class);
    }

    public function head()
    {
        return $this->belongsTo(Resident::class, 'head_resident_id');
    }

    public function headName(): string
    {
        return $this->head?->fullName() ?? $this->head_name ?? 'N/A';
    }

    public function getMemberCountAttribute(): int
    {
        return $this->residents()->count();
    }
}
