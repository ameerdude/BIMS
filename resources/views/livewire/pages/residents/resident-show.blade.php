<?php

use Livewire\Volt\Component;
use App\Models\Resident;
use App\Models\DocumentIssued;
use App\Models\BarangayId;
use App\Models\RevenueRecord;
use Livewire\WithFileUploads;

new #[Layout("layouts.app")] class extends Component
{
    use WithFileUploads;

    public Resident $resident;
    public string $activeTab = 'overview';

    // Document issue form
    public string $doc_type = 'barangay_clearance';
    public string $doc_purpose = '';
    public string $doc_remarks = '';

    // ID issue form
    public string $id_reason = '';

    public function mount(Resident $resident): void
    {
        $this->resident = $resident->load([
            'household', 'documentsIssued', 'barangayIds',
            'healthRecords', 'blotterRecords', 'blotterRecords.parties',
            'blottersAsParty', 'blottersAsParty.blotterRecord',
            'businesses',
        ]);
    }

    public function issueDocument(): void
    {
        $this->validate([
            'doc_type' => 'required',
            'doc_purpose' => 'required|string|min:3',
        ]);

        $doc = DocumentIssued::create([
            'resident_id' => $this->resident->id,
            'document_type' => $this->doc_type,
            'control_number' => DocumentIssued::generateControlNumber($this->doc_type),
            'issued_by' => auth()->id(),
            'issued_at' => now(),
            'purpose' => $this->doc_purpose,
            'remarks' => $this->doc_remarks ?: null,
            'qr_token' => DocumentIssued::generateQrToken(),
            'status' => 'active',
        ]);

        // Auto-log revenue if template has a fee
        $templateFee = \App\Models\DocumentTemplate::where('slug', $this->doc_type)->value('fee');
        if ($templateFee && $templateFee > 0) {
            RevenueRecord::create([
                'or_number' => RevenueRecord::generateOrNumber(),
                'category' => str_replace('_', '_', $this->doc_type),
                'description' => $doc->getDocumentTypeLabel() . ': ' . $this->doc_purpose,
                'payer_id' => $this->resident->id,
                'payer_name' => $this->resident->fullName(),
                'amount' => $templateFee,
                'payment_date' => now()->toDateString(),
                'payment_method' => 'cash',
                'received_by' => auth()->id(),
                'remarks' => 'Auto-recorded upon document issuance',
            ]);
        }

        $this->doc_purpose = '';
        $this->doc_remarks = '';
        $this->refreshResident();
    }

    public function issueBarangayId(): void
    {
        // Cancel any existing active ID
        $existing = BarangayId::where('resident_id', $this->resident->id)->where('status', 'active')->first();
        $version = 1;
        if ($existing) {
            $existing->update(['status' => 'cancelled']);
            $version = $existing->version + 1;
        }

        $idCard = BarangayId::create([
            'resident_id' => $this->resident->id,
            'id_number' => BarangayId::generateIdNumber(),
            'barcode_number' => BarangayId::generateBarcodeNumber(),
            'version' => $version,
            'reason' => $this->id_reason ?: 'First-time issuance',
            'qr_token' => \Illuminate\Support\Str::random(32) . time(),
            'status' => 'active',
            'issued_at' => now(),
            'issued_by' => auth()->id(),
        ]);

        // Auto-log ID card fee
        $idFee = \App\Models\BarangaySetting::first()?->id_card_fee;
        if ($idFee && $idFee > 0) {
            RevenueRecord::create([
                'or_number' => RevenueRecord::generateOrNumber(),
                'category' => 'id_card',
                'description' => 'Barangay ID Card: ' . ($this->id_reason ?: 'First-time issuance'),
                'payer_id' => $this->resident->id,
                'payer_name' => $this->resident->fullName(),
                'amount' => $idFee,
                'payment_date' => now()->toDateString(),
                'payment_method' => 'cash',
                'received_by' => auth()->id(),
                'remarks' => 'Auto-recorded upon ID issuance',
            ]);
        }

        $this->id_reason = '';
        $this->refreshResident();
    }

    private function refreshResident(): void
    {
        $this->resident = $this->resident->fresh()->load([
            'household', 'documentsIssued', 'barangayIds',
            'healthRecords', 'blotterRecords', 'blotterRecords.parties',
            'blottersAsParty', 'blottersAsParty.blotterRecord',
            'businesses',
        ]);
    }
}; ?>

<div x-data="{ activeTab: '{{ $activeTab }}', showIssueDoc: false, showIssueId: false }">
<div style="max-width:1200px;margin:0 auto;">

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <a href="{{ route('residents.index') }}" wire:navigate class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back to Residents
        </a>
        <a href="{{ route('residents.edit', $resident) }}" wire:navigate class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit Resident
        </a>
    </div>

    <div style="display:grid;grid-template-columns:300px 1fr;gap:20px;">

    {{-- Left Column: Profile Card --}}
    <div>
        <div class="card" style="padding:24px;text-align:center;margin-bottom:16px;">
            @if($resident->photo_path)
            <div style="width:88px;height:88px;border-radius:50%;overflow:hidden;margin:0 auto 12px;border:3px solid var(--blue-100);">
                <img src="{{ asset('storage/' . $resident->photo_path) }}" style="width:100%;height:100%;object-fit:cover;">
            </div>
            @else
            <div style="width:88px;height:88px;border-radius:50%;background:var(--blue-600);display:flex;align-items:center;justify-content:center;font-size:1.75rem;font-weight:800;color:#fff;margin:0 auto 12px;">
                {{ substr($resident->first_name, 0, 1) }}{{ substr($resident->last_name, 0, 1) }}
            </div>
            @endif
            <h2 style="font-size:1rem;font-weight:800;color:var(--text-primary);margin:0 0 2px;">{{ $resident->fullName() }}</h2>
            <div style="font-size:0.6875rem;font-family:'JetBrains Mono',monospace;color:var(--blue-600);margin-bottom:4px;">{{ $resident->resident_id_number }}</div>
            <div style="font-size:0.8125rem;color:var(--text-secondary);">{{ ucfirst($resident->sex) }} · {{ $resident->getAge() }} yrs old</div>
            <div style="display:flex;flex-wrap:wrap;gap:4px;justify-content:center;margin-top:10px;">
                @foreach($resident->getSectorBadges() as $badge)
                <span class="badge badge-blue" style="font-size:0.625rem;">{{ $badge }}</span>
                @endforeach
            </div>
        </div>

        {{-- Quick Info --}}
        <div class="card" style="padding:16px;margin-bottom:16px;">
            <div class="card-header-title" style="font-size:0.8125rem;margin-bottom:10px;">Quick Info</div>
            <table style="width:100%;font-size:0.8125rem;border-collapse:collapse;">
                <tr style="border-bottom:1px solid var(--border-light);">
                    <td style="padding:5px 0;color:var(--text-muted);width:42%;">Purok</td>
                    <td style="padding:5px 0;font-weight:600;">{{ $resident->purok ?? 'N/A' }}</td>
                </tr>
                <tr style="border-bottom:1px solid var(--border-light);">
                    <td style="padding:5px 0;color:var(--text-muted);">Civil Status</td>
                    <td style="padding:5px 0;font-weight:600;">{{ ucfirst($resident->civil_status) }}</td>
                </tr>
                <tr style="border-bottom:1px solid var(--border-light);">
                    <td style="padding:5px 0;color:var(--text-muted);">Blood Type</td>
                    <td style="padding:5px 0;font-weight:600;">{{ $resident->blood_type ?? 'N/A' }}</td>
                </tr>
                <tr style="border-bottom:1px solid var(--border-light);">
                    <td style="padding:5px 0;color:var(--text-muted);">Religion</td>
                    <td style="padding:5px 0;font-weight:600;">{{ $resident->religion ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding:5px 0;color:var(--text-muted);">Contact</td>
                    <td style="padding:5px 0;font-weight:600;">{{ $resident->contact_number ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        {{-- Emergency Contact --}}
        @if($resident->emergency_contact_name)
        <div class="card" style="padding:16px;">
            <div class="card-header-title" style="font-size:0.8125rem;margin-bottom:10px;">Emergency Contact</div>
            <table style="width:100%;font-size:0.8125rem;border-collapse:collapse;">
                <tr style="border-bottom:1px solid var(--border-light);">
                    <td style="padding:5px 0;color:var(--text-muted);width:42%;">Name</td>
                    <td style="padding:5px 0;font-weight:600;">{{ $resident->emergency_contact_name }}</td>
                </tr>
                <tr style="border-bottom:1px solid var(--border-light);">
                    <td style="padding:5px 0;color:var(--text-muted);">Phone</td>
                    <td style="padding:5px 0;font-weight:600;">{{ $resident->emergency_contact_number ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding:5px 0;color:var(--text-muted);">Relationship</td>
                    <td style="padding:5px 0;font-weight:600;">{{ $resident->emergency_relationship ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
        @endif
    </div>

    {{-- Right Column: Tabbed Content --}}
    <div>

        {{-- Tab Navigation --}}
        <div class="section-tabs" style="margin-bottom:16px;">
            <button type="button" @click="activeTab='overview'" :class="activeTab==='overview' && 'active'" class="section-tab">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                Overview
            </button>
            <button type="button" @click="activeTab='documents'" :class="activeTab==='documents' && 'active'" class="section-tab">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Documents & IDs
            </button>
            <button type="button" @click="activeTab='blotters'" :class="activeTab==='blotters' && 'active'" class="section-tab">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                Blotter Records
            </button>
            <button type="button" @click="activeTab='businesses'" :class="activeTab==='businesses' && 'active'" class="section-tab">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2-2v16"/></svg>
                Businesses
            </button>
            <button type="button" @click="activeTab='health'" :class="activeTab==='health' && 'active'" class="section-tab">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                Health
            </button>
        </div>

        {{-- ═══ OVERVIEW TAB ═══ --}}
        <div x-show="activeTab==='overview'" x-cloak>

            {{-- IDs --}}
            <div class="section-card">
                <div class="section-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    System & Government IDs
                </div>
                <div class="grid-2">
                    <div style="padding:12px;background:var(--navy-50);border-radius:var(--radius);">
                        <div style="font-size:0.6875rem;color:var(--text-muted);margin-bottom:2px;">Resident ID</div>
                        <div style="font-family:'JetBrains Mono',monospace;font-size:0.8125rem;font-weight:700;color:var(--blue-600);">{{ $resident->resident_id_number ?? 'N/A' }}</div>
                    </div>
                    <div style="padding:12px;background:var(--navy-50);border-radius:var(--radius);">
                        <div style="font-size:0.6875rem;color:var(--text-muted);margin-bottom:2px;">Barangay Card</div>
                        <div style="font-family:'JetBrains Mono',monospace;font-size:0.8125rem;font-weight:700;">{{ $resident->barangay_card_id ?? 'N/A' }}</div>
                    </div>
                    <div style="padding:12px;background:var(--navy-50);border-radius:var(--radius);">
                        <div style="font-size:0.6875rem;color:var(--text-muted);margin-bottom:2px;">National ID (PhilSys)</div>
                        <div style="font-family:'JetBrains Mono',monospace;font-size:0.8125rem;font-weight:700;">{{ $resident->national_id_number ?? 'N/A' }}</div>
                    </div>
                    <div style="padding:12px;background:var(--navy-50);border-radius:var(--radius);">
                        <div style="font-size:0.6875rem;color:var(--text-muted);margin-bottom:2px;">Voter's Precinct</div>
                        <div style="font-family:'JetBrains Mono',monospace;font-size:0.8125rem;font-weight:700;">{{ $resident->voters_precinct_number ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            {{-- Personal Demographics --}}
            <div class="section-card">
                <div class="section-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4-4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Personal Demographics
                </div>
                <table style="width:100%;font-size:0.8125rem;border-collapse:collapse;">
                    <tr style="border-bottom:1px solid var(--border-light);">
                        <td style="padding:7px 0;color:var(--text-muted);width:40%;">Full Name</td>
                        <td style="padding:7px 0;font-weight:600;">{{ $resident->fullName() }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--border-light);">
                        <td style="padding:7px 0;color:var(--text-muted);">Birth Date</td>
                        <td style="padding:7px 0;font-weight:600;">{{ $resident->birthdate->format('M d, Y') }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--border-light);">
                        <td style="padding:7px 0;color:var(--text-muted);">Birth Place</td>
                        <td style="padding:7px 0;font-weight:600;">{{ $resident->birth_place ?? 'N/A' }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--border-light);">
                        <td style="padding:7px 0;color:var(--text-muted);">Sex / Age</td>
                        <td style="padding:7px 0;font-weight:600;">{{ ucfirst($resident->sex) }} · {{ $resident->getAge() }} yrs</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--border-light);">
                        <td style="padding:7px 0;color:var(--text-muted);">Civil Status</td>
                        <td style="padding:7px 0;font-weight:600;">{{ ucfirst($resident->civil_status) }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--border-light);">
                        <td style="padding:7px 0;color:var(--text-muted);">Citizenship</td>
                        <td style="padding:7px 0;font-weight:600;">{{ $resident->citizenship ?? 'Filipino' }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--border-light);">
                        <td style="padding:7px 0;color:var(--text-muted);">Blood Type</td>
                        <td style="padding:7px 0;font-weight:600;">{{ $resident->blood_type ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:7px 0;color:var(--text-muted);">Religion</td>
                        <td style="padding:7px 0;font-weight:600;">{{ $resident->religion ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>

            {{-- Address & Housing --}}
            <div class="section-card">
                <div class="section-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Address & Housing
                </div>
                <table style="width:100%;font-size:0.8125rem;border-collapse:collapse;">
                    <tr style="border-bottom:1px solid var(--border-light);">
                        <td style="padding:7px 0;color:var(--text-muted);width:40%;">Street Address</td>
                        <td style="padding:7px 0;font-weight:600;">{{ $resident->street_address ?? 'N/A' }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--border-light);">
                        <td style="padding:7px 0;color:var(--text-muted);">Purok / Zone</td>
                        <td style="padding:7px 0;font-weight:600;">{{ $resident->purok ?? 'N/A' }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--border-light);">
                        <td style="padding:7px 0;color:var(--text-muted);">Residency Status</td>
                        <td style="padding:7px 0;font-weight:600;">{{ $resident->getResidencyStatusLabel() }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--border-light);">
                        <td style="padding:7px 0;color:var(--text-muted);">Years in Barangay</td>
                        <td style="padding:7px 0;font-weight:600;">{{ $resident->length_of_residency_years ?? 'N/A' }} years</td>
                    </tr>
                    <tr>
                        <td style="padding:7px 0;color:var(--text-muted);">Previous Address</td>
                        <td style="padding:7px 0;font-weight:600;">{{ $resident->previous_address ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>

            {{-- Socio-Economic --}}
            <div class="section-card">
                <div class="section-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2-2v16"/></svg>
                    Socio-Economic Profile
                </div>
                <div class="grid-2">
                    <div style="padding:12px;background:var(--navy-50);border-radius:var(--radius);">
                        <div style="font-size:0.6875rem;color:var(--text-muted);margin-bottom:2px;">Occupation</div>
                        <div style="font-size:0.8125rem;font-weight:600;">{{ $resident->occupation ?? 'N/A' }}</div>
                    </div>
                    <div style="padding:12px;background:var(--navy-50);border-radius:var(--radius);">
                        <div style="font-size:0.6875rem;color:var(--text-muted);margin-bottom:2px;">Employment Status</div>
                        <div style="font-size:0.8125rem;font-weight:600;">{{ $resident->getEmploymentStatusLabel() }}</div>
                    </div>
                    <div style="padding:12px;background:var(--navy-50);border-radius:var(--radius);">
                        <div style="font-size:0.6875rem;color:var(--text-muted);margin-bottom:2px;">Monthly Income</div>
                        <div style="font-size:0.8125rem;font-weight:600;">{{ $resident->monthly_income_range ?? 'N/A' }}</div>
                    </div>
                    <div style="padding:12px;background:var(--navy-50);border-radius:var(--radius);">
                        <div style="font-size:0.6875rem;color:var(--text-muted);margin-bottom:2px;">Education</div>
                        <div style="font-size:0.8125rem;font-weight:600;">{{ $resident->getEducationLabel() }}</div>
                    </div>
                </div>
            </div>

            {{-- Digital Attachments --}}
            @if($resident->signature_path || $resident->fingerprint_data)
            <div class="section-card">
                <div class="section-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    Digital Attachments
                </div>
                <div class="grid-2">
                    @if($resident->signature_path)
                    <div>
                        <div style="font-size:0.75rem;color:var(--text-muted);margin-bottom:6px;font-weight:600;">Digital Signature</div>
                        <div style="width:100%;height:72px;border-radius:var(--radius);overflow:hidden;border:1px solid var(--border);background:#fff;display:flex;align-items:center;justify-content:center;">
                            <img src="{{ asset('storage/' . $resident->signature_path) }}" style="max-width:100%;max-height:100%;object-fit:contain;">
                        </div>
                    </div>
                    @endif
                    @if($resident->fingerprint_data)
                    <div>
                        <div style="font-size:0.75rem;color:var(--text-muted);margin-bottom:6px;font-weight:600;">Fingerprint Scan</div>
                        <div style="width:100%;height:72px;border-radius:var(--radius);overflow:hidden;border:1px solid var(--border);background:#fff;display:flex;align-items:center;justify-content:center;">
                            <img src="{{ asset('storage/' . $resident->fingerprint_data) }}" style="max-width:100%;max-height:100%;object-fit:contain;">
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- ═══ DOCUMENTS & IDS TAB ═══ --}}
        <div x-show="activeTab==='documents'" x-cloak>

            {{-- Issue Document Button --}}
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <h3 style="font-size:0.9375rem;font-weight:700;color:var(--text-primary);margin:0;">Documents & Barangay IDs</h3>
                <button @click="showIssueDoc = !showIssueDoc" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Issue Document
                </button>
            </div>

            {{-- Issue Document Form --}}
            <div x-show="showIssueDoc" x-transition style="margin-bottom:16px;">
                <form wire:submit="issueDocument" class="card" style="padding:20px;">
                    <div style="display:grid;grid-template-columns:1fr 2fr 1fr;gap:12px;align-items:end;">
                        <div>
                            <label class="form-label">Document Type *</label>
                            <select wire:model="doc_type" class="form-select">
                                <option value="barangay_clearance">Barangay Clearance</option>
                                <option value="certificate_of_residency">Certificate of Residency</option>
                                <option value="certificate_of_indigency">Certificate of Indigency</option>
                                <option value="certificate_of_good_moral">Good Moral Character</option>
                                <option value="business_clearance">Business Permit Clearance</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Purpose *</label>
                            <input type="text" wire:model="doc_purpose" placeholder="e.g., Employment, Loan, Bank Account" class="form-input">
                            @error('doc_purpose') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary" style="width:100%;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                Issue & Print
                            </button>
                        </div>
                    </div>
                    <div style="margin-top:8px;">
                        <label class="form-label">Remarks</label>
                        <input type="text" wire:model="doc_remarks" placeholder="Optional notes" class="form-input">
                    </div>
                </form>
            </div>

            {{-- Issued Documents Log --}}
            <div class="section-card">
                <div class="section-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    Issued Documents ({{ $resident->documentsIssued->count() }})
                </div>
                @forelse($resident->documentsIssued->sortByDesc('issued_at') as $doc)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border-light);">
                    <div>
                        <div style="font-size:0.8125rem;font-weight:600;color:var(--text-primary);">{{ $doc->getDocumentTypeLabel() }}</div>
                        <div style="font-size:0.6875rem;color:var(--text-muted);font-family:'JetBrains Mono',monospace;">
                            {{ $doc->control_number }} · {{ $doc->issued_at->format('M d, Y') }}
                            @if($doc->purpose) · {{ $doc->purpose }} @endif
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span class="badge {{ $doc->status === 'active' ? 'badge-green' : 'badge-gray' }}">{{ ucfirst($doc->status) }}</span>
                        <a href="{{ route('documents.print', $doc) }}" wire:navigate class="btn btn-sm btn-outline" style="font-size:0.6875rem;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v6"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                            Print
                        </a>
                    </div>
                </div>
                @empty
                <div style="padding:20px 0;text-align:center;color:var(--text-muted);font-size:0.8125rem;">
                    No documents issued yet. Click "Issue Document" above to create one.
                </div>
                @endforelse
            </div>

            {{-- Barangay IDs --}}
            <div class="section-card">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                    <div class="section-card-title" style="margin-bottom:0;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                        Barangay IDs ({{ $resident->barangayIds->count() }})
                    </div>
                    <button @click="showIssueId = !showIssueId" class="btn btn-primary btn-sm">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Issue ID
                    </button>
                </div>

                {{-- Issue ID Form --}}
                <div x-show="showIssueId" x-transition style="margin-bottom:16px;">
                    <form wire:submit="issueBarangayId" class="card" style="padding:16px;">
                        <div style="display:flex;gap:12px;align-items:end;">
                            <div style="flex:1;">
                                <label class="form-label">Reason (optional)</label>
                                <input type="text" wire:model="id_reason" placeholder="e.g., Lost ID, Damaged, First-time" class="form-input">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                Issue & Print
                            </button>
                        </div>
                    </form>
                </div>

                @forelse($resident->barangayIds->sortByDesc('issued_at') as $id)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border-light);">
                    <div>
                        <div style="font-size:0.8125rem;font-weight:600;color:var(--text-primary);font-family:'JetBrains Mono',monospace;">{{ $id->id_number }}</div>
                        <div style="font-size:0.6875rem;color:var(--text-muted);">Version {{ $id->version }} · {{ $id->issued_at->format('M d, Y') }}</div>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span class="badge {{ $id->status === 'active' ? 'badge-green' : 'badge-red' }}">{{ ucfirst($id->status) }}</span>
                        @if($id->status === 'active')
                        <a href="{{ route('ids.print', $id) }}" wire:navigate class="btn btn-table btn-table-print">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v6"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                            Print ID
                        </a>
                        @endif
                    </div>
                </div>
                @empty
                <div style="padding:20px 0;text-align:center;color:var(--text-muted);font-size:0.8125rem;">
                    No Barangay ID issued yet. Click "Issue ID" above to create one.
                </div>
                @endforelse
            </div>
        </div>

        {{-- ═══ BLOTTER RECORDS TAB ═══ --}}
        <div x-show="activeTab==='blotters'" x-cloak>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <h3 style="font-size:0.9375rem;font-weight:700;color:var(--text-primary);margin:0;">Blotter Records</h3>
                <a href="{{ route('blotter.create') }}" wire:navigate class="btn btn-primary btn-sm">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    New Blotter
                </a>
            </div>

            @php
                $blotters = $resident->blotterRecords->sortByDesc('created_at');
                $asParty = $resident->blottersAsParty->load('blotterRecord')->sortByDesc('created_at');
            @endphp

            {{-- As Complainant/Respondent --}}
            @if($asParty->count())
            <div class="section-card">
                <div class="section-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4-4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Involved As Party ({{ $asParty->count() }})
                </div>
                @foreach($asParty as $party)
                @php $blotter = $party->blotterRecord; @endphp
                @if($blotter)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border-light);">
                    <div>
                        <div style="font-size:0.8125rem;font-weight:600;">
                            <span class="badge {{ $party->role === 'complainant' ? 'badge-blue' : 'badge-red' }}" style="font-size:0.625rem;">{{ ucfirst($party->role) }}</span>
                            · {{ str_replace('_', ' ', ucfirst($blotter->incident_type)) }}
                        </div>
                        <div style="font-size:0.6875rem;color:var(--text-muted);font-family:'JetBrains Mono',monospace;">
                            {{ $blotter->blotter_number }} · {{ $blotter->incident_datetime->format('M d, Y') }}
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span class="badge {{ $blotter->status === 'settled' ? 'badge-green' : ($blotter->status === 'pending' ? 'badge-amber' : 'badge-red') }}">{{ $blotter->getStatusLabel() }}</span>
                        <a href="{{ route('blotter.show', $blotter) }}" wire:navigate class="btn btn-sm btn-outline" style="font-size:0.6875rem;">View</a>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @endif

            {{-- As Recorded (direct resident_id) --}}
            @if($blotters->count())
            <div class="section-card">
                <div class="section-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                    Direct Blotter Records ({{ $blotters->count() }})
                </div>
                @foreach($blotters as $blotter)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border-light);">
                    <div>
                        <div style="font-size:0.8125rem;font-weight:600;">{{ str_replace('_', ' ', ucfirst($blotter->incident_type)) }}</div>
                        <div style="font-size:0.6875rem;color:var(--text-muted);font-family:'JetBrains Mono',monospace;">
                            {{ $blotter->blotter_number }} · {{ $blotter->incident_datetime->format('M d, Y') }} · {{ $blotter->location }}
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span class="badge {{ $blotter->status === 'settled' ? 'badge-green' : ($blotter->status === 'pending' ? 'badge-amber' : 'badge-red') }}">{{ $blotter->getStatusLabel() }}</span>
                        <a href="{{ route('blotter.show', $blotter) }}" wire:navigate class="btn btn-sm btn-outline" style="font-size:0.6875rem;">View</a>
                        <a href="{{ route('blotter.print', $blotter) }}" wire:navigate class="btn btn-sm btn-outline" style="font-size:0.6875rem;">Print</a>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if(!$blotters->count() && !$asParty->count())
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity:0.3;margin:0 auto;"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                </div>
                <div class="empty-state-title">No blotter records</div>
                <div class="empty-state-desc">This resident has no blotter records on file.</div>
            </div>
            @endif
        </div>

        {{-- ═══ BUSINESSES TAB ═══ --}}
        <div x-show="activeTab==='businesses'" x-cloak>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <h3 style="font-size:0.9375rem;font-weight:700;color:var(--text-primary);margin:0;">Business Records</h3>
                <a href="{{ route('businesses.create') }}" wire:navigate class="btn btn-primary btn-sm">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Register Business
                </a>
            </div>

            @forelse($resident->businesses as $biz)
            <div class="card" style="padding:16px;margin-bottom:12px;">
                <div style="display:flex;justify-content:space-between;align-items:start;">
                    <div>
                        <div style="font-size:0.9375rem;font-weight:700;color:var(--text-primary);">{{ $biz->business_name }}</div>
                        <div style="font-size:0.8125rem;color:var(--text-secondary);margin-top:2px;">{{ $biz->business_type }}</div>
                        <div style="font-size:0.75rem;color:var(--text-muted);margin-top:4px;">
                            @if($biz->business_address) 📍 {{ $biz->business_address }} · @endif
                            @if($biz->date_registered) Registered {{ $biz->date_registered->format('M d, Y') }} @endif
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span class="badge {{ $biz->is_active ? 'badge-green' : 'badge-red' }}">{{ $biz->is_active ? 'Active' : 'Inactive' }}</span>
                        <a href="{{ route('businesses.edit', $biz) }}" wire:navigate class="btn btn-sm btn-outline" style="font-size:0.6875rem;">Edit</a>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity:0.3;margin:0 auto;"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2-2v16"/></svg>
                </div>
                <div class="empty-state-title">No businesses registered</div>
                <div class="empty-state-desc">This resident has no registered businesses.</div>
            </div>
            @endforelse
        </div>

        {{-- ═══ HEALTH RECORDS TAB ═══ --}}
        <div x-show="activeTab==='health'" x-cloak>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <h3 style="font-size:0.9375rem;font-weight:700;color:var(--text-primary);margin:0;">Health Records</h3>
                <a href="{{ route('health.create') }}" wire:navigate class="btn btn-primary btn-sm">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add Health Record
                </a>
            </div>

            @forelse($resident->healthRecords->sortByDesc('record_date') as $hr)
            <div class="card" style="padding:16px;margin-bottom:12px;">
                <div style="display:flex;justify-content:space-between;align-items:start;">
                    <div>
                        <div style="font-size:0.875rem;font-weight:700;color:var(--text-primary);">{{ $hr->title }}</div>
                        <div style="font-size:0.75rem;color:var(--text-secondary);margin-top:2px;">
                            <span class="badge badge-blue" style="font-size:0.625rem;">{{ $hr->getTypeLabel() }}</span>
                            · {{ $hr->record_date->format('M d, Y') }}
                            @if($hr->provider) · {{ $hr->provider }} @endif
                        </div>
                        @if($hr->description)
                        <div style="font-size:0.8125rem;color:var(--text-secondary);margin-top:6px;">{{ $hr->description }}</div>
                        @endif
                    </div>
                    <div style="text-align:right;">
                        @if($hr->result)
                        <div style="font-size:0.75rem;color:var(--text-muted);">Result: <strong>{{ $hr->result }}</strong></div>
                        @endif
                        @if($hr->next_schedule)
                        <div style="font-size:0.75rem;color:var(--blue-600);font-weight:600;">Next: {{ $hr->next_schedule->format('M d, Y') }}</div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity:0.3;margin:0 auto;"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                </div>
                <div class="empty-state-title">No health records</div>
                <div class="empty-state-desc">No health records found for this resident.</div>
            </div>
            @endforelse
        </div>

    </div>
    </div>
</div>
</div>
