<?php

use Livewire\Volt\Component;
use App\Models\DocumentIssued;
use Livewire\WithPagination;

new #[Layout("layouts.app")] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterType = '';
    public string $filterStatus = '';

    public function updatingSearch() { $this->resetPage(); }

    public function documents()
    {
        return DocumentIssued::with('resident', 'issuer')
            ->when($this->search, fn($q) => $q->where(function ($w) {
                $w->where('control_number', 'like', "%{$this->search}%")
                  ->orWhereHas('resident', fn($r) => $r->where(fn($s) => $s->where('first_name', 'like', "%{$this->search}%")->orWhere('last_name', 'like', "%{$this->search}%")));
            }))
            ->when($this->filterType, fn($q) => $q->where('document_type', $this->filterType))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(20);
    }

    public function totalIssued(): int
    {
        return DocumentIssued::count();
    }

    public function activeCount(): int
    {
        return DocumentIssued::where('status', 'active')->count();
    }
}; ?>

<div>
<div style="max-width:1200px;margin:0 auto;">

    <div class="page-header">
        <div>
            <h1 class="page-title">Documents Log</h1>
            <p class="page-subtitle">
                Audit trail of all issued documents. Issue new documents from the
                <a href="{{ route('residents.index') }}" wire:navigate style="color:var(--blue-600);font-weight:600;">Resident Information</a> page.
            </p>
        </div>
    </div>

    {{-- Summary Stats --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px;">
        <div class="card" style="padding:16px;">
            <div style="font-size:0.6875rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.04em;font-weight:600;">Total Issued</div>
            <div style="font-size:1.25rem;font-weight:800;color:var(--text-primary);margin-top:4px;">{{ number_format($this->totalIssued()) }}</div>
        </div>
        <div class="card" style="padding:16px;">
            <div style="font-size:0.6875rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.04em;font-weight:600;">Active</div>
            <div style="font-size:1.25rem;font-weight:800;color:var(--green-600);margin-top:4px;">{{ number_format($this->activeCount()) }}</div>
        </div>
        <div class="card" style="padding:16px;">
            <div style="font-size:0.6875rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.04em;font-weight:600;">This Page</div>
            <div style="font-size:1.25rem;font-weight:800;color:var(--blue-600);margin-top:4px;">{{ $this->documents()->count() }}</div>
        </div>
    </div>

    <div class="card" style="margin-bottom:16px;">
        <div class="card-body" style="display:flex;gap:12px;flex-wrap:wrap;">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search control number or resident name..." class="form-input">
            <select wire:model.live="filterType" class="form-select">
                <option value="">All Document Types</option>
                <option value="barangay_clearance">Barangay Clearance</option>
                <option value="certificate_of_residency">Certificate of Residency</option>
                <option value="certificate_of_indigency">Certificate of Indigency</option>
                <option value="certificate_of_good_moral">Good Moral Character</option>
                <option value="business_clearance">Business Clearance</option>
            </select>
            <select wire:model.live="filterStatus" class="form-select">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="expired">Expired</option>
                <option value="revoked">Revoked</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="card" style="overflow:hidden;">
        <table class="table">
            <thead>
                <tr>
                    <th>Control No.</th>
                    <th>Type</th>
                    <th>Resident</th>
                    <th>Purpose</th>
                    <th>Issued</th>
                    <th>By</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->documents() as $doc)
                <tr>
                    <td>
                        <span class="font-mono" style="font-size:0.75rem;font-weight:700;color:var(--blue-600);">{{ $doc->control_number }}</span>
                    </td>
                    <td>
                        <span style="font-size:0.8125rem;font-weight:600;">{{ $doc->getDocumentTypeLabel() }}</span>
                    </td>
                    <td>
                        @if($doc->resident)
                        <a href="{{ route('residents.show', $doc->resident) }}" wire:navigate style="font-size:0.8125rem;color:var(--blue-600);font-weight:600;text-decoration:none;">
                            {{ $doc->resident->fullName() }}
                        </a>
                        @else
                        <span style="font-size:0.8125rem;color:var(--text-muted);">N/A</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-size:0.8125rem;color:var(--text-secondary);">{{ $doc->purpose ?? 'N/A' }}</span>
                    </td>
                    <td>
                        <span style="font-size:0.8125rem;color:var(--text-muted);">{{ $doc->issued_at->format('M d, Y') }}</span>
                    </td>
                    <td>
                        <span style="font-size:0.8125rem;color:var(--text-muted);">{{ $doc->issuer->name ?? 'Staff' }}</span>
                    </td>
                    <td>
                        @if($doc->status === 'active')
                            <span class="badge badge-green">Active</span>
                        @elseif($doc->status === 'expired')
                            <span class="badge badge-amber">Expired</span>
                        @else
                            <span class="badge badge-red">{{ ucfirst($doc->status) }}</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <a href="{{ route('documents.print', $doc) }}" wire:navigate class="btn-table btn-table-print">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v6"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                            Print
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);font-size:0.8125rem;">
                        No documents found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:12px 16px;">{{ $this->documents()->links() }}</div>
    </div>

</div>
</div>
