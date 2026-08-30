<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlotterRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'blotter_number',
        'resident_id',
        'incident_type',
        'location',
        'incident_datetime',
        'narrative',
        'status',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'incident_datetime' => 'datetime',
        ];
    }

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function parties()
    {
        return $this->hasMany(BlotterParty::class);
    }

    public function mediationSchedules()
    {
        return $this->hasMany(MediationSchedule::class);
    }

    public static function generateBlotterNumber(): string
    {
        $year = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        return "BLT-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'under_mediation' => 'Under Mediation',
            'settled' => 'Settled',
            'endorsed_to_police' => 'Endorsed to Police',
            'endorsed_to_court' => 'Endorsed to Court',
            default => $this->status,
        };
    }
}
