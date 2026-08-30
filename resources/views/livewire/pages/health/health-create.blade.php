<?php

use Livewire\Volt\Component;
use App\Models\HealthRecord;
use App\Models\Resident;

new #[Layout("layouts.app")] class extends Component
{
    public int $resident_id = 0;
    public string $record_type = 'vaccination';
    public string $title = '';
    public string $description = '';
    public string $record_date = '';
    public string $provider = '';
    public string $result = '';
    public string $next_schedule = '';
    public string $searchTerm = '';
    public $selectedResident = null;

    public function updatedResidentId($v) { $this->selectedResident = $v ? Resident::find($v) : null; }

    public function residents()
    {
        if (strlen($this->searchTerm) < 2) return collect();
        return Resident::where('is_active', true)
            ->where(fn($q) => $q->where('first_name', 'like', "%{$this->searchTerm}%")->orWhere('last_name', 'like', "%{$this->searchTerm}%"))
            ->limit(10)->get();
    }

    public function save(): void
    {
        $this->validate([
            'resident_id' => 'required',
            'title' => 'required',
            'record_date' => 'required|date',
        ]);
        HealthRecord::create([
            'resident_id' => $this->resident_id,
            'record_type' => $this->record_type,
            'title' => $this->title,
            'description' => $this->description,
            'record_date' => $this->record_date,
            'provider' => $this->provider,
            'result' => $this->result,
            'next_schedule' => $this->next_schedule ?: null,
            'recorded_by' => auth()->id(),
        ]);
        $this->redirect(route('health.index'), navigate: true);
    }
}; ?>

<div>
<div style="max-width:800px;margin:0 auto;">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <div>
            <h1 style="font-size:1.125rem;font-weight:800;color:var(--text-primary);margin:0;">Add Health Record</h1>
            <p style="font-size:0.8125rem;color:var(--text-secondary);margin:2px 0 0;">Record a health entry for a resident</p>
        </div>
        <a href="{{ route('health.index') }}" wire:navigate class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back
        </a>
    </div>

    <form wire:submit="save">

        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4-4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Select Resident
            </div>
            <div>
                <input type="text" wire:model.live.debounce.300ms="searchTerm" placeholder="Type resident name to search..." class="form-input">
                @if($this->residents()->count())
                <div style="border:1px solid var(--border);border-radius:var(--radius);max-height:200px;overflow-y:auto;margin-top:4px;">
                    @foreach($this->residents() as $r)
                    <button type="button" wire:click="$set('resident_id', {{ $r->id }}); $set('searchTerm', '')"
                        style="display:flex;justify-content:space-between;align-items:center;width:100%;text-align:left;padding:8px 12px;border:none;border-bottom:1px solid var(--border-light);background:{{ $resident_id == $r->id ? 'var(--blue-50)' : 'var(--surface)' }};cursor:pointer;font-family:inherit;font-size:0.8125rem;">
                        <div>
                            <strong>{{ $r->fullName() }}</strong>
                            <span style="color:var(--text-muted);font-size:0.75rem;margin-left:6px;">{{ $r->purok ?? '' }}</span>
                        </div>
                        <span style="font-size:0.6875rem;color:var(--blue-600);font-weight:600;">Select</span>
                    </button>
                    @endforeach
                </div>
                @endif
            </div>
            @if($selectedResident)
            <div class="alert alert-success" style="margin-top:8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span>Selected: <strong>{{ $selectedResident->fullName() }}</strong></span>
            </div>
            @endif
        </div>

        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                Health Record Details
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label class="form-label">Record Type *</label>
                    <select wire:model="record_type" class="form-select">
                        <option value="vaccination">Vaccination</option>
                        <option value="medical_referral">Medical Referral</option>
                        <option value="health_program">Health Program</option>
                        <option value="checkup">Checkup</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Title *</label>
                    <input type="text" wire:model="title" class="form-input">
                </div>
                <div>
                    <label class="form-label">Date *</label>
                    <input type="date" wire:model="record_date" class="form-input">
                </div>
                <div>
                    <label class="form-label">Provider / Hospital</label>
                    <input type="text" wire:model="provider" class="form-input">
                </div>
                <div>
                    <label class="form-label">Result</label>
                    <input type="text" wire:model="result" class="form-input">
                </div>
                <div>
                    <label class="form-label">Next Schedule</label>
                    <input type="date" wire:model="next_schedule" class="form-input">
                </div>
                <div style="grid-column:span 2;">
                    <label class="form-label">Description</label>
                    <textarea wire:model="description" rows="3" class="form-textarea"></textarea>
                </div>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:8px;padding:16px 0 32px;">
            <a href="{{ route('health.index') }}" wire:navigate class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary btn-lg">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Save Record
            </button>
        </div>

    </form>
</div>
</div>
