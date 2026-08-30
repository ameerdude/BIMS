<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BarangayId extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'resident_id',
        'id_number',
        'barcode_number',
        'version',
        'reason',
        'qr_token',
        'status',
        'issued_at',
        'issued_by',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
        ];
    }

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public static function generateIdNumber(): string
    {
        $year = date('Y');
        $count = self::count() + 1;
        return "BRGY-ID-{$year}-" . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    public static function generateBarcodeNumber(): string
    {
        // Simple numeric barcode: year + sequential number
        $year = date('Y');
        $count = self::count() + 1;
        return $year . str_pad($count, 6, '0', STR_PAD_LEFT);
    }
}
