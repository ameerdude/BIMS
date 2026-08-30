<?php

use Livewire\Volt\Component;
use App\Models\Business;
use Livewire\WithPagination;

new #[Layout("layouts.app")] class extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch() { $this->resetPage(); }

    public function businesses()
    {
        return Business::with('latestPermit')
            ->when($this->search, fn($q) => $q->where(function ($w) {
                $w->where('business_name', 'like', "%{$this->search}%")
                  ->orWhere('owner_name', 'like', "%{$this->search}%");
            }))
            ->latest()
            ->paginate(20);
    }
}; ?>

<div>
<div style="max-width:1200px;margin:0 auto;">

    <div class="page-header">
        <div>
            <h1 class="page-title">Businesses</h1>
            <p class="page-subtitle">Registered businesses in the barangay</p>
        </div>
        <a href="{{ route('businesses.create') }}" wire:navigate class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Register Business
        </a>
    </div>

    <div class="card" style="margin-bottom:16px;">
        <div class="card-body">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search business name or owner..." class="form-input" style="max-width:400px;">
        </div>
    </div>

    <div class="card" style="overflow:hidden;">
        <table class="table">
            <thead>
                <tr>
                    <th>Business Name</th>
                    <th>Owner</th>
                    <th>Type</th>
                    <th>Address</th>
                    <th>Registered</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->businesses() as $biz)
                <tr>
                    <td><span style="font-size:0.8125rem;font-weight:600;">{{ $biz->business_name }}</span></td>
                    <td>
                        @if($biz->owner)
                        <a href="{{ route('residents.show', $biz->owner) }}" wire:navigate style="font-size:0.8125rem;color:var(--blue-600);font-weight:600;text-decoration:none;">{{ $biz->owner_name }}</a>
                        @else
                        <span style="font-size:0.8125rem;color:var(--text-secondary);">{{ $biz->owner_name }}</span>
                        @endif
                    </td>
                    <td><span style="font-size:0.8125rem;color:var(--text-secondary);">{{ $biz->business_type }}</span></td>
                    <td><span style="font-size:0.8125rem;color:var(--text-muted);">{{ $biz->business_address ?? 'N/A' }}</span></td>
                    <td><span style="font-size:0.8125rem;color:var(--text-muted);">{{ $biz->date_registered?->format('M d, Y') ?? 'N/A' }}</span></td>
                    <td>
                        @if($biz->is_active)
                            <span class="badge badge-green">Active</span>
                        @else
                            <span class="badge badge-red">Inactive</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <a href="{{ route('businesses.edit', $biz) }}" wire:navigate class="btn-table btn-table-edit">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Edit
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);font-size:0.8125rem;">No businesses found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:12px 16px;">{{ $this->businesses()->links() }}</div>
    </div>

</div>
</div>
