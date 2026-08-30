<?php

use Livewire\Volt\Component;
use App\Models\Business;
use App\Models\Resident;

new #[Layout("layouts.app")] class extends Component
{
    public string $business_name = '';
    public int $owner_id = 0;
    public string $owner_name = '';
    public string $owner_search = '';
    public $selectedOwner = null;
    public string $business_type = '';
    public string $business_address = '';
    public string $date_registered = '';

    public function updatedOwnerId($v)
    {
        $this->selectedOwner = $v ? Resident::find($v) : null;
        if ($this->selectedOwner) {
            $this->owner_name = $this->selectedOwner->fullName();
        }
    }

    public function searchOwners()
    {
        if (strlen($this->owner_search) < 2) return collect();
        return Resident::where('is_active', true)
            ->where(fn($q) => $q->where('first_name', 'like', "%{$this->owner_search}%")
                ->orWhere('last_name', 'like', "%{$this->owner_search}%"))
            ->limit(8)->get();
    }

    public function save(): void
    {
        $this->validate([
            'business_name' => 'required',
            'owner_name' => 'required',
            'business_type' => 'required',
        ]);

        Business::create([
            'business_name' => $this->business_name,
            'owner_id' => $this->owner_id ?: null,
            'owner_name' => $this->owner_name,
            'business_type' => $this->business_type,
            'business_address' => $this->business_address ?: null,
            'date_registered' => $this->date_registered ?: null,
        ]);

        $this->redirect(route('businesses.index'), navigate: true);
    }
}; ?>

<div>
<div style="max-width:800px;margin:0 auto;">

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <div>
            <h1 style="font-size:1.125rem;font-weight:800;color:var(--text-primary);margin:0;">Register Business</h1>
            <p style="font-size:0.8125rem;color:var(--text-secondary);margin:2px 0 0;">Register a new business under a resident owner</p>
        </div>
        <a href="{{ route('businesses.index') }}" wire:navigate class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back
        </a>
    </div>

    <form wire:submit="save">

        {{-- Business Details --}}
        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2-2v16"/></svg>
                Business Information
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label class="form-label">Business Name *</label>
                    <input type="text" wire:model="business_name" class="form-input" placeholder="e.g. Juan's Sari-Sari Store">
                </div>
                <div>
                    <label class="form-label">Business Type *</label>
                    <input type="text" wire:model="business_type" class="form-input" placeholder="e.g. Sari-Sari Store, Restaurant">
                </div>
                <div>
                    <label class="form-label">Date Registered</label>
                    <input type="date" wire:model="date_registered" class="form-input">
                </div>
                <div>
                    <label class="form-label">Address</label>
                    <input type="text" wire:model="business_address" class="form-input" placeholder="Business address">
                </div>
            </div>
        </div>

        {{-- Owner --}}
        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4-4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Business Owner
            </div>
            <p style="font-size:0.75rem;color:var(--text-muted);margin-bottom:12px;">Search for a registered resident or type the owner's name manually.</p>

            <div>
                <label class="form-label">Search Resident Owner</label>
                <input type="text" wire:model.live.debounce.300ms="owner_search" placeholder="Type name to search..." class="form-input">
                @if($this->searchOwners()->count())
                <div style="border:1px solid var(--border);border-radius:var(--radius);max-height:200px;overflow-y:auto;margin-top:4px;">
                    @foreach($this->searchOwners() as $r)
                    <button type="button" wire:click="$set('owner_id', {{ $r->id }}); $set('owner_search', '')"
                        style="display:flex;justify-content:space-between;align-items:center;width:100%;text-align:left;padding:8px 12px;border:none;border-bottom:1px solid var(--border-light);background:{{ $owner_id == $r->id ? 'var(--blue-50)' : 'var(--surface)' }};cursor:pointer;font-family:inherit;font-size:0.8125rem;">
                        <div>
                            <strong>{{ $r->fullName() }}</strong>
                            <span style="color:var(--text-muted);font-size:0.75rem;margin-left:6px;">{{ $r->purok ?? '' }} · {{ $r->resident_id_number }}</span>
                        </div>
                        <span style="font-size:0.6875rem;color:var(--blue-600);font-weight:600;">Select</span>
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            @if($selectedOwner)
            <div class="alert alert-success" style="margin-top:8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span>Linked to resident: <strong>{{ $selectedOwner->fullName() }}</strong></span>
            </div>
            @endif

            <div style="margin-top:12px;">
                <label class="form-label">Owner Name *</label>
                <input type="text" wire:model="owner_name" class="form-input" placeholder="Full name" {{ $selectedOwner ? 'readonly style=\"background:var(--navy-50);\"' : '' }}>
            </div>
        </div>

        {{-- Submit --}}
        <div style="display:flex;justify-content:flex-end;gap:8px;padding:16px 0 32px;">
            <a href="{{ route('businesses.index') }}" wire:navigate class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary btn-lg">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Register Business
            </button>
        </div>

    </form>
</div>
</div>
