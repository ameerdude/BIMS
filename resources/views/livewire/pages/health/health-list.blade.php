<?php

use Livewire\Volt\Component;
use App\Models\HealthRecord;
use Livewire\WithPagination;

new #[Layout("layouts.app")] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterType = '';

    public function updatingSearch() { $this->resetPage(); }

    public function deleteRecord(HealthRecord $record): void
    {
        $record->delete();
        $this->dispatch('refresh');
    }

    public function records()
    {
        return HealthRecord::with('resident', 'recorder')
            ->when($this->search, fn($q) => $q->whereHas('resident', fn($r) => $r->where(fn($s) => $s->where('first_name', 'like', "%{$this->search}%")->orWhere('last_name', 'like', "%{$this->search}%"))))
            ->when($this->filterType, fn($q) => $q->where('record_type', $this->filterType))
            ->latest()
            ->paginate(20);
    }
}; ?>

<div>
<div style="max-width:1200px;margin:0 auto;">

    {{-- Page Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <div>
            <h1 style="font-size:1.125rem;font-weight:800;color:var(--text-primary);margin:0;">Health Records Log</h1>
            <p style="font-size:0.8125rem;color:var(--text-secondary);margin:2px 0 0;">
                Audit trail of all health records. Add records from the
                <a href="{{ route('residents.index') }}" wire:navigate style="color:var(--blue-600);font-weight:600;">Resident Information</a> page.
            </p>
        </div>
    </div>

    <div class="card" style="margin-bottom:16px;max-width:600px;">
        <div class="card-body" style="display:flex;gap:12px;flex-wrap:wrap;">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by resident name..." class="form-input">
            <select wire:model.live="filterType" class="form-select">
                <option value="">All Types</option>
                <option value="vaccination">Vaccination</option>
                <option value="medical_referral">Medical Referral</option>
                <option value="health_program">Health Program</option>
                <option value="checkup">Checkup</option>
                <option value="other">Other</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="card" style="overflow:hidden;">
        <table class="table">
            <thead>
                <tr>
                    <th>Resident</th>
                    <th>Type</th>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Provider</th>
                    <th>Result</th>
                    <th>Next Schedule</th>
                    <th>Recorded By</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->records() as $rec)
                <tr>
                    <td>
                        @if($rec->resident)
                        <a href="{{ route('residents.show', $rec->resident) }}" wire:navigate style="font-size:0.8125rem;color:var(--blue-600);font-weight:600;text-decoration:none;">
                            {{ $rec->resident->fullName() }}
                        </a>
                        @else
                        <span style="font-size:0.8125rem;color:var(--text-muted);">N/A</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-blue" style="font-size:0.625rem;">{{ $rec->getTypeLabel() }}</span>
                    </td>
                    <td>
                        <span style="font-size:0.8125rem;font-weight:600;color:var(--text-primary);">{{ $rec->title }}</span>
                    </td>
                    <td>
                        <span style="font-size:0.8125rem;color:var(--text-muted);">{{ $rec->record_date->format('M d, Y') }}</span>
                    </td>
                    <td>
                        <span style="font-size:0.8125rem;color:var(--text-muted);">{{ $rec->provider ?? 'N/A' }}</span>
                    </td>
                    <td>
                        <span style="font-size:0.8125rem;color:var(--text-secondary);">{{ $rec->result ?? 'N/A' }}</span>
                    </td>
                    <td>
                        @if($rec->next_schedule)
                        <span style="font-size:0.8125rem;color:var(--blue-600);font-weight:600;">{{ $rec->next_schedule->format('M d, Y') }}</span>
                        @else
                        <span style="font-size:0.8125rem;color:var(--text-muted);">N/A</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-size:0.8125rem;color:var(--text-muted);">{{ $rec->recorder->name ?? 'Staff' }}</span>
                    </td>
                    <td style="text-align:right;">
                        <button wire:click="deleteRecord({{ $rec->id }})" wire:confirm="Delete this health record?" class="btn-table btn-table-danger">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                            Delete
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:40px;color:var(--text-muted);font-size:0.8125rem;">
                        No health records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:12px 16px;">{{ $this->records()->links() }}</div>
    </div>

</div>
</div>
