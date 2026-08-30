<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Resident;
use App\Models\Household;
use App\Models\Purok;

new #[Layout("layouts.app")] class extends Component
{
    use WithFileUploads;

    // Demographics
    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $suffix = '';
    public string $nickname = '';
    public string $birthdate = '';
    public string $birth_place = '';
    public string $sex = 'male';
    public ?float $height_cm = null;
    public ?float $weight_kg = null;
    public string $citizenship = 'Filipino';
    public string $blood_type = '';
    public string $religion = '';
    public string $civil_status = 'single';
    public ?int $no_of_children = null;
    public bool $is_pregnant = false;
    public bool $is_lactating = false;

    // Household
    public ?int $household_id = null;
    public string $relationship_to_head = 'member';
    public bool $showHouseholdCreate = false;
    public string $new_hh_address = '';
    public string $new_hh_purok = '';

    // Address
    public string $purok = '';
    public string $street_address = '';
    public string $residency_status = 'homeowner';
    public ?int $length_of_residency_years = null;
    public string $previous_address = '';

    // Socio-Economic
    public string $occupation = '';
    public string $employment_status = '';
    public string $monthly_income_range = '';
    public string $educational_attainment = '';

    // Sector flags
    public bool $is_registered_voter = false;
    public bool $is_pwd = false;
    public string $type_of_disability = '';
    public bool $is_senior_citizen = false;
    public bool $is_4ps_beneficiary = false;
    public bool $is_solo_parent = false;
    public bool $is_indigent = false;

    // Contact
    public string $contact_number = '';
    public string $email = '';

    // Emergency
    public string $emergency_contact_name = '';
    public string $emergency_contact_number = '';
    public string $emergency_relationship = '';

    // IDs
    public string $barangay_card_id = '';
    public string $national_id_number = '';
    public string $voters_precinct_number = '';
    public string $voter_id_number = '';
    public string $tin_number = '';
    public string $philhealth_number = '';
    public string $pag_ibig_number = '';
    public string $sss_number = '';

    // Digital Attachments
    public $photo = null;
    public $signature = null;
    public $fingerprint_file = null;

    public function updatedIsPwd(): void
    {
        if (!$this->is_pwd) $this->type_of_disability = '';
    }

    public function createHouseholdInline(): void
    {
        $this->validate(['new_hh_address' => 'required|string|max:255']);
        $hh = Household::create([
            'full_address' => $this->new_hh_address,
            'purok' => $this->new_hh_purok ?: null,
        ]);
        $this->household_id = $hh->id;
        $this->new_hh_address = '';
        $this->new_hh_purok = '';
        $this->showHouseholdCreate = false;
    }

    public function save(): void
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthdate' => 'required|date|before:today',
            'sex' => 'required|in:male,female',
            'photo' => 'nullable|image|max:2048',
            'signature' => 'nullable|image|max:1024',
            'fingerprint_file' => 'nullable|image|max:1024',
        ]);

        $photoPath = null;
        if ($this->photo) {
            $photoPath = $this->photo->store('residents', 'public');
        }

        $signaturePath = null;
        if ($this->signature) {
            $signaturePath = $this->signature->store('residents/signatures', 'public');
        }

        $fingerprintPath = null;
        if ($this->fingerprint_file) {
            $fingerprintPath = $this->fingerprint_file->store('residents/fingerprints', 'public');
        }

        $resident = Resident::create([
            'barangay_card_id' => $this->barangay_card_id ?: null,
            'national_id_number' => $this->national_id_number ?: null,
            'voters_precinct_number' => $this->voters_precinct_number ?: null,
            'voter_id_number' => $this->voter_id_number ?: null,
            'tin_number' => $this->tin_number ?: null,
            'philhealth_number' => $this->philhealth_number ?: null,
            'pag_ibig_number' => $this->pag_ibig_number ?: null,
            'sss_number' => $this->sss_number ?: null,
            'household_id' => $this->household_id,
            'relationship_to_head' => $this->relationship_to_head,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name ?: null,
            'last_name' => $this->last_name,
            'suffix' => $this->suffix ?: null,
            'nickname' => $this->nickname ?: null,
            'birthdate' => $this->birthdate,
            'birth_place' => $this->birth_place ?: null,
            'sex' => $this->sex,
            'height_cm' => $this->height_cm,
            'weight_kg' => $this->weight_kg,
            'citizenship' => $this->citizenship,
            'blood_type' => $this->blood_type ?: null,
            'religion' => $this->religion ?: null,
            'civil_status' => $this->civil_status,
            'no_of_children' => $this->no_of_children,
            'is_pregnant' => $this->is_pregnant,
            'is_lactating' => $this->is_lactating,
            'purok' => $this->purok ?: null,
            'street_address' => $this->street_address ?: null,
            'residency_status' => $this->residency_status,
            'length_of_residency_years' => $this->length_of_residency_years,
            'previous_address' => $this->previous_address ?: null,
            'occupation' => $this->occupation ?: null,
            'employment_status' => $this->employment_status ?: null,
            'monthly_income_range' => $this->monthly_income_range ?: null,
            'educational_attainment' => $this->educational_attainment ?: null,
            'is_registered_voter' => $this->is_registered_voter,
            'is_pwd' => $this->is_pwd,
            'type_of_disability' => $this->is_pwd ? ($this->type_of_disability ?: null) : null,
            'is_4ps_beneficiary' => $this->is_4ps_beneficiary,
            'is_solo_parent' => $this->is_solo_parent,
            'is_indigent' => $this->is_indigent,
            'contact_number' => $this->contact_number ?: null,
            'email' => $this->email ?: null,
            'emergency_contact_name' => $this->emergency_contact_name ?: null,
            'emergency_contact_number' => $this->emergency_contact_number ?: null,
            'emergency_relationship' => $this->emergency_relationship ?: null,
            'photo_path' => $photoPath,
            'signature_path' => $signaturePath,
            'fingerprint_data' => $fingerprintPath,
            'is_active' => true,
        ]);

        session()->flash('success', 'Resident ' . $this->first_name . ' ' . $this->last_name . ' registered successfully! ID: ' . $resident->resident_id_number);
        $this->redirect(route('residents.index'), navigate: true);
    }
}; ?>

<div x-data="{ openSection: 'personal' }">
<div style="max-width:900px;margin:0 auto;">

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <div>
            <h1 style="font-size:1.25rem;font-weight:800;color:var(--text-primary);margin:0;">Register New Resident</h1>
            <p style="font-size:0.8125rem;color:var(--text-secondary);margin:2px 0 0;">Fill in the resident's complete information</p>
        </div>
        <a href="{{ route('residents.index') }}" wire:navigate class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back
        </a>
    </div>

    <form wire:submit="save" enctype="multipart/form-data">

    {{-- Section Tabs - Personal first, IDs last --}}
    <div class="section-tabs">
        <button type="button" @click="openSection='personal'" :class="openSection==='personal' && 'active'" class="section-tab">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Personal
        </button>
        <button type="button" @click="openSection='address'" :class="openSection==='address' && 'active'" class="section-tab">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Address
        </button>
        <button type="button" @click="openSection='socioeconomic'" :class="openSection==='socioeconomic' && 'active'" class="section-tab">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
            Socio-Economic
        </button>
        <button type="button" @click="openSection='sectors'" :class="openSection==='sectors' && 'active'" class="section-tab">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
            Sectors
        </button>
        <button type="button" @click="openSection='contact'" :class="openSection==='contact' && 'active'" class="section-tab">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
            Contact
        </button>
        <button type="button" @click="openSection='emergency'" :class="openSection==='emergency' && 'active'" class="section-tab">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            Emergency
        </button>
        <button type="button" @click="openSection='attachments'" :class="openSection==='attachments' && 'active'" class="section-tab">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            Attachments
        </button>
        <button type="button" @click="openSection='ids'" :class="openSection==='ids' && 'active'" class="section-tab">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
            IDs
        </button>
    </div>

    {{-- 👤 Personal Demographics --}}
    <div class="section-card" x-show="openSection==='personal'" x-cloak>
        <div class="section-card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Personal Demographics
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">First Name <span style="color:var(--red-600);">*</span></label>
                <input type="text" wire:model="first_name" class="form-input">
                @error('first_name') <span class="form-error">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Middle Name</label>
                <input type="text" wire:model="middle_name" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Last Name <span style="color:var(--red-600);">*</span></label>
                <input type="text" wire:model="last_name" class="form-input">
                @error('last_name') <span class="form-error">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Suffix</label>
                <input type="text" wire:model="suffix" placeholder="Jr., Sr., III" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Nickname / Alias</label>
                <input type="text" wire:model="nickname" placeholder="e.g. Jun, Boy" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Birth Date <span style="color:var(--red-600);">*</span></label>
                <input type="date" wire:model="birthdate" class="form-input">
                @error('birthdate') <span class="form-error">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Birth Place</label>
                <input type="text" wire:model="birth_place" placeholder="City/Municipality, Province" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Sex <span style="color:var(--red-600);">*</span></label>
                <select wire:model="sex" class="form-select">
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Height (cm)</label>
                <input type="number" wire:model="height_cm" step="0.1" min="0" placeholder="e.g. 165.5" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Weight (kg)</label>
                <input type="number" wire:model="weight_kg" step="0.1" min="0" placeholder="e.g. 65.0" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Civil Status</label>
                <select wire:model="civil_status" class="form-select">
                    <option value="single">Single</option>
                    <option value="married">Married</option>
                    <option value="widowed">Widowed</option>
                    <option value="separated">Separated</option>
                    <option value="cohabiting">Cohabiting (Live-in)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Citizenship</label>
                <input type="text" wire:model="citizenship" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Blood Type</label>
                <select wire:model="blood_type" class="form-select">
                    <option value="">Select...</option>
                    @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt)
                    <option value="{{ $bt }}">{{ $bt }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Religion</label>
                <input type="text" wire:model="religion" placeholder="e.g. Roman Catholic" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Household <span style="font-size:0.6875rem;color:var(--text-muted);font-weight:400;">(Optional)</span></label>
                @if($showHouseholdCreate)
                <div style="display:flex;gap:6px;">
                    <input type="text" wire:model="new_hh_address" placeholder="Full address" class="form-input" style="flex:1;">
                    <input type="text" wire:model="new_hh_purok" placeholder="Purok" class="form-input" style="width:100px;">
                    <button type="button" wire:click="createHouseholdInline" class="btn btn-sm btn-primary" style="padding:6px 12px;white-space:nowrap;">Save</button>
                    <button type="button" wire:click="$set('showHouseholdCreate', false)" class="btn btn-sm btn-outline" style="padding:6px 12px;">Cancel</button>
                </div>
                @else
                <div style="display:flex;gap:6px;">
                    <select wire:model="household_id" class="form-select" style="flex:1;">
                        <option value="">No household (optional)</option>
                        @foreach(\App\Models\Household::orderBy('full_address')->get() as $hh)
                        <option value="{{ $hh->id }}">{{ $hh->full_address }}</option>
                        @endforeach
                    </select>
                    <button type="button" wire:click="$set('showHouseholdCreate', true)" class="btn btn-sm btn-outline" style="padding:6px 10px;white-space:nowrap;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        + New
                    </button>
                </div>
                @endif
            </div>
            <div class="form-group">
                <label class="form-label">Relationship to Head</label>
                <select wire:model="relationship_to_head" class="form-select">
                    @foreach(['head','spouse','child','relative','boarder','other'] as $rel)
                    <option value="{{ $rel }}">{{ ucfirst($rel) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Number of Children</label>
                <input type="number" wire:model="no_of_children" min="0" placeholder="0" class="form-input">
            </div>
            <div x-show="$wire.sex === 'female'" x-transition>
                <label class="form-check" style="padding:8px 12px;border-radius:var(--radius);background:var(--navy-50);margin-bottom:8px;">
                    <input type="checkbox" wire:model="is_pregnant" class="w-4 h-4">
                    <span class="form-check-label">Currently Pregnant</span>
                </label>
                <label class="form-check" style="padding:8px 12px;border-radius:var(--radius);background:var(--navy-50);">
                    <input type="checkbox" wire:model="is_lactating" class="w-4 h-4">
                    <span class="form-check-label">Currently Lactating</span>
                </label>
            </div>
        </div>
    </div>

    {{-- 📍 Address --}}
    <div class="section-card" x-show="openSection==='address'" x-cloak>
        <div class="section-card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Address & Housing Logistics
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Purok / Zone / Sitio</label>
                <select wire:model="purok" class="form-select">
                    <option value="">Select Purok</option>
                    @foreach(\App\Models\Purok::active()->get() as $pk)
                    <option value="{{ $pk->name }}">{{ $pk->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Street Address</label>
                <input type="text" wire:model="street_address" placeholder="House No., Street Name" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Residency Status</label>
                <select wire:model="residency_status" class="form-select">
                    <option value="homeowner">Homeowner</option>
                    <option value="renter">Renter</option>
                    <option value="transient">Transient / Boarder</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Length of Residency (years)</label>
                <input type="number" wire:model="length_of_residency_years" min="0" class="form-input">
            </div>
            <div class="form-group" style="grid-column:span 2;">
                <label class="form-label">Previous Address (for transferees)</label>
                <input type="text" wire:model="previous_address" placeholder="City/Municipality, Province" class="form-input">
            </div>
        </div>
    </div>

    {{-- 💼 Socio-Economic --}}
    <div class="section-card" x-show="openSection==='socioeconomic'" x-cloak>
        <div class="section-card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
            Socio-Economic Profile
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Occupation</label>
                <input type="text" wire:model="occupation" placeholder="e.g. Teacher, Driver" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Employment Status</label>
                <select wire:model="employment_status" class="form-select">
                    <option value="">Select...</option>
                    <option value="employed">Employed</option>
                    <option value="unemployed">Unemployed</option>
                    <option value="self_employed">Self-Employed</option>
                    <option value="underemployed">Underemployed</option>
                    <option value="student">Student</option>
                    <option value="osy">Out-of-School Youth (OSY)</option>
                    <option value="retired">Retired</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Monthly Income Range</label>
                <select wire:model="monthly_income_range" class="form-select">
                    <option value="">Select...</option>
                    <option value="Below ₱10,000">Below ₱10,000</option>
                    <option value="₱10,000 - ₱20,000">₱10,000 - ₱20,000</option>
                    <option value="₱20,000 - ₱50,000">₱20,000 - ₱50,000</option>
                    <option value="₱50,000 - ₱100,000">₱50,000 - ₱100,000</option>
                    <option value="Above ₱100,000">Above ₱100,000</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Educational Attainment</label>
                <select wire:model="educational_attainment" class="form-select">
                    <option value="">Select...</option>
                    <option value="elementary">Elementary</option>
                    <option value="high_school">High School</option>
                    <option value="vocational">Vocational</option>
                    <option value="college_undergrad">College Undergraduate</option>
                    <option value="college_graduate">College Graduate</option>
                    <option value="post_graduate">Post-Graduate</option>
                </select>
            </div>
        </div>
    </div>

    {{-- 🏷️ Sectors --}}
    <div class="section-card" x-show="openSection==='sectors'" x-cloak>
        <div class="section-card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
            Special Sector Classifications
        </div>
        <div class="grid-2">
            <label class="form-check" style="padding:12px;border-radius:var(--radius);transition:background 0.12s;" onmouseover="this.style.background='var(--navy-50)'" onmouseout="this.style.background='transparent'">
                <input type="checkbox" wire:model.live="is_registered_voter" class="w-4 h-4">
                <span class="form-check-label">Registered Voter</span>
            </label>
            <label class="form-check" style="padding:12px;border-radius:var(--radius);transition:background 0.12s;" onmouseover="this.style.background='var(--navy-50)'" onmouseout="this.style.background='transparent'">
                <input type="checkbox" wire:model.live="is_pwd" class="w-4 h-4">
                <span class="form-check-label">PWD (Person with Disability)</span>
            </label>
            <div x-show="$wire.is_pwd" x-transition style="grid-column:span 2;padding-left:32px;">
                <div class="form-group">
                    <label class="form-label">Type of Disability</label>
                    <select wire:model="type_of_disability" class="form-select">
                        <option value="">Select type...</option>
                        <option value="Visual">Visual</option>
                        <option value="Hearing">Hearing</option>
                        <option value="Mobility">Mobility</option>
                        <option value="Learning">Learning</option>
                        <option value="Psychosocial">Psychosocial</option>
                        <option value="Speech">Speech</option>
                        <option value="Multiple">Multiple</option>
                    </select>
                </div>
            </div>
            <label class="form-check" style="padding:12px;border-radius:var(--radius);transition:background 0.12s;" onmouseover="this.style.background='var(--navy-50)'" onmouseout="this.style.background='transparent'">
                <input type="checkbox" wire:model="is_senior_citizen" class="w-4 h-4">
                <span class="form-check-label">Senior Citizen (60+)</span>
            </label>
            <label class="form-check" style="padding:12px;border-radius:var(--radius);transition:background 0.12s;" onmouseover="this.style.background='var(--navy-50)'" onmouseout="this.style.background='transparent'">
                <input type="checkbox" wire:model="is_solo_parent" class="w-4 h-4">
                <span class="form-check-label">Solo Parent</span>
            </label>
            <label class="form-check" style="padding:12px;border-radius:var(--radius);transition:background 0.12s;" onmouseover="this.style.background='var(--navy-50)'" onmouseout="this.style.background='transparent'">
                <input type="checkbox" wire:model="is_indigent" class="w-4 h-4">
                <span class="form-check-label">Indigent</span>
            </label>
            <label class="form-check" style="padding:12px;border-radius:var(--radius);transition:background 0.12s;" onmouseover="this.style.background='var(--navy-50)'" onmouseout="this.style.background='transparent'">
                <input type="checkbox" wire:model="is_4ps_beneficiary" class="w-4 h-4">
                <span class="form-check-label">4Ps Beneficiary</span>
            </label>
        </div>
    </div>

    {{-- 📞 Contact --}}
    <div class="section-card" x-show="openSection==='contact'" x-cloak>
        <div class="section-card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
            Contact Information
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Mobile Number</label>
                <input type="text" wire:model="contact_number" placeholder="09XXXXXXXXX" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" wire:model="email" placeholder="optional@email.com" class="form-input">
            </div>
        </div>
    </div>

    {{-- 🚨 Emergency --}}
    <div class="section-card" x-show="openSection==='emergency'" x-cloak>
        <div class="section-card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            Emergency Contact
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Emergency Contact Name</label>
                <input type="text" wire:model="emergency_contact_name" placeholder="Full name of next of kin" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Emergency Contact Number</label>
                <input type="text" wire:model="emergency_contact_number" placeholder="09XXXXXXXXX" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Relationship</label>
                <select wire:model="emergency_relationship" class="form-select">
                    <option value="">Select...</option>
                    <option value="Spouse">Spouse</option>
                    <option value="Parent">Parent</option>
                    <option value="Child">Child</option>
                    <option value="Sibling">Sibling</option>
                    <option value="Other">Other</option>
                </select>
            </div>
        </div>
    </div>

    {{-- 📸 Attachments --}}
    <div class="section-card" x-show="openSection==='attachments'" x-cloak>
        <div class="section-card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            Digital Attachments
        </div>
        <p style="font-size:0.8125rem;color:var(--text-secondary);margin-bottom:16px;">Upload photo, signature, and fingerprint. Max 2MB per file (JPEG/PNG).</p>
        <div class="grid-3">

            {{-- Photo --}}
            <div>
                <label class="form-label" style="margin-bottom:8px;">2×2 Portrait Photo</label>
                @if($photo)
                <div style="width:160px;height:160px;border-radius:var(--radius-lg);overflow:hidden;border:2px solid var(--blue-100);margin-bottom:12px;">
                    <img src="{{ $photo->temporaryUrl() }}" style="width:100%;height:100%;object-fit:cover;">
                </div>
                @else
                <div class="upload-zone" style="width:160px;height:160px;display:flex;flex-direction:column;align-items:center;justify-content:center;margin-bottom:12px;">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--text-tertiary)" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    <div class="upload-zone-text">2×2 Photo</div>
                </div>
                @endif
                <input type="file" wire:model="photo" accept="image/*" class="form-input" style="padding:6px;font-size:0.75rem;">
                @error('photo') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            {{-- Signature --}}
            <div>
                <label class="form-label" style="margin-bottom:8px;">Digital Signature</label>
                @if($signature)
                <div style="width:160px;height:96px;border-radius:var(--radius-lg);overflow:hidden;border:2px solid var(--blue-100);margin-bottom:12px;background:#fff;">
                    <img src="{{ $signature->temporaryUrl() }}" style="width:100%;height:100%;object-fit:contain;">
                </div>
                @else
                <div class="upload-zone" style="width:160px;height:96px;display:flex;flex-direction:column;align-items:center;justify-content:center;margin-bottom:12px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--text-tertiary)" stroke-width="1.5"><path d="M17 3a2.828 2.828 0 114 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                    <div class="upload-zone-text">Signature</div>
                </div>
                @endif
                <input type="file" wire:model="signature" accept="image/*" class="form-input" style="padding:6px;font-size:0.75rem;">
                @error('signature') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            {{-- Fingerprint --}}
            <div>
                <label class="form-label" style="margin-bottom:8px;">Fingerprint Scan</label>
                @if($fingerprint_file)
                <div style="width:160px;height:96px;border-radius:var(--radius-lg);overflow:hidden;border:2px solid var(--blue-100);margin-bottom:12px;">
                    <img src="{{ $fingerprint_file->temporaryUrl() }}" style="width:100%;height:100%;object-fit:contain;">
                </div>
                @else
                <div class="upload-zone" style="width:160px;height:96px;display:flex;flex-direction:column;align-items:center;justify-content:center;margin-bottom:12px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--text-tertiary)" stroke-width="1.5"><path d="M2 12C2 6.5 6.5 2 12 2a10 10 0 018 4"/><path d="M5 19.5C5.5 18 6 15 6 12c0-3.5 2.5-6 6-6 2.1 0 4 1 5.2 2.5"/><circle cx="12" cy="12" r="2"/></svg>
                    <div class="upload-zone-text">Fingerprint</div>
                </div>
                @endif
                <input type="file" wire:model="fingerprint_file" accept="image/*" class="form-input" style="padding:6px;font-size:0.75rem;">
                @error('fingerprint_file') <span class="form-error">{{ $message }}</span> @enderror
            </div>

        </div>
    </div>

    {{-- 🪪 System IDs --}}
    <div class="section-card" x-show="openSection==='ids'" x-cloak>
        <div class="section-card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
            System & Government IDs
        </div>
        <p style="font-size:0.8125rem;color:var(--text-secondary);margin-bottom:16px;">Resident ID Number is auto-generated on save. These fields are optional.</p>
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Resident ID</label>
                <input type="text" value="Auto-generated" disabled class="form-input" style="background:var(--gray-50);color:var(--text-tertiary);">
            </div>
            <div class="form-group">
                <label class="form-label">Barangay Card ID</label>
                <input type="text" wire:model="barangay_card_id" placeholder="e.g. BC-0001" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">National ID (PhilSys)</label>
                <input type="text" wire:model="national_id_number" placeholder="e.g. PSN-123456789012" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Voter's ID Number</label>
                <input type="text" wire:model="voter_id_number" placeholder="e.g. VOT-12345678" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Voter's Precinct No.</label>
                <input type="text" wire:model="voters_precinct_number" placeholder="e.g. MAS-3-042" class="form-input">
            </div>
        </div>
        <div style="border-top:1px solid var(--border-light);margin:16px 0;"></div>
        <p style="font-size:0.8125rem;color:var(--text-secondary);margin-bottom:12px;">Government Contributions & Tax ID (optional, for employed residents)</p>
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">TIN Number</label>
                <input type="text" wire:model="tin_number" placeholder="e.g. 123-456-789-000" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">PhilHealth Number</label>
                <input type="text" wire:model="philhealth_number" placeholder="e.g. 12-345678901-2" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Pag-IBIG / HDMF Number</label>
                <input type="text" wire:model="pag_ibig_number" placeholder="e.g. 1234-5678-9012" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">SSS Number</label>
                <input type="text" wire:model="sss_number" placeholder="e.g. 12-3456789-0" class="form-input">
            </div>
        </div>
    </div>

    {{-- Submit --}}
    <div style="display:flex;justify-content:flex-end;gap:8px;padding:16px 0 32px;">
        <a href="{{ route('residents.index') }}" wire:navigate class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary btn-lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Save Resident
        </button>
    </div>

    </form>
</div>
</div>
