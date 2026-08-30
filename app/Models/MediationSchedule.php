<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediationSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'blotter_record_id',
        'hearing_date',
        'mediator_name',
        'outcome',
        'outcome_notes',
    ];

    protected function casts(): array
    {
        return [
            'hearing_date' => 'datetime',
        ];
    }

    public function blotterRecord()
    {
        return $this->belongsTo(BlotterRecord::class);
    }
}
