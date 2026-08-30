<?php

use Livewire\Volt\Component;
use App\Models\ServiceRequest;
use App\Models\Resident;
use App\Models\User;

new #[Layout("layouts.app")] class extends Component
{
    public string $requester_name = '';
    public string $requester_contact = '';
    public ?int $resident_id = null;
    public string $category = 'road_repair';
    public string $subject = '';
    public string $description = '';
    public string $location = '';
    public string $priority = 'medium';
    public ?int $assigned_to = null;
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

    public function staff() { return User::where('is_active', true)->orderBy('name')->get(); }

    public function save(): void
    {
        $this->validate(['requester_name' => 'required', 'subject' => 'required', 'description' => 'required']);
        ServiceRequest::create([
            'request_number' => ServiceRequest::generateNumber(),
            'resident_id' => $this->resident_id,
            'requester_name' => $this->requester_name,
            'requester_contact' => $this->requester_contact,
            'category' => $this->category,
            'subject' => $this->subject,
            'description' => $this->description,
            'location' => $this->location,
            'priority' => $this->priority,
            'assigned_to' => $this->assigned_to,
            'created_by' => auth()->id(),
        ]);
        $this->redirect(route('services.index'), navigate: true);
    }
}; ?>

<div>
<div style="max-width:800px;margin:0 auto;">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <div>
            <h1 style="font-size:1.125rem;font-weight:800;color:var(--text-primary);margin:0;">New Service Request</h1>
            <p style="font-size:0.8125rem;color:var(--text-secondary);margin:2px 0 0;">Submit a community service or maintenance request</p>
        </div>
        <a href="{{ route('services.index') }}" wire:navigate class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back
        </a>
    </div>

    <form wire:submit="save">

        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4-4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Requester Information
            </div>
            <div>
                <label class="form-label">Link to Resident (Optional)</label>
                <input type="text" wire:model.live.debounce.300ms="searchTerm" placeholder="Type resident name to search..." class="form-input">
                @if($this->residents()->count())
                <div style="border:1px solid var(--border);border-radius:var(--radius);max-height:200px;overflow-y:auto;margin-top:4px;">
                    @foreach($this->residents() as $r)
                    <button type="button" wire:click="$set('resident_id', {{ $r->id }}); $set('searchTerm', '')"
                        style="display:flex;justify-content:space-between;align-items:center;width:100%;text-align:left;padding:8px 12px;border:none;border-bottom:1px solid var(--border-light);background:{{ $resident_id == $r->id ? 'var(--blue-50)' : 'var(--surface)' }};cursor:pointer;font-family:inherit;font-size:0.8125rem;">
                        <div><strong>{{ $r->fullName() }}</strong> <span style="color:var(--text-muted);font-size:0.75rem;">{{ $r->purok ?? '' }}</span></div>
                        <span style="font-size:0.6875rem;color:var(--blue-600);font-weight:600;">Select</span>
                    </button>
                    @endforeach
                </div>
                @endif
            </div>
            @if($selectedResident)
            <div class="alert alert-success" style="margin-top:8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span>Linked to: <strong>{{ $selectedResident->fullName() }}</strong></span>
            </div>
            @endif
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:12px;">
                <div>
                    <label class="form-label">Requester Name *</label>
                    <input type="text" wire:model="requester_name" class="form-input">
                </div>
                <div>
                    <label class="form-label">Contact Number</label>
                    <input type="text" wire:model="requester_contact" class="form-input">
                </div>
            </div>
        </div>

        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.32 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                Request Details
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label class="form-label">Category *</label>
                    <select wire:model="category" class="form-select">
                        <option value="road_repair">Road Repair</option>
                        <option value="drainage">Drainage</option>
                        <option value="garbage">Garbage Collection</option>
                        <option value="noise">Noise Complaint</option>
                        <option value="lighting">Street Lighting</option>
                        <option value="water">Water Supply</option>
                        <option value="electrical">Electrical</option>
                        <option value="flooding">Flooding</option>
                        <option value="tree_cutting">Tree Cutting</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Priority *</label>
                    <select wire:model="priority" class="form-select">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
            </div>
            <div class="form-group" style="margin-top:12px;">
                <label class="form-label">Subject *</label>
                <input type="text" wire:model="subject" placeholder="Brief description of the issue" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Description *</label>
                <textarea wire:model="description" rows="4" class="form-textarea" placeholder="Detailed description..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Location</label>
                <input type="text" wire:model="location" placeholder="Specific location/address" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Assign To</label>
                <select wire:model="assigned_to" class="form-select">
                    <option value="">Unassigned</option>
                    @foreach($this->staff() as $s)
                    <option value="{{ $s->id }}">{{ $s->name }} ({{ ucfirst($s->role) }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:8px;padding:16px 0 32px;">
            <a href="{{ route('services.index') }}" wire:navigate class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary btn-lg">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Submit Request
            </button>
        </div>

    </form>
</div>
</div>
