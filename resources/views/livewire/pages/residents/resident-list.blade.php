<?php

use Livewire\Volt\Component;
use App\Models\Resident;
use Livewire\WithPagination;

new #[Layout("layouts.app")] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterPurok = '';
    public string $filterStatus = '';
    public bool $showArchived = false;

    protected $listeners = ['residentSaved' => '$refresh'];

    public function updatingSearch() { $this->resetPage(); }
    public function toggledShowArchived() { $this->resetPage(); }

    public function residents()
    {
        $query = $this->showArchived
            ? Resident::with('household')->onlyTrashed()->orderBy('deleted_at', 'desc')
            : Resident::with('household')->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('first_name', 'like', "%{$this->search}%")
                  ->orWhere('last_name', 'like', "%{$this->search}%")
                  ->orWhere('middle_name', 'like', "%{$this->search}%")
                  ->orWhere('resident_id_number', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterPurok) $query->where('purok', $this->filterPurok);
        if ($this->filterStatus === 'pwd') $query->where('is_pwd', true);
        if ($this->filterStatus === 'senior') $query->where('is_senior_citizen', true);
        if ($this->filterStatus === '4ps') $query->where('is_4ps_beneficiary', true);
        if ($this->filterStatus === 'voter') $query->where('is_registered_voter', true);
        if ($this->filterStatus === 'solo_parent') $query->where('is_solo_parent', true);

        return $query->paginate(15);
    }

    public function archiveResident(Resident $resident): void
    {
        $resident->delete();
        $this->dispatch('residentSaved');
    }

    public function restoreResident(Resident $resident): void
    {
        $resident->restore();
        $this->dispatch('residentSaved');
    }

    public function forceDelete(Resident $resident): void
    {
        $resident->forceDelete();
        $this->dispatch('residentSaved');
    }
}; ?>

<div>
    {{-- Page Header --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Resident Master List</h1>
            <p class="page-subtitle">Manage and track all registered residents</p>
        </div>
        <div style="display:flex;gap:8px;">
            <button wire:model live.click="toggleShowArchived" wire:click="" @click="$wire.set('showArchived', !$wire.showArchived)" :class="$wire.showArchived ? 'btn btn-amber' : 'btn btn-outline'">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/></svg>
                {{ $showArchived ? 'Show Active' : 'Archived' }}
            </button>
            @if(!$showArchived)
            <a href="{{ route('residents.create') }}" wire:navigate class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Resident
            </a>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success" style="margin-bottom:16px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- Filters --}}
    <div class="card" style="margin-bottom:16px;">
        <div class="card-body">
            <div class="toolbar" style="margin-bottom:0;padding:0;">
                <input type="text" wire:model.live="search" placeholder="Search by name or ID..."
                       class="form-input">
                <select wire:model.live="filterPurok" class="form-select">
                    <option value="">All Puroks</option>
                    @foreach(\App\Models\Purok::active()->get() as $pk)
                    <option value="{{ $pk->name }}">{{ $pk->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="filterStatus" class="form-select">
                    <option value="">All Status</option>
                    <option value="pwd">PWD</option>
                    <option value="senior">Senior Citizen</option>
                    <option value="4ps">4Ps Beneficiary</option>
                    <option value="voter">Registered Voter</option>
                    <option value="solo_parent">Solo Parent</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Sex</th>
                        <th>Age</th>
                        <th>Purok</th>
                        <th>Occupation</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->residents() as $resident)
                    <tr>
                        <td><span class="font-mono" style="font-size:0.75rem;color:var(--blue-600);font-weight:600;">{{ $resident->resident_id_number }}</span></td>
                        <td>
                            <a href="{{ route('residents.show', $resident) }}" wire:navigate style="font-weight:600;color:var(--text-primary);text-decoration:none;font-size:0.8125rem;">
                                {{ $resident->fullName() }}
                            </a>
                        </td>
                        <td><span style="font-size:0.8125rem;color:var(--text-secondary);">{{ ucfirst($resident->sex) }}</span></td>
                        <td><span style="font-size:0.8125rem;color:var(--text-secondary);">{{ $resident->getAge() }}</span></td>
                        <td><span style="font-size:0.8125rem;color:var(--text-secondary);">{{ $resident->purok ?? 'N/A' }}</span></td>
                        <td><span style="font-size:0.8125rem;color:var(--text-secondary);">{{ $resident->occupation ?? 'N/A' }}</span></td>
                        <td>
                            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                                @if($resident->is_pwd)<span class="badge badge-purple">PWD</span>@endif
                                @if($resident->is_senior_citizen)<span class="badge badge-amber">Senior</span>@endif
                                @if($resident->is_4ps_beneficiary)<span class="badge badge-cyan">4Ps</span>@endif
                                @if($resident->is_solo_parent)<span class="badge badge-orange">Solo Parent</span>@endif
                                @if($resident->is_indigent)<span class="badge badge-red">Indigent</span>@endif
                            </div>
                        </td>
                        <td style="text-align:right;">
                            <div style="display:flex;gap:6px;justify-content:flex-end;">
                                <a href="{{ route('residents.show', $resident) }}" wire:navigate class="btn-table btn-table-view">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    View
                                </a>
                                <a href="{{ route('residents.edit', $resident) }}" wire:navigate class="btn-table btn-table-edit">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    Edit
                                </a>
                                @if($showArchived)
                                <button wire:click="restoreResident({{ $resident->id }})" class="btn-table" style="color:var(--green-600);">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>
                                    Restore
                                </button>
                                <button wire:click="forceDelete({{ $resident->id }})" wire:confirm="Permanently delete this resident? This cannot be undone!" class="btn-table btn-table-danger">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                                    Delete
                                </button>
                                @else
                                <button wire:click="archiveResident({{ $resident->id }})" wire:confirm="Archive this resident?" class="btn-table btn-table-danger">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                                    Archive
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <div class="empty-state-icon">👥</div>
                                <div class="empty-state-title">No residents found</div>
                                <div class="empty-state-desc">Try adjusting your search or filters</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:12px 16px;border-top:1px solid var(--border);">
            {{ $this->residents()->links() }}
        </div>
    </div>
</div>
