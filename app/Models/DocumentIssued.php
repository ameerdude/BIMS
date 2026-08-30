<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DocumentIssued extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'documents_issued';

    protected $fillable = [
        'resident_id',
        'document_type',
        'control_number',
        'issued_by',
        'issued_at',
        'purpose',
        'remarks',
        'qr_token',
        'status',
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

    public static function generateControlNumber(string $type): string
    {
        $typeCode = match($type) {
            'barangay_clearance' => 'CLR',
            'certificate_of_residency' => 'RES',
            'certificate_of_indigency' => 'IND',
            'business_clearance' => 'BIZ',
            default => 'DOC',
        };
        $year = date('Y');
        $count = self::whereYear('issued_at', $year)->where('document_type', $type)->count() + 1;
        return "BRGY-{$typeCode}-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public static function generateQrToken(): string
    {
        return Str::random(32) . time();
    }

    public function getDocumentTypeLabel(): string
    {
        return match($this->document_type) {
            'barangay_clearance' => 'Barangay Clearance',
            'certificate_of_residency' => 'Certificate of Residency',
            'certificate_of_indigency' => 'Certificate of Indigency',
            'certificate_of_good_moral' => 'Certificate of Good Moral Character',
            'business_clearance' => 'Business Clearance',
            default => str_replace('_', ' ', ucfirst($this->document_type)),
        };
    }
}
