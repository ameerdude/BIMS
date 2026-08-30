<?php

use Livewire\Volt\Component;
use App\Models\Business;

new #[Layout("layouts.app")] class extends Component
{
    public Business $business;
    public string $business_name = '';
    public string $owner_name = '';
    public string $business_type = '';
    public string $business_address = '';
    public string $date_registered = '';
    public bool $is_active = true;

    public function mount(Business $business): void
    {
        $this->business = $business;
        $this->business_name = $business->business_name;
        $this->owner_name = $business->owner_name;
        $this->business_type = $business->business_type;
        $this->business_address = $business->business_address ?? '';
        $this->date_registered = $business->date_registered?->format('Y-m-d') ?? '';
        $this->is_active = $business->is_active;
    }

    public function save(): void
    {
        $this->validate([
            'business_name' => 'required',
            'owner_name' => 'required',
            'business_type' => 'required',
        ]);
        $this->business->update([
            'business_name' => $this->business_name,
            'owner_name' => $this->owner_name,
            'business_type' => $this->business_type,
            'business_address' => $this->business_address,
            'date_registered' => $this->date_registered ?: null,
            'is_active' => $this->is_active,
        ]);
        $this->redirect(route('businesses.index'), navigate: true);
    }
}; ?>

<div>
<div style="max-width:800px;margin:0 auto;">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <div>
            <h1 style="font-size:1.125rem;font-weight:800;color:var(--text-primary);margin:0;">Edit Business</h1>
            <p style="font-size:0.8125rem;color:var(--text-secondary);margin:2px 0 0;">{{ $business->business_name }}</p>
        </div>
        <a href="{{ route('businesses.index') }}" wire:navigate class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back
        </a>
    </div>

    <form wire:submit="save">
        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2-2v16"/></svg>
                Business Information
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label class="form-label">Business Name *</label>
                    <input type="text" wire:model="business_name" class="form-input">
                </div>
                <div>
                    <label class="form-label">Business Type *</label>
                    <input type="text" wire:model="business_type" class="form-input">
                </div>
                <div>
                    <label class="form-label">Owner Name *</label>
                    <input type="text" wire:model="owner_name" class="form-input">
                </div>
                <div>
                    <label class="form-label">Date Registered</label>
                    <input type="date" wire:model="date_registered" class="form-input">
                </div>
                <div style="grid-column:span 2;">
                    <label class="form-label">Address</label>
                    <input type="text" wire:model="business_address" class="form-input">
                </div>
                <div style="grid-column:span 2;">
                    <label class="form-check" style="padding:12px;border-radius:var(--radius);border:1px solid var(--border);">
                        <input type="checkbox" wire:model="is_active" class="w-4 h-4">
                        <span class="form-check-label">Active Business</span>
                    </label>
                </div>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:8px;padding:16px 0 32px;">
            <a href="{{ route('businesses.index') }}" wire:navigate class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary btn-lg">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Save Changes
            </button>
        </div>
    </form>

</div>
</div>
