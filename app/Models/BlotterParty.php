<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlotterParty extends Model
{
    use HasFactory;

    protected $fillable = [
        'blotter_record_id',
        'resident_id',
        'role',
        'name',
        'address',
        'contact_number',
        'contact',
    ];

    public function blotterRecord()
    {
        return $this->belongsTo(BlotterRecord::class);
    }

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }
}
