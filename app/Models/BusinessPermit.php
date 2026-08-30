<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessPermit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'permit_number',
        'valid_from',
        'valid_until',
        'is_renewed',
        'issued_by',
    ];

    protected function casts(): array
    {
        return [
            'valid_from' => 'date',
            'valid_until' => 'date',
            'is_renewed' => 'boolean',
        ];
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function isExpiringSoon(): bool
    {
        return $this->valid_until->diffInDays(now()) <= 30;
    }
}
