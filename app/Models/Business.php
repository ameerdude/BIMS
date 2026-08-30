<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_name',
        'owner_id',
        'owner_name',
        'business_type',
        'business_address',
        'date_registered',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'date_registered' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function owner()
    {
        return $this->belongsTo(Resident::class, 'owner_id');
    }

    public function permits()
    {
        return $this->hasMany(BusinessPermit::class);
    }

    public function latestPermit()
    {
        return $this->hasOne(BusinessPermit::class)->latest();
    }
}
