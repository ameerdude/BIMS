<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resident extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // IDs
        'resident_id_number',
        'barangay_card_id',
        'national_id_number',
        'voters_precinct_number',
        'voter_id_number',
        // Government IDs & Contributions
        'tin_number', 'philhealth_number', 'pag_ibig_number', 'sss_number', 'gsis_number',
        // Household
        'household_id',
        'relationship_to_head',
        // Demographics
        'first_name', 'middle_name', 'last_name', 'suffix', 'nickname',
        'birthdate', 'birth_place', 'sex',
        'height_cm', 'weight_kg',
        'citizenship', 'blood_type', 'religion', 'civil_status',
        // Address
        'purok', 'street_address', 'residency_status', 'length_of_residency_years', 'previous_address',
        // Socio-Economic
        'occupation', 'employment_status', 'monthly_income_range', 'educational_attainment',
        // Sector flags
        'is_registered_voter', 'is_pwd', 'type_of_disability',
        'is_senior_citizen', 'is_4ps_beneficiary', 'is_solo_parent', 'is_indigent',
        // Family
        'no_of_children', 'is_pregnant', 'is_lactating',
        // Contact
        'contact_number', 'email',
        // Emergency
        'emergency_contact_name', 'emergency_contact_number', 'emergency_relationship',
        // Digital
        'photo_path', 'signature_path', 'fingerprint_data', 'government_id_photo_path',
        // Status
        'is_active', 'is_deceased', 'date_of_death', 'cause_of_death',
    ];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'date_of_death' => 'date',
            'height_cm' => 'decimal:1',
            'weight_kg' => 'decimal:1',
            'no_of_children' => 'integer',
            'length_of_residency_years' => 'integer',
            'is_registered_voter' => 'boolean',
            'is_pwd' => 'boolean',
            'is_senior_citizen' => 'boolean',
            'is_4ps_beneficiary' => 'boolean',
            'is_solo_parent' => 'boolean',
            'is_indigent' => 'boolean',
            'is_pregnant' => 'boolean',
            'is_lactating' => 'boolean',
            'is_active' => 'boolean',
            'is_deceased' => 'boolean',
        ];
    }

    // Auto-generate resident ID and auto-flag senior citizen on creation
    protected static function booted(): void
    {
        static::creating(function (Resident $resident) {
            if (!$resident->resident_id_number) {
                $resident->resident_id_number = self::generateResidentId();
            }
        });

        static::saving(function (Resident $resident) {
            // Auto-flag senior citizen if age >= 60
            if ($resident->birthdate && $resident->birthdate->age >= 60) {
                $resident->is_senior_citizen = true;
            }
        });
    }

    public static function generateResidentId(): string
    {
        $year = date('Y');
        $lastId = static::withTrashed()
            ->where('resident_id_number', 'like', "BGY-{$year}-%")
            ->orderByRaw("CAST(RIGHT(resident_id_number, 5) AS INTEGER) DESC")
            ->first();

        if ($lastId) {
            $lastNum = (int) substr($lastId->resident_id_number, -5);
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }

        return 'BGY-' . $year . '-' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);
    }

    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    public function documentsIssued()
    {
        return $this->hasMany(DocumentIssued::class);
    }

    public function barangayIds()
    {
        return $this->hasMany(BarangayId::class);
    }

    public function healthRecords()
    {
        return $this->hasMany(HealthRecord::class);
    }

    public function blottersAsParty()
    {
        return $this->hasMany(BlotterParty::class);
    }

    public function blotterRecords()
    {
        return $this->hasMany(BlotterRecord::class);
    }

    public function businesses()
    {
        return $this->hasMany(Business::class, 'owner_id');
    }

    public function fullName(): string
    {
        $parts = [$this->first_name];
        if ($this->middle_name) $parts[] = $this->middle_name[0] . '.';
        $parts[] = $this->last_name;
        if ($this->suffix) $parts[] = $this->suffix;
        return implode(' ', $parts);
    }

    public function displayName(): string
    {
        return $this->first_name . ' ' . $this->last_name[0] . '.';
    }

    public function getAge(): int
    {
        return $this->birthdate->age;
    }

    public function getBloodTypeLabel(): string
    {
        return $this->blood_type ?? 'N/A';
    }

    public function getResidencyStatusLabel(): string
    {
        return match($this->residency_status) {
            'homeowner' => 'Homeowner',
            'renter' => 'Renter',
            'transient' => 'Transient/Boarder',
            default => ucfirst($this->residency_status ?? 'N/A'),
        };
    }

    public function getEmploymentStatusLabel(): string
    {
        return match($this->employment_status) {
            'employed' => 'Employed',
            'unemployed' => 'Unemployed',
            'self_employed' => 'Self-Employed',
            'underemployed' => 'Underemployed',
            'student' => 'Student',
            'osy' => 'Out-of-School Youth',
            'retired' => 'Retired',
            default => ucfirst(str_replace('_', ' ', $this->employment_status ?? 'N/A')),
        };
    }

    public function getEducationLabel(): string
    {
        return match($this->educational_attainment) {
            'elementary' => 'Elementary',
            'high_school' => 'High School',
            'vocational' => 'Vocational',
            'college_undergrad' => 'College Undergraduate',
            'college_graduate' => 'College Graduate',
            'post_graduate' => 'Post-Graduate',
            default => ucfirst(str_replace('_', ' ', $this->educational_attainment ?? 'N/A')),
        };
    }

    public function getIncomeLabel(): string
    {
        return $this->monthly_income_range ?? 'N/A';
    }

    public function getSectorBadges(): array
    {
        $badges = [];
        if ($this->is_senior_citizen) $badges[] = 'Senior Citizen';
        if ($this->is_pwd) $badges[] = 'PWD' . ($this->type_of_disability ? " ({$this->type_of_disability})" : '');
        if ($this->is_solo_parent) $badges[] = 'Solo Parent';
        if ($this->is_indigent) $badges[] = 'Indigent';
        if ($this->is_4ps_beneficiary) $badges[] = '4Ps Beneficiary';
        if ($this->is_registered_voter) $badges[] = 'Registered Voter';
        return $badges;
    }
}
