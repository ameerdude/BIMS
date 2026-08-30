<?php

use Livewire\Volt\Component;
use App\Models\BlotterRecord;

new #[Layout("layouts.app")] class extends Component
{
    public BlotterRecord $blotter;
    public string $newStatus = '';
    public string $mediationDate = '';
    public string $mediationTime = '';
    public string $mediationNotes = '';

    public function mount(BlotterRecord $blotter): void
    {
        $this->blotter = $blotter->load(['parties', 'mediationSchedules', 'recorder']);
        $this->newStatus = $blotter->status;
    }

    public function updateStatus(): void
    {
        if (!$this->newStatus) return;
        $this->blotter->update(['status' => $this->newStatus]);
        $this->blotter = $this->blotter->fresh(['parties', 'mediationSchedules', 'recorder']);
    }

    public function scheduleMediation(): void
    {
        $this->validate([
            'mediationDate' => 'required|date',
            'mediationTime' => 'required',
        ]);
        \App\Models\MediationSchedule::create([
            'blotter_record_id' => $this->blotter->id,
            'scheduled_date' => $this->mediationDate,
            'scheduled_time' => $this->mediationTime,
            'notes' => $this->mediationNotes,
            'status' => 'scheduled',
        ]);
        $this->mediationDate = '';
        $this->mediationTime = '';
        $this->mediationNotes = '';
        $this->blotter = $this->blotter->fresh(['parties', 'mediationSchedules', 'recorder']);
    }
}; ?>

<div>
<div style="max-width:1100px;margin:0 auto;">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <a href="{{ route('blotter.index') }}" wire:navigate class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back to Blotters
        </a>
        <a href="{{ route('blotter.print', $blotter) }}" wire:navigate class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v6"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Print Record
        </a>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;">

    <div>
        {{-- Incident Details --}}
        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                Incident Details
            </div>
            <table style="width:100%;font-size:0.8125rem;border-collapse:collapse;">
                <tr style="border-bottom:1px solid var(--border-light);">
                    <td style="padding:7px 0;color:var(--text-muted);width:35%;">Blotter Number</td>
                    <td style="padding:7px 0;font-weight:700;font-family:'JetBrains Mono',monospace;color:var(--blue-600);">{{ $blotter->blotter_number }}</td>
                </tr>
                <tr style="border-bottom:1px solid var(--border-light);">
                    <td style="padding:7px 0;color:var(--text-muted);">Incident Type</td>
                    <td style="padding:7px 0;font-weight:600;">{{ str_replace('_', ' ', ucfirst($blotter->incident_type)) }}</td>
                </tr>
                <tr style="border-bottom:1px solid var(--border-light);">
                    <td style="padding:7px 0;color:var(--text-muted);">Date & Time</td>
                    <td style="padding:7px 0;font-weight:600;">{{ $blotter->incident_datetime->format('M d, Y h:i A') }}</td>
                </tr>
                <tr style="border-bottom:1px solid var(--border-light);">
                    <td style="padding:7px 0;color:var(--text-muted);">Location</td>
                    <td style="padding:7px 0;font-weight:600;">{{ $blotter->location }}</td>
                </tr>
                <tr style="border-bottom:1px solid var(--border-light);">
                    <td style="padding:7px 0;color:var(--text-muted);">Recorded By</td>
                    <td style="padding:7px 0;font-weight:600;">{{ $blotter->recorder->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding:7px 0;color:var(--text-muted);">Narrative</td>
                    <td style="padding:7px 0;font-weight:500;color:var(--text-secondary);line-height:1.6;">{{ $blotter->narrative }}</td>
                </tr>
            </table>
        </div>

        {{-- Parties Involved --}}
        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                Parties Involved
            </div>
            @forelse($blotter->parties as $p)
            <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border-light);">
                <span class="badge {{ $p->role === 'complainant' ? 'badge-blue' : 'badge-red' }}">{{ ucfirst($p->role) }}</span>
                <span style="font-size:0.8125rem;font-weight:600;">{{ $p->name }}</span>
                @if($p->resident)
                <span style="font-size:0.6875rem;color:var(--text-muted);font-family:'JetBrains Mono',monospace;">{{ $p->resident->resident_id_number }}</span>
                @endif
            </div>
            @empty
            <div style="padding:16px 0;text-align:center;color:var(--text-muted);font-size:0.8125rem;">No parties recorded.</div>
            @endforelse
        </div>

        {{-- Mediation Schedules --}}
        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Mediation Schedules
            </div>
            @forelse($blotter->mediationSchedules as $ms)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border-light);">
                <div>
                    <div style="font-size:0.8125rem;font-weight:600;">{{ $ms->scheduled_date->format('M d, Y') }} at {{ $ms->scheduled_time }}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted);">{{ $ms->notes ?? 'No notes' }}</div>
                </div>
                <span class="badge {{ $ms->status === 'completed' ? 'badge-green' : 'badge-amber' }}">{{ ucfirst($ms->status) }}</span>
            </div>
            @empty
            <div style="padding:16px 0;text-align:center;color:var(--text-muted);font-size:0.8125rem;">No mediation scheduled.</div>
            @endforelse
        </div>
    </div>

    <div>
        {{-- Update Status --}}
        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Update Status
            </div>
            <form wire:submit="updateStatus">
                <div class="form-group">
                    <select wire:model="newStatus" class="form-select">
                        <option value="pending">Pending</option>
                        <option value="under_mediation">Under Mediation</option>
                        <option value="settled">Settled</option>
                        <option value="endorsed_to_police">Endorsed to Police</option>
                        <option value="endorsed_to_court">Endorsed to Court</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Update Status
                </button>
            </form>
        </div>

        {{-- Schedule Mediation --}}
        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Schedule Mediation
            </div>
            <form wire:submit="scheduleMediation">
                <div class="form-group">
                    <label class="form-label">Date *</label>
                    <input type="date" wire:model="mediationDate" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Time *</label>
                    <input type="time" wire:model="mediationTime" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea wire:model="mediationNotes" rows="2" class="form-textarea" placeholder="Mediation notes..."></textarea>
                </div>
                <button type="submit" class="btn btn-success" style="width:100%;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Schedule
                </button>
            </form>
        </div>
    </div>

    </div>
</div>
</div>
