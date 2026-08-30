<?php

use Livewire\Volt\Component;
use App\Models\BlotterRecord;
use App\Models\BlotterParty;
use App\Models\Resident;

new #[Layout("layouts.app")] class extends Component
{
    public string $incident_type = 'other';
    public string $location = '';
    public string $incident_date = '';
    public string $incident_time = '';
    public string $narrative = '';

    // Complainant
    public int $complainant_resident_id = 0;
    public string $complainant_name = '';
    public string $complainant_search = '';
    public $complainant_resident = null;

    // Respondent
    public int $respondent_resident_id = 0;
    public string $respondent_name = '';
    public string $respondent_search = '';
    public $respondent_resident = null;

    public function updatedComplainantResidentId($v)
    {
        $this->complainant_resident = $v ? Resident::find($v) : null;
        if ($this->complainant_resident) {
            $this->complainant_name = $this->complainant_resident->fullName();
        }
    }

    public function updatedRespondentResidentId($v)
    {
        $this->respondent_resident = $v ? Resident::find($v) : null;
        if ($this->respondent_resident) {
            $this->respondent_name = $this->respondent_resident->fullName();
        }
    }

    public function searchComplainants()
    {
        if (strlen($this->complainant_search) < 2) return collect();
        return Resident::where('is_active', true)
            ->where(fn($q) => $q->where('first_name', 'like', "%{$this->complainant_search}%")
                ->orWhere('last_name', 'like', "%{$this->complainant_search}%"))
            ->limit(8)->get();
    }

    public function searchRespondents()
    {
        if (strlen($this->respondent_search) < 2) return collect();
        return Resident::where('is_active', true)
            ->where(fn($q) => $q->where('first_name', 'like', "%{$this->respondent_search}%")
                ->orWhere('last_name', 'like', "%{$this->respondent_search}%"))
            ->limit(8)->get();
    }

    public function save(): void
    {
        $this->validate([
            'incident_type' => 'required',
            'location' => 'required',
            'incident_date' => 'required|date',
            'incident_time' => 'required',
            'narrative' => 'required',
            'complainant_name' => 'required',
            'respondent_name' => 'required',
        ]);

        $blotter = BlotterRecord::create([
            'blotter_number' => BlotterRecord::generateBlotterNumber(),
            'incident_type' => $this->incident_type,
            'location' => $this->location,
            'incident_datetime' => "{$this->incident_date} {$this->incident_time}",
            'narrative' => $this->narrative,
            'status' => 'pending',
            'recorded_by' => auth()->id(),
        ]);

        BlotterParty::create([
            'blotter_record_id' => $blotter->id,
            'resident_id' => $this->complainant_resident_id ?: null,
            'role' => 'complainant',
            'name' => $this->complainant_name,
        ]);

        BlotterParty::create([
            'blotter_record_id' => $blotter->id,
            'resident_id' => $this->respondent_resident_id ?: null,
            'role' => 'respondent',
            'name' => $this->respondent_name,
        ]);

        $this->redirect(route('blotter.show', $blotter), navigate: true);
    }
}; ?>

<div>
<div style="max-width:800px;margin:0 auto;">

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <div>
            <h1 style="font-size:1.125rem;font-weight:800;color:var(--text-primary);margin:0;">New Blotter Record</h1>
            <p style="font-size:0.8125rem;color:var(--text-secondary);margin:2px 0 0;">Record a new incident complaint</p>
        </div>
        <a href="{{ route('blotter.index') }}" wire:navigate class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back
        </a>
    </div>

    <form wire:submit="save">

        {{-- Incident Details --}}
        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                Incident Details
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                <div>
                    <label class="form-label">Incident Type *</label>
                    <select wire:model="incident_type" class="form-select">
                        <option value="theft">Theft</option>
                        <option value="assault">Assault</option>
                        <option value="quarrel">Quarrel</option>
                        <option value="noise_complaint">Noise Complaint</option>
                        <option value="domestic_violence">Domestic Violence</option>
                        <option value="vandalism">Vandalism</option>
                        <option value="fraud">Fraud</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Date *</label>
                    <input type="date" wire:model="incident_date" class="form-input">
                </div>
                <div>
                    <label class="form-label">Time *</label>
                    <input type="time" wire:model="incident_time" class="form-input">
                </div>
            </div>
            <div style="margin-top:12px;">
                <label class="form-label">Location *</label>
                <input type="text" wire:model="location" placeholder="Where did the incident occur?" class="form-input">
            </div>
            <div style="margin-top:12px;">
                <label class="form-label">Narrative / Description *</label>
                <textarea wire:model="narrative" rows="4" class="form-textarea" placeholder="Describe the incident in detail..."></textarea>
            </div>
        </div>

        {{-- Complainant --}}
        <div class="section-card">
            <div class="section-card-title" style="color:var(--blue-600);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4-4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Complainant
            </div>
            <p style="font-size:0.75rem;color:var(--text-muted);margin-bottom:12px;">Search for a registered resident or type a name manually.</p>

            <div>
                <label class="form-label">Search Resident</label>
                <input type="text" wire:model.live.debounce.300ms="complainant_search" placeholder="Type name to search database..." class="form-input">
                @if(strlen($complainant_search) >= 2)
                    @if($this->searchComplainants()->count())
                    <div style="border:1px solid var(--border);border-radius:var(--radius);max-height:200px;overflow-y:auto;margin-top:4px;">
                        @foreach($this->searchComplainants() as $r)
                        <button type="button" wire:click="$set('complainant_resident_id', {{ $r->id }}); $set('complainant_name', '{{ addslashes($r->fullName()) }}'); $set('complainant_search', '')"
                            style="display:flex;justify-content:space-between;align-items:center;width:100%;text-align:left;padding:8px 12px;border:none;border-bottom:1px solid var(--border-light);background:{{ $complainant_resident_id == $r->id ? 'var(--blue-50)' : 'var(--surface)' }};cursor:pointer;font-family:inherit;font-size:0.8125rem;">
                            <div>
                                <strong>{{ $r->fullName() }}</strong>
                                <span style="color:var(--text-muted);font-size:0.75rem;margin-left:6px;">{{ $r->purok ?? '' }}</span>
                            </div>
                            <span style="font-size:0.6875rem;color:var(--blue-600);font-weight:600;">Select</span>
                        </button>
                        @endforeach
                    </div>
                    @else
                    <div style="padding:8px 12px;margin-top:4px;border:1px solid var(--border);border-radius:var(--radius);background:var(--navy-50);font-size:0.8125rem;color:var(--text-muted);">
                        No residents found for "{{ $complainant_search }}". Type the name manually below.
                    </div>
                    @endif
                @endif
            </div>

            @if($complainant_resident)
            <div class="alert alert-success" style="margin-top:8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span style="flex:1;">Linked to: <strong>{{ $complainant_resident->fullName() }}</strong> | {{ $complainant_resident->resident_id_number }}</span>
                <button type="button" wire:click="$set('complainant_resident_id', 0); $set('complainant_resident', null)" style="background:none;border:none;color:var(--red-600);cursor:pointer;font-size:0.75rem;font-weight:600;text-decoration:underline;font-family:inherit;">Clear</button>
            </div>
            @endif

            <div style="margin-top:12px;">
                <label class="form-label">Complainant Name *</label>
                <input type="text" wire:model="complainant_name" placeholder="Full name" class="form-input">
            </div>
        </div>

        {{-- Respondent --}}
        <div class="section-card">
            <div class="section-card-title" style="color:var(--red-600);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4-4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Respondent
            </div>
            <p style="font-size:0.75rem;color:var(--text-muted);margin-bottom:12px;">Search for a registered resident or type a name manually.</p>

            <div>
                <label class="form-label">Search Resident</label>
                <input type="text" wire:model.live.debounce.300ms="respondent_search" placeholder="Type name to search database..." class="form-input">
                @if(strlen($respondent_search) >= 2)
                    @if($this->searchRespondents()->count())
                    <div style="border:1px solid var(--border);border-radius:var(--radius);max-height:200px;overflow-y:auto;margin-top:4px;">
                        @foreach($this->searchRespondents() as $r)
                        <button type="button" wire:click="$set('respondent_resident_id', {{ $r->id }}); $set('respondent_name', '{{ addslashes($r->fullName()) }}'); $set('respondent_search', '')"
                            style="display:flex;justify-content:space-between;align-items:center;width:100%;text-align:left;padding:8px 12px;border:none;border-bottom:1px solid var(--border-light);background:{{ $respondent_resident_id == $r->id ? 'var(--blue-50)' : 'var(--surface)' }};cursor:pointer;font-family:inherit;font-size:0.8125rem;">
                            <div>
                                <strong>{{ $r->fullName() }}</strong>
                                <span style="color:var(--text-muted);font-size:0.75rem;margin-left:6px;">{{ $r->purok ?? '' }}</span>
                            </div>
                            <span style="font-size:0.6875rem;color:var(--blue-600);font-weight:600;">Select</span>
                        </button>
                        @endforeach
                    </div>
                    @else
                    <div style="padding:8px 12px;margin-top:4px;border:1px solid var(--border);border-radius:var(--radius);background:var(--navy-50);font-size:0.8125rem;color:var(--text-muted);">
                        No residents found for "{{ $respondent_search }}". Type the name manually below.
                    </div>
                    @endif
                @endif
            </div>

            @if($respondent_resident)
            <div class="alert alert-success" style="margin-top:8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span style="flex:1;">Linked to: <strong>{{ $respondent_resident->fullName() }}</strong> | {{ $respondent_resident->resident_id_number }}</span>
                <button type="button" wire:click="$set('respondent_resident_id', 0); $set('respondent_resident', null)" style="background:none;border:none;color:var(--red-600);cursor:pointer;font-size:0.75rem;font-weight:600;text-decoration:underline;font-family:inherit;">Clear</button>
            </div>
            @endif

            <div style="margin-top:12px;">
                <label class="form-label">Respondent Name *</label>
                <input type="text" wire:model="respondent_name" placeholder="Full name" class="form-input">
            </div>
        </div>

        {{-- Submit --}}
        <div style="display:flex;justify-content:flex-end;gap:8px;padding:16px 0 32px;">
            <a href="{{ route('blotter.index') }}" wire:navigate class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary btn-lg">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Save Blotter Record
            </button>
        </div>

    </form>
</div>
</div>
