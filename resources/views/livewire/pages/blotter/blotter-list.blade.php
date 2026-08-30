<?php

use Livewire\Volt\Component;
use App\Models\BlotterRecord;
use Livewire\WithPagination;

new #[Layout("layouts.app")] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';

    public function updatingSearch() { $this->resetPage(); }

    public function blotters()
    {
        return BlotterRecord::with('parties')
            ->when($this->search, fn($q) => $q->where(function ($w) {
                $w->where('blotter_number', 'like', "%{$this->search}%")
                  ->orWhere('location', 'like', "%{$this->search}%")
                  ->orWhereHas('parties', fn($p) => $p->where('name', 'like', "%{$this->search}%"));
            }))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(20);
    }
}; ?>

<div>
<div style="max-width:1200px;margin:0 auto;">

    <div class="page-header">
        <div>
            <h1 class="page-title">Blotter Records</h1>
            <p class="page-subtitle">Incident reports and mediation records</p>
        </div>
        <a href="{{ route('blotter.create') }}" wire:navigate class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Blotter
        </a>
    </div>

    <div class="card" style="margin-bottom:16px;max-width:600px;">
        <div class="card-body" style="display:flex;gap:12px;">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by number, party name, or location..." class="form-input">
            <select wire:model.live="filterStatus" class="form-select">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="under_mediation">Under Mediation</option>
                <option value="settled">Settled</option>
                <option value="endorsed_to_police">Endorsed to Police</option>
            </select>
        </div>
    </div>

    <div class="card" style="overflow:hidden;">
        <table class="table">
            <thead>
                <tr>
                    <th>Blotter No.</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Parties</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->blotters() as $b)
                <tr>
                    <td><span class="font-mono" style="font-size:0.75rem;font-weight:700;color:var(--blue-600);">{{ $b->blotter_number }}</span></td>
                    <td><span style="font-size:0.8125rem;font-weight:600;">{{ str_replace('_', ' ', ucfirst($b->incident_type)) }}</span></td>
                    <td><span style="font-size:0.8125rem;color:var(--text-muted);">{{ $b->incident_datetime->format('M d, Y') }}</span></td>
                    <td>
                        <div style="display:flex;flex-wrap:wrap;gap:4px;">
                            @foreach($b->parties->take(2) as $p)
                            <span class="badge {{ $p->role === 'complainant' ? 'badge-blue' : 'badge-red' }}" style="font-size:0.625rem;">{{ $p->name }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td>
                        @if($b->status === 'pending')
                            <span class="badge badge-amber">{{ $b->getStatusLabel() }}</span>
                        @elseif($b->status === 'settled')
                            <span class="badge badge-green">{{ $b->getStatusLabel() }}</span>
                        @else
                            <span class="badge badge-red">{{ $b->getStatusLabel() }}</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <div style="display:flex;gap:6px;justify-content:flex-end;">
                            <a href="{{ route('blotter.show', $b) }}" wire:navigate class="btn-table btn-table-view">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                View
                            </a>
                            <a href="{{ route('blotter.print', $b) }}" wire:navigate class="btn-table btn-table-print">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v6"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                                Print
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);font-size:0.8125rem;">No blotter records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:12px 16px;">{{ $this->blotters()->links() }}</div>
    </div>

</div>
</div>
