<?php

use Livewire\Volt\Component;
use App\Models\BarangayId;
use Livewire\WithPagination;

new #[Layout("layouts.app")] class extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch() { $this->resetPage(); }

    public function ids()
    {
        return BarangayId::with('resident', 'issuer')
            ->when($this->search, fn($q) => $q->where(function ($w) {
                $w->where('id_number', 'like', "%{$this->search}%")
                  ->orWhereHas('resident', fn($r) => $r->where(fn($s) => $s->where('first_name', 'like', "%{$this->search}%")->orWhere('last_name', 'like', "%{$this->search}%")));
            }))
            ->latest()
            ->paginate(20);
    }

    public function totalIssued(): int
    {
        return BarangayId::count();
    }

    public function activeCount(): int
    {
        return BarangayId::where('status', 'active')->count();
    }
}; ?>

<div>
<div style="max-width:1200px;margin:0 auto;">

    {{-- Page Header --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Barangay IDs Log</h1>
            <p class="page-subtitle">
                Audit trail of all issued Barangay IDs. Issue new IDs from the
                <a href="{{ route('residents.index') }}" wire:navigate style="color:var(--blue-600);font-weight:600;">Resident Information</a> page.
            </p>
        </div>
        <a href="{{ route('ids.mass-print') }}" wire:navigate class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            📄 Mass Print IDs
        </a>
    </div>

    {{-- Summary Stats --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px;">
        <div class="card" style="padding:16px;">
            <div style="font-size:0.6875rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.04em;font-weight:600;">Total IDs</div>
            <div style="font-size:1.25rem;font-weight:800;color:var(--text-primary);margin-top:4px;">{{ number_format($this->totalIssued()) }}</div>
        </div>
        <div class="card" style="padding:16px;">
            <div style="font-size:0.6875rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.04em;font-weight:600;">Active</div>
            <div style="font-size:1.25rem;font-weight:800;color:var(--green-600);margin-top:4px;">{{ number_format($this->activeCount()) }}</div>
        </div>
        <div class="card" style="padding:16px;">
            <div style="font-size:0.6875rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.04em;font-weight:600;">Cancelled</div>
            <div style="font-size:1.25rem;font-weight:800;color:var(--text-muted);margin-top:4px;">{{ number_format($this->totalIssued() - $this->activeCount()) }}</div>
        </div>
    </div>

    <div class="card" style="margin-bottom:16px;">
        <div class="card-body" style="display:flex;gap:12px;">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search ID number or resident name..." class="form-input" style="max-width:400px;">
        </div>
    </div>

    {{-- Table --}}
    <div class="card" style="overflow:hidden;">
        <table class="table">
            <thead>
                <tr>
                    <th>ID Number</th>
                    <th>Resident</th>
                    <th>Version</th>
                    <th>Issued By</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->ids() as $id)
                <tr>
                    <td>
                        <span class="font-mono" style="font-size:0.75rem;font-weight:700;color:var(--blue-600);">{{ $id->id_number }}</span>
                    </td>
                    <td>
                        @if($id->resident)
                        <a href="{{ route('residents.show', $id->resident) }}" wire:navigate style="font-size:0.8125rem;color:var(--blue-600);font-weight:600;text-decoration:none;">
                            {{ $id->resident->fullName() }}
                        </a>
                        @else
                        <span style="font-size:0.8125rem;color:var(--text-muted);">N/A</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-size:0.8125rem;color:var(--text-secondary);">v{{ $id->version }}</span>
                    </td>
                    <td>
                        <span style="font-size:0.8125rem;color:var(--text-muted);">{{ $id->issuer->name ?? 'Staff' }}</span>
                    </td>
                    <td>
                        <span style="font-size:0.8125rem;color:var(--text-muted);">{{ $id->issued_at->format('M d, Y') }}</span>
                    </td>
                    <td>
                        @if($id->status === 'active')
                            <span class="badge badge-green">Active</span>
                        @else
                            <span class="badge badge-red">{{ ucfirst($id->status) }}</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        @if($id->status === 'active')
                        <a href="{{ route('ids.print', $id) }}" wire:navigate class="btn-table btn-table-print">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v6"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                            Print
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);font-size:0.8125rem;">
                        No IDs found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:12px 16px;">{{ $this->ids()->links() }}</div>
    </div>

</div>
</div>
