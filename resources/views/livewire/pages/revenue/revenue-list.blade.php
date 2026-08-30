<?php

use Livewire\Volt\Component;
use App\Models\RevenueRecord;
use Livewire\WithPagination;

new #[Layout("layouts.app")] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterCategory = '';

    public function updatingSearch() { $this->resetPage(); }

    public function records()
    {
        return RevenueRecord::with('payer', 'receiver')
            ->when($this->search, fn($q) => $q->where(function ($w) {
                $w->where('or_number', 'like', "%{$this->search}%")
                  ->orWhere('payer_name', 'like', "%{$this->search}%");
            }))
            ->when($this->filterCategory, fn($q) => $q->where('category', $this->filterCategory))
            ->latest()
            ->paginate(20);
    }

    public function totalRevenue()
    {
        return RevenueRecord::sum('amount');
    }

    public function deleteRecord(RevenueRecord $record): void
    {
        $record->delete();
    }
}; ?>

<div>
<div style="max-width:1200px;margin:0 auto;">

    <div class="page-header">
        <div>
            <h1 class="page-title">Revenue / Treasury</h1>
            <p class="page-subtitle">Payment records and financial tracking</p>
        </div>
        <a href="{{ route('revenue.create') }}" wire:navigate class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Record Payment
        </a>
    </div>

    <div class="card" style="padding:20px;margin-bottom:16px;background:linear-gradient(135deg,#f0fdf4 0%,#dcfce7 100%);border-color:#bbf7d0;">
        <div style="font-size:0.75rem;font-weight:600;color:var(--green-700);text-transform:uppercase;letter-spacing:0.04em;">Total Revenue (All Time)</div>
        <div style="font-size:1.5rem;font-weight:800;color:var(--green-700);margin-top:4px;">₱ {{ number_format($this->totalRevenue(), 2) }}</div>
    </div>

    <div class="card" style="margin-bottom:16px;max-width:600px;">
        <div class="card-body" style="display:flex;gap:12px;flex-wrap:wrap;">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search OR number or payer..." class="form-input">
            <select wire:model.live="filterCategory" class="form-select">
                <option value="">All Categories</option>
                <option value="business_permit">Business Permit</option>
                <option value="barangay_clearance">Barangay Clearance</option>
                <option value="certificate">Certificate</option>
                <option value="id_card">ID Card</option>
                <option value="penalty">Penalty</option>
                <option value="donation">Donation</option>
            </select>
        </div>
    </div>

    <div class="card" style="overflow:hidden;">
        <table class="table">
            <thead>
                <tr>
                    <th>OR Number</th>
                    <th>Category</th>
                    <th>Payer</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Method</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->records() as $rec)
                <tr>
                    <td><span class="font-mono" style="font-size:0.75rem;font-weight:700;color:var(--blue-600);">{{ $rec->or_number }}</span></td>
                    <td><span style="font-size:0.8125rem;color:var(--text-secondary);">{{ $rec->getCategoryLabel() }}</span></td>
                    <td><span style="font-size:0.8125rem;font-weight:600;">{{ $rec->payer_name }}</span></td>
                    <td><span style="font-size:0.8125rem;font-weight:700;color:var(--green-600);">₱ {{ number_format($rec->amount, 2) }}</span></td>
                    <td><span style="font-size:0.8125rem;color:var(--text-muted);">{{ $rec->payment_date->format('M d, Y') }}</span></td>
                    <td><span class="badge badge-gray">{{ str_replace('_', ' ', ucfirst($rec->payment_method)) }}</span></td>
                    <td style="text-align:right;">
                        <button wire:click="deleteRecord({{ $rec->id }})" wire:confirm="Delete this revenue record?" class="btn-table btn-table-danger">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);font-size:0.8125rem;">No records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:12px 16px;">{{ $this->records()->links() }}</div>
    </div>

</div>
</div>
