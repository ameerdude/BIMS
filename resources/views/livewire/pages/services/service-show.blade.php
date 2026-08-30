<?php

use Livewire\Volt\Component;
use App\Models\ServiceRequest;

new #[Layout("layouts.app")] class extends Component
{
    public ServiceRequest $service;

    public function mount(ServiceRequest $service): void
    {
        $this->service = $service->load(['resident', 'assignee', 'creator']);
    }

    public function updateStatus(string $status): void
    {
        $this->service->update(['status' => $status]);
        $this->service = $this->service->fresh(['resident', 'assignee', 'creator']);
    }

    public function resolve(string $notes): void
    {
        $this->validate(['notes' => 'required']);
        $this->service->update([
            'status' => 'resolved',
            'resolution_notes' => $notes,
            'resolved_at' => now(),
        ]);
        $this->service = $this->service->fresh(['resident', 'assignee', 'creator']);
    }
}; ?>

<div>
<div style="max-width:900px;margin:0 auto;">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <a href="{{ route('services.index') }}" wire:navigate class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back
        </a>
        <div style="display:flex;gap:8px;">
            @if($service->status !== 'resolved' && $service->status !== 'closed')
                <button wire:click="updateStatus('in_progress')" class="btn btn-sm btn-amber" @if($service->status === 'in_progress') disabled style="opacity:0.5;" @endif>In Progress</button>
                <button wire:click="updateStatus('resolved')" class="btn btn-sm btn-success">Resolve</button>
                <button wire:click="updateStatus('closed')" class="btn btn-sm btn-outline">Close</button>
            @else
                <span class="badge {{ $service->status === 'resolved' ? 'badge-green' : 'badge-gray' }}" style="font-size:0.875rem;padding:8px 16px;">{{ $service->getStatusLabel() }}</span>
            @endif
        </div>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;">

    <div>
        {{-- Request Details --}}
        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                Request Details
            </div>
            <table style="width:100%;font-size:0.8125rem;border-collapse:collapse;">
                <tr style="border-bottom:1px solid var(--border-light);">
                    <td style="padding:7px 0;color:var(--text-muted);width:35%;">Request Number</td>
                    <td style="padding:7px 0;font-weight:700;font-family:'JetBrains Mono',monospace;color:var(--blue-600);">{{ $service->request_number }}</td>
                </tr>
                <tr style="border-bottom:1px solid var(--border-light);">
                    <td style="padding:7px 0;color:var(--text-muted);">Subject</td>
                    <td style="padding:7px 0;font-weight:600;">{{ $service->subject }}</td>
                </tr>
                <tr style="border-bottom:1px solid var(--border-light);">
                    <td style="padding:7px 0;color:var(--text-muted);">Category</td>
                    <td style="padding:7px 0;font-weight:600;">{{ $service->getCategoryLabel() }}</td>
                </tr>
                <tr style="border-bottom:1px solid var(--border-light);">
                    <td style="padding:7px 0;color:var(--text-muted);">Priority</td>
                    <td style="padding:7px 0;">
                        <span class="badge badge-{{ $service->priority === 'urgent' ? 'red' : ($service->priority === 'high' ? 'orange' : ($service->priority === 'medium' ? 'amber' : 'gray')) }}">{{ ucfirst($service->priority) }}</span>
                    </td>
                </tr>
                <tr style="border-bottom:1px solid var(--border-light);">
                    <td style="padding:7px 0;color:var(--text-muted);">Status</td>
                    <td style="padding:7px 0;">
                        <span class="badge badge-{{ $service->status === 'resolved' ? 'green' : ($service->status === 'in_progress' ? 'amber' : ($service->status === 'open' ? 'blue' : 'gray')) }}">{{ $service->getStatusLabel() }}</span>
                    </td>
                </tr>
                <tr style="border-bottom:1px solid var(--border-light);">
                    <td style="padding:7px 0;color:var(--text-muted);">Location</td>
                    <td style="padding:7px 0;font-weight:600;">{{ $service->location ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding:7px 0;color:var(--text-muted);">Description</td>
                    <td style="padding:7px 0;font-weight:500;color:var(--text-secondary);line-height:1.6;">{{ $service->description }}</td>
                </tr>
            </table>
        </div>

        {{-- Resolution Notes --}}
        @if($service->resolution_notes)
        <div class="section-card" style="border-left:3px solid var(--green-600);">
            <div class="section-card-title" style="color:var(--green-600);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Resolution
            </div>
            <p style="font-size:0.8125rem;line-height:1.6;color:var(--text-secondary);">{{ $service->resolution_notes }}</p>
            <div style="font-size:0.75rem;color:var(--text-muted);margin-top:8px;">Resolved {{ $service->resolved_at->format('M d, Y h:i A') }}</div>
        </div>
        @endif
    </div>

    <div>
        {{-- Requester --}}
        <div class="section-card">
            <div class="section-card-title">Requester</div>
            <div style="font-size:0.875rem;font-weight:600;">{{ $service->requester_name }}</div>
            @if($service->requester_contact)
            <div style="font-size:0.8125rem;color:var(--text-muted);margin-top:2px;">{{ $service->requester_contact }}</div>
            @endif
            @if($service->resident)
            <a href="{{ route('residents.show', $service->resident) }}" wire:navigate style="font-size:0.75rem;color:var(--blue-600);font-weight:600;display:block;margin-top:6px;">View Resident Profile →</a>
            @endif
        </div>

        {{-- Assignment --}}
        <div class="section-card">
            <div class="section-card-title">Assignment</div>
            <table style="width:100%;font-size:0.8125rem;border-collapse:collapse;">
                <tr style="border-bottom:1px solid var(--border-light);">
                    <td style="padding:5px 0;color:var(--text-muted);">Assigned To</td>
                    <td style="padding:5px 0;font-weight:600;">{{ $service->assignee->name ?? 'Unassigned' }}</td>
                </tr>
                <tr style="border-bottom:1px solid var(--border-light);">
                    <td style="padding:5px 0;color:var(--text-muted);">Created By</td>
                    <td style="padding:5px 0;font-weight:600;">{{ $service->creator->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding:5px 0;color:var(--text-muted);">Date</td>
                    <td style="padding:5px 0;font-weight:600;">{{ $service->created_at->format('M d, Y') }}</td>
                </tr>
            </table>
        </div>
    </div>

    </div>
</div>
</div>
