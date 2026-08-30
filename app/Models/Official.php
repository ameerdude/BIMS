<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Official extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'position', 'position_category', 'term_start', 'term_end', 'is_current',
        'contact_number', 'email', 'photo_path',
    ];

    protected function casts(): array
    {
        return [
            'term_start' => 'date',
            'term_end' => 'date',
            'is_current' => 'boolean',
        ];
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }
}
