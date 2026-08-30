<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Resident;

new #[Layout("layouts.app")] class extends Component
{
    use WithFileUploads;

    public Resident $resident;

    // IDs
    public ?string $resident_id_number = '';
    public ?string $barangay_card_id = '';
    public ?string $national_id_number = '';
    public ?string $voters_precinct_number = '';
    public ?string $voter_id_number = '';

    // Government IDs & Contributions
    public ?string $tin_number = '';
    public ?string $philhealth_number = '';
    public ?string $pag_ibig_number = '';
    public ?string $sss_number = '';
    public ?string $gsis_number = '';

    // Demographics
    public string $first_name = '';
    public ?string $middle_name = '';
    public string $last_name = '';
    public ?string $suffix = '';
    public ?string $nickname = '';
    public ?string $birthdate = '';
    public ?string $birth_place = '';
    public string $sex = 'male';
    public ?string $citizenship = 'Filipino';
    public ?string $blood_type = '';
    public ?string $religion = '';
    public string $civil_status = 'single';
    public ?string $height_cm = '';
    public ?string $weight_kg = '';

    // Household
    public ?int $household_id = null;
    public ?string $relationship_to_head = 'member';

    // Address
    public ?string $purok = '';
    public ?string $street_address = '';
    public string $residency_status = 'homeowner';
    public ?int $length_of_residency_years = null;
    public ?string $previous_address = '';

    // Socio-Economic
    public ?string $occupation = '';
    public ?string $employment_status = '';
    public ?string $monthly_income_range = '';
    public ?string $educational_attainment = '';

    // Sector flags
    public bool $is_registered_voter = false;
    public bool $is_pwd = false;
    public ?string $type_of_disability = '';
    public bool $is_senior_citizen = false;
    public bool $is_4ps_beneficiary = false;
    public bool $is_solo_parent = false;
    public bool $is_indigent = false;

    // Family
    public ?int $no_of_children = null;
    public bool $is_pregnant = false;
    public bool $is_lactating = false;

    // Contact
    public ?string $contact_number = '';
    public ?string $email = '';

    // Emergency
    public ?string $emergency_contact_name = '';
    public ?string $emergency_contact_number = '';
    public ?string $emergency_relationship = '';

    // Digital Attachments
    public $photo = null;
    public $signature = null;
    public $fingerprint_file = null;

    public function mount(Resident $resident): void
    {
        $this->resident = $resident;
        $this->fill($resident->only([
            'resident_id_number', 'barangay_card_id', 'national_id_number', 'voters_precinct_number', 'voter_id_number',
            'tin_number', 'philhealth_number', 'pag_ibig_number', 'sss_number', 'gsis_number',
            'first_name', 'middle_name', 'last_name', 'suffix', 'nickname',
            'birthdate', 'birth_place', 'sex', 'citizenship', 'blood_type', 'religion', 'civil_status',
            'height_cm', 'weight_kg',
            'household_id', 'relationship_to_head',
            'purok', 'street_address', 'residency_status', 'length_of_residency_years', 'previous_address',
            'occupation', 'employment_status', 'monthly_income_range', 'educational_attainment',
            'is_registered_voter', 'is_pwd', 'type_of_disability', 'is_senior_citizen',
            'is_4ps_beneficiary', 'is_solo_parent', 'is_indigent',
            'no_of_children', 'is_pregnant', 'is_lactating',
            'contact_number', 'email',
            'emergency_contact_name', 'emergency_contact_number', 'emergency_relationship',
        ]));

        $this->birthdate = $this->resident->birthdate?->format('Y-m-d') ?? '';
        // Convert numeric fields to string for form binding
        $this->height_cm = $this->resident->height_cm ? (string) $this->resident->height_cm : '';
        $this->weight_kg = $this->resident->weight_kg ? (string) $this->resident->weight_kg : '';
    }

    public function updatedIsPwd(): void
    {
        if (!$this->is_pwd) $this->type_of_disability = '';
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

        // Handle file uploads - delete old files if replacing
        $photoPath = $this->resident->photo_path;
        if ($this->photo) {
            if ($photoPath) \Storage::disk('public')->delete($photoPath);
            $photoPath = $this->photo->store('residents', 'public');
        }

        $signaturePath = $this->resident->signature_path;
        if ($this->signature) {
            if ($signaturePath) \Storage::disk('public')->delete($signaturePath);
            $signaturePath = $this->signature->store('residents/signatures', 'public');
        }

        $fingerprintPath = $this->resident->fingerprint_data;
        if ($this->fingerprint_file) {
            if ($fingerprintPath) \Storage::disk('public')->delete($fingerprintPath);
            $fingerprintPath = $this->fingerprint_file->store('residents/fingerprints', 'public');
        }

        $this->resident->update([
            'barangay_card_id' => $this->barangay_card_id ?: null,
            'national_id_number' => $this->national_id_number ?: null,
            'voters_precinct_number' => $this->voters_precinct_number ?: null,
            'voter_id_number' => $this->voter_id_number ?: null,
            'tin_number' => $this->tin_number ?: null,
            'philhealth_number' => $this->philhealth_number ?: null,
            'pag_ibig_number' => $this->pag_ibig_number ?: null,
            'sss_number' => $this->sss_number ?: null,
            'gsis_number' => $this->gsis_number ?: null,
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
            'citizenship' => $this->citizenship,
            'blood_type' => $this->blood_type ?: null,
            'religion' => $this->religion ?: null,
            'civil_status' => $this->civil_status,
            'height_cm' => $this->height_cm ? (float) $this->height_cm : null,
            'weight_kg' => $this->weight_kg ? (float) $this->weight_kg : null,
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
            'no_of_children' => $this->no_of_children,
            'is_pregnant' => $this->is_pregnant,
            'is_lactating' => $this->is_lactating,
            'contact_number' => $this->contact_number ?: null,
            'email' => $this->email ?: null,
            'emergency_contact_name' => $this->emergency_contact_name ?: null,
            'emergency_contact_number' => $this->emergency_contact_number ?: null,
            'emergency_relationship' => $this->emergency_relationship ?: null,
            'photo_path' => $photoPath,
            'signature_path' => $signaturePath,
            'fingerprint_data' => $fingerprintPath,
        ]);

        $this->dispatch('residentSaved');
        $this->redirect(route('residents.show', $this->resident), navigate: true);
    }
}; ?>

<div x-data="{openSection:'personal'}">
<div style="max-width:900px;margin:0 auto;">
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <div>
        <h1 style="font-size:1.25rem;font-weight:800;color:var(--text-primary);margin:0;">Edit Resident: {{ $resident->fullName() }}</h1>
        <p style="font-size:0.8125rem;color:var(--text-secondary);margin:2px 0 0;">Update the resident's information below</p>
    </div>
    <a href="{{ route('residents.show', $resident) }}" wire:navigate class="btn btn-outline">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        Back
    </a>
</div>

<form wire:submit="save" class="space-y-4" enctype="multipart/form-data">

{{-- Section Tabs --}}
<div class="section-tabs">
    <button type="button" @click="openSection='personal'" :class="openSection==='personal' && 'active'" class="section-tab">👤 Personal</button>
    <button type="button" @click="openSection='address'" :class="openSection==='address' && 'active'" class="section-tab">📍 Address</button>
    <button type="button" @click="openSection='socioeconomic'" :class="openSection==='socioeconomic' && 'active'" class="section-tab">💼 Socio-Economic</button>
    <button type="button" @click="openSection='sectors'" :class="openSection==='sectors' && 'active'" class="section-tab">🏷️ Sectors</button>
    <button type="button" @click="openSection='family'" :class="openSection==='family' && 'active'" class="section-tab">👨‍👩‍👧‍👦 Family</button>
    <button type="button" @click="openSection='contact'" :class="openSection==='contact' && 'active'" class="section-tab">📞 Contact</button>
    <button type="button" @click="openSection='emergency'" :class="openSection==='emergency' && 'active'" class="section-tab">🚨 Emergency</button>
    <button type="button" @click="openSection='attachments'" :class="openSection==='attachments' && 'active'" class="section-tab">📸 Attachments</button>
    <button type="button" @click="openSection='ids'" :class="openSection==='ids' && 'active'" class="section-tab">🪪 IDs</button>
</div>

{{-- 🪪 System IDs --}}
<div class="section-card" x-show="openSection==='ids'" x-cloak>
    <h3 class="section-card-title">🪪 System & Government IDs</h3>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div>
            <label class="form-label">Resident ID</label>
            <input type="text" value="{{ $resident->resident_id_number }}" disabled class="form-input" style="background:var(--navy-50);color:var(--text-muted);">
        </div>
        <div>
            <label class="form-label">Barangay Card ID</label>
            <input type="text" wire:model="barangay_card_id" class="form-input">
        </div>
        <div>
            <label class="form-label">National ID (PhilSys)</label>
            <input type="text" wire:model="national_id_number" class="form-input">
        </div>
        <div>
            <label class="form-label">Voter's ID Number</label>
            <input type="text" wire:model="voter_id_number" class="form-input">
        </div>
        <div>
            <label class="form-label">Voter's Precinct No.</label>
            <input type="text" wire:model="voters_precinct_number" class="form-input">
        </div>
    </div>
    <h3 class="section-card-title" style="margin-top:20px;">🏛️ Government Contributions</h3>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div>
            <label class="form-label">TIN Number</label>
            <input type="text" wire:model="tin_number" class="form-input" placeholder="XXX-XXX-XXX">
        </div>
        <div>
            <label class="form-label">PhilHealth Number</label>
            <input type="text" wire:model="philhealth_number" class="form-input" placeholder="XX-XXXXXXXXXX-X">
        </div>
        <div>
            <label class="form-label">Pag-IBIG Number</label>
            <input type="text" wire:model="pag_ibig_number" class="form-input" placeholder="XXXXXXXXXX">
        </div>
        <div>
            <label class="form-label">SSS Number</label>
            <input type="text" wire:model="sss_number" class="form-input" placeholder="XX-XXXXXXX-X">
        </div>
        <div>
            <label class="form-label">GSIS Number</label>
            <input type="text" wire:model="gsis_number" class="form-input">
        </div>
    </div>
</div>

{{-- 👤 Personal Demographics --}}
<div class="section-card" x-show="openSection==='personal'" x-cloak>
    <h3 class="section-card-title">👤 Personal Demographics</h3>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div>
            <label class="form-label">First Name *</label>
            <input type="text" wire:model="first_name" class="form-input">
            @error('first_name') <span class="form-error">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="form-label">Middle Name</label>
            <input type="text" wire:model="middle_name" class="form-input">
        </div>
        <div>
            <label class="form-label">Last Name *</label>
            <input type="text" wire:model="last_name" class="form-input">
            @error('last_name') <span class="form-error">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="form-label">Suffix</label>
            <input type="text" wire:model="suffix" class="form-input" placeholder="Jr., Sr., III, etc.">
        </div>
        <div>
            <label class="form-label">Nickname</label>
            <input type="text" wire:model="nickname" class="form-input">
        </div>
        <div>
            <label class="form-label">Birth Date *</label>
            <input type="date" wire:model="birthdate" class="form-input">
            @error('birthdate') <span class="form-error">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="form-label">Birth Place</label>
            <input type="text" wire:model="birth_place" class="form-input">
        </div>
        <div>
            <label class="form-label">Sex *</label>
            <select wire:model="sex" class="form-input">
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </div>
        <div>
            <label class="form-label">Civil Status</label>
            <select wire:model="civil_status" class="form-input">
                <option value="single">Single</option>
                <option value="married">Married</option>
                <option value="widowed">Widowed</option>
                <option value="separated">Separated</option>
                <option value="cohabiting">Cohabiting (Live-in)</option>
            </select>
        </div>
        <div>
            <label class="form-label">Citizenship</label>
            <input type="text" wire:model="citizenship" class="form-input">
        </div>
        <div>
            <label class="form-label">Blood Type</label>
            <select wire:model="blood_type" class="form-input">
                <option value="">Select...</option>
                @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt)
                <option value="{{ $bt }}">{{ $bt }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Religion</label>
            <input type="text" wire:model="religion" class="form-input">
        </div>
        <div>
            <label class="form-label">Height (cm)</label>
            <input type="number" wire:model="height_cm" step="0.1" min="0" class="form-input" placeholder="e.g. 165">
        </div>
        <div>
            <label class="form-label">Weight (kg)</label>
            <input type="number" wire:model="weight_kg" step="0.1" min="0" class="form-input" placeholder="e.g. 65">
        </div>
        <div>
            <label class="form-label">Household</label>
            <div style="display:flex;gap:6px;">
                <select wire:model="household_id" class="form-input" style="flex:1;">
                    <option value="">No household (optional)</option>
                    @foreach(\App\Models\Household::orderBy('full_address')->get() as $hh)
                    <option value="{{ $hh->id }}">{{ $hh->full_address }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="form-label">Relationship to Head</label>
            <select wire:model="relationship_to_head" class="form-input">
                @foreach(['head','spouse','child','relative','boarder','other'] as $rel)
                <option value="{{ $rel }}">{{ ucfirst($rel) }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

{{-- 📍 Address & Housing --}}
<div class="section-card" x-show="openSection==='address'" x-cloak>
    <h3 class="section-card-title">📍 Address & Housing Logistics</h3>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div>
            <label class="form-label">Purok / Zone / Sitio</label>
            <select wire:model="purok" class="form-input">
                <option value="">Select Purok</option>
                @foreach(\App\Models\Purok::active()->get() as $pk)
                <option value="{{ $pk->name }}">{{ $pk->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Street Address</label>
            <input type="text" wire:model="street_address" class="form-input">
        </div>
        <div>
            <label class="form-label">Residency Status</label>
            <select wire:model="residency_status" class="form-input">
                <option value="homeowner">Homeowner</option>
                <option value="renter">Renter</option>
                <option value="transient">Transient / Boarder</option>
            </select>
        </div>
        <div>
            <label class="form-label">Length of Residency (years)</label>
            <input type="number" wire:model="length_of_residency_years" min="0" class="form-input">
        </div>
        <div style="grid-column:span 2;">
            <label class="form-label">Previous Address</label>
            <input type="text" wire:model="previous_address" class="form-input">
        </div>
    </div>
</div>

{{-- 💼 Socio-Economic --}}
<div class="section-card" x-show="openSection==='socioeconomic'" x-cloak>
    <h3 class="section-card-title">💼 Socio-Economic Profile</h3>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div>
            <label class="form-label">Occupation</label>
            <input type="text" wire:model="occupation" class="form-input">
        </div>
        <div>
            <label class="form-label">Employment Status</label>
            <select wire:model="employment_status" class="form-input">
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
        <div>
            <label class="form-label">Monthly Income Range</label>
            <select wire:model="monthly_income_range" class="form-input">
                <option value="">Select...</option>
                <option value="Below ₱10,000">Below ₱10,000</option>
                <option value="₱10,000 - ₱20,000">₱10,000 - ₱20,000</option>
                <option value="₱20,000 - ₱50,000">₱20,000 - ₱50,000</option>
                <option value="₱50,000 - ₱100,000">₱50,000 - ₱100,000</option>
                <option value="Above ₱100,000">Above ₱100,000</option>
            </select>
        </div>
        <div>
            <label class="form-label">Educational Attainment</label>
            <select wire:model="educational_attainment" class="form-input">
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

{{-- 🏷️ Sector Classifications --}}
<div class="section-card" x-show="openSection==='sectors'" x-cloak>
    <h3 class="section-card-title">🏷️ Special Sector Classifications</h3>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <label class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 cursor-pointer">
            <input type="checkbox" wire:model.live="is_registered_voter" class="w-5 h-5 rounded text-indigo-600">
            <div><span class="text-sm font-semibold text-gray-700">Registered Voter</span></div>
        </label>
        <label class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 cursor-pointer">
            <input type="checkbox" wire:model.live="is_pwd" class="w-5 h-5 rounded text-indigo-600">
            <div><span class="text-sm font-semibold text-gray-700">PWD (Person with Disability)</span></div>
        </label>
        <div x-show="$wire.is_pwd" x-transition class="md:col-span-2 ml-8">
            <label class="form-label">Type of Disability</label>
            <select wire:model="type_of_disability" class="form-input">
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
        <label class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 cursor-pointer">
            <input type="checkbox" wire:model="is_senior_citizen" class="w-5 h-5 rounded text-indigo-600">
            <div><span class="text-sm font-semibold text-gray-700">Senior Citizen (60+)</span></div>
        </label>
        <label class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 cursor-pointer">
            <input type="checkbox" wire:model="is_solo_parent" class="w-5 h-5 rounded text-indigo-600">
            <div><span class="text-sm font-semibold text-gray-700">Solo Parent</span></div>
        </label>
        <label class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 cursor-pointer">
            <input type="checkbox" wire:model="is_indigent" class="w-5 h-5 rounded text-indigo-600">
            <div><span class="text-sm font-semibold text-gray-700">Indigent</span></div>
        </label>
        <label class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 cursor-pointer">
            <input type="checkbox" wire:model="is_4ps_beneficiary" class="w-5 h-5 rounded text-indigo-600">
            <div><span class="text-sm font-semibold text-gray-700">4Ps Beneficiary</span></div>
        </label>
    </div>
</div>

{{-- 👨‍👩‍👧‍👦 Family --}}
<div class="section-card" x-show="openSection==='family'" x-cloak>
    <h3 class="section-card-title">👨‍👩‍👧‍👦 Family & Children</h3>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div>
            <label class="form-label">Number of Children</label>
            <input type="number" wire:model="no_of_children" min="0" class="form-input" placeholder="0">
        </div>
        <div></div>
        <label class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 cursor-pointer">
            <input type="checkbox" wire:model="is_pregnant" class="w-5 h-5 rounded text-indigo-600">
            <div><span class="text-sm font-semibold text-gray-700">Currently Pregnant</span></div>
        </label>
        <label class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 cursor-pointer">
            <input type="checkbox" wire:model="is_lactating" class="w-5 h-5 rounded text-indigo-600">
            <div><span class="text-sm font-semibold text-gray-700">Currently Lactating</span></div>
        </label>
    </div>
</div>

{{-- 📞 Contact --}}
<div class="section-card" x-show="openSection==='contact'" x-cloak>
    <h3 class="section-card-title">📞 Contact Information</h3>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div>
            <label class="form-label">Mobile Number</label>
            <input type="text" wire:model="contact_number" class="form-input">
        </div>
        <div>
            <label class="form-label">Email Address</label>
            <input type="email" wire:model="email" class="form-input">
        </div>
    </div>
</div>

{{-- 🚨 Emergency --}}
<div class="section-card" x-show="openSection==='emergency'" x-cloak>
    <h3 class="section-card-title">🚨 Emergency Contact</h3>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div>
            <label class="form-label">Emergency Contact Name</label>
            <input type="text" wire:model="emergency_contact_name" class="form-input">
        </div>
        <div>
            <label class="form-label">Emergency Contact Number</label>
            <input type="text" wire:model="emergency_contact_number" class="form-input">
        </div>
        <div>
            <label class="form-label">Relationship</label>
            <select wire:model="emergency_relationship" class="form-input">
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
    <h3 class="section-card-title">📸 Digital Attachments</h3>
    <p style="font-size:0.75rem;color:var(--text-tertiary);margin-bottom:16px;">Upload new files to replace existing ones. Max 2MB per file (JPEG/PNG).</p>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:24px;">

        {{-- Photo --}}
        <div>
            <label style="font-size:0.8125rem;font-weight:700;color:var(--text-primary);margin-bottom:8px;display:block;">2x2 Portrait Photo</label>
            @if($photo)
            <div style="width:160px;height:160px;border-radius:12px;overflow:hidden;margin:0 auto 12px;border:2px solid var(--blue-200);">
                <img src="{{ $photo->temporaryUrl() }}" style="width:100%;height:100%;object-fit:cover;">
            </div>
            @elseif($resident->photo_path)
            <div style="width:160px;height:160px;border-radius:12px;overflow:hidden;margin:0 auto 12px;border:2px solid var(--green-200);">
                <img src="{{ asset('storage/' . $resident->photo_path) }}" style="width:100%;height:100%;object-fit:cover;">
            </div>
            @else
            <div style="width:160px;height:160px;border-radius:12px;margin:0 auto 12px;border:2px dashed var(--border);display:flex;align-items:center;justify-content:center;background:var(--gray-50);">
                <div style="text-align:center;color:var(--text-tertiary);">
                    <div style="font-size:1.75rem;">📷</div>
                    <div style="font-size:0.6875rem;">2x2 Photo</div>
                </div>
            </div>
            @endif
            <input type="file" wire:model="photo" accept="image/*" class="form-input" style="padding:6px;font-size:0.75rem;">
            @error('photo') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        {{-- Signature --}}
        <div>
            <label style="font-size:0.8125rem;font-weight:700;color:var(--text-primary);margin-bottom:8px;display:block;">Digital Signature</label>
            @if($signature)
            <div style="width:160px;height:96px;border-radius:12px;overflow:hidden;margin:0 auto 12px;border:2px solid var(--blue-200);background:white;">
                <img src="{{ $signature->temporaryUrl() }}" style="width:100%;height:100%;object-fit:contain;">
            </div>
            @elseif($resident->signature_path)
            <div style="width:160px;height:96px;border-radius:12px;overflow:hidden;margin:0 auto 12px;border:2px solid var(--green-200);background:white;">
                <img src="{{ asset('storage/' . $resident->signature_path) }}" style="width:100%;height:100%;object-fit:contain;">
            </div>
            @else
            <div style="width:160px;height:96px;border-radius:12px;margin:0 auto 12px;border:2px dashed var(--border);display:flex;align-items:center;justify-content:center;background:var(--gray-50);">
                <div style="text-align:center;color:var(--text-tertiary);">
                    <div style="font-size:1.5rem;">✍️</div>
                    <div style="font-size:0.6875rem;">Signature</div>
                </div>
            </div>
            @endif
            <input type="file" wire:model="signature" accept="image/*" class="form-input" style="padding:6px;font-size:0.75rem;">
            @error('signature') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        {{-- Fingerprint --}}
        <div>
            <label style="font-size:0.8125rem;font-weight:700;color:var(--text-primary);margin-bottom:8px;display:block;">Fingerprint Scan</label>
            @if($fingerprint_file)
            <div style="width:160px;height:96px;border-radius:12px;overflow:hidden;margin:0 auto 12px;border:2px solid var(--blue-200);">
                <img src="{{ $fingerprint_file->temporaryUrl() }}" style="width:100%;height:100%;object-fit:contain;">
            </div>
            @elseif($resident->fingerprint_data)
            <div style="width:160px;height:96px;border-radius:12px;overflow:hidden;margin:0 auto 12px;border:2px solid var(--green-200);">
                <img src="{{ asset('storage/' . $resident->fingerprint_data) }}" style="width:100%;height:100%;object-fit:contain;">
            </div>
            @else
            <div style="width:160px;height:96px;border-radius:12px;margin:0 auto 12px;border:2px dashed var(--border);display:flex;align-items:center;justify-content:center;background:var(--gray-50);">
                <div style="text-align:center;color:var(--text-tertiary);">
                    <div style="font-size:1.5rem;">👆</div>
                    <div style="font-size:0.6875rem;">Fingerprint</div>
                </div>
            </div>
            @endif
            <input type="file" wire:model="fingerprint_file" accept="image/*" class="form-input" style="padding:6px;font-size:0.75rem;">
            @error('fingerprint_file') <span class="form-error">{{ $message }}</span> @enderror
        </div>

    </div>
</div>

{{-- Submit --}}
<div style="display:flex;justify-content:flex-end;gap:8px;padding:16px 0 32px;">
    <a href="{{ route('residents.show', $resident) }}" wire:navigate class="btn btn-outline">Cancel</a>
    <button type="submit" class="btn btn-primary btn-lg">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Update Resident
    </button>
</div>

</form>
</div>
</div>
