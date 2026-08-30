<?php

use Livewire\Volt\Component;
use App\Models\ServiceRequest;
use Livewire\WithPagination;

new #[Layout("layouts.app")] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $filterPriority = '';

    public function updatingSearch() { $this->resetPage(); }

    public function requests()
    {
        return ServiceRequest::with('assignee', 'resident')
            ->when($this->search, fn($q) => $q->where(function ($w) {
                $w->where('request_number', 'like', "%{$this->search}%")
                  ->orWhere('subject', 'like', "%{$this->search}%");
            }))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterPriority, fn($q) => $q->where('priority', $this->filterPriority))
            ->latest()
            ->paginate(20);
    }
}; ?>

<div>
<div style="max-width:1200px;margin:0 auto;">

    <div class="page-header">
        <div>
            <h1 class="page-title">Service Requests</h1>
            <p class="page-subtitle">Community service and maintenance requests</p>
        </div>
        <a href="{{ route('services.create') }}" wire:navigate class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Request
        </a>
    </div>

    <div class="card" style="margin-bottom:16px;">
        <div class="card-body" style="display:flex;gap:12px;flex-wrap:wrap;">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search request number or subject..." class="form-input">
            <select wire:model.live="filterStatus" class="form-select">
                <option value="">All Status</option>
                <option value="open">Open</option>
                <option value="in_progress">In Progress</option>
                <option value="resolved">Resolved</option>
                <option value="closed">Closed</option>
            </select>
            <select wire:model.live="filterPriority" class="form-select">
                <option value="">All Priority</option>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
            </select>
        </div>
    </div>

    <div class="card" style="overflow:hidden;">
        <table class="table">
            <thead>
                <tr>
                    <th>Request No.</th>
                    <th>Subject</th>
                    <th>Category</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Assigned</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->requests() as $req)
                <tr>
                    <td><span class="font-mono" style="font-size:0.75rem;font-weight:700;color:var(--blue-600);">{{ $req->request_number }}</span></td>
                    <td><a href="{{ route('services.show', $req) }}" wire:navigate style="font-size:0.8125rem;font-weight:600;color:var(--blue-600);text-decoration:none;">{{ $req->subject }}</a></td>
                    <td><span style="font-size:0.8125rem;color:var(--text-secondary);">{{ $req->getCategoryLabel() }}</span></td>
                    <td>
                        @if($req->priority === 'urgent')
                            <span class="badge badge-red">{{ ucfirst($req->priority) }}</span>
                        @elseif($req->priority === 'high')
                            <span class="badge badge-orange">{{ ucfirst($req->priority) }}</span>
                        @elseif($req->priority === 'medium')
                            <span class="badge badge-amber">{{ ucfirst($req->priority) }}</span>
                        @else
                            <span class="badge badge-gray">{{ ucfirst($req->priority) }}</span>
                        @endif
                    </td>
                    <td>
                        @if($req->status === 'open')
                            <span class="badge badge-blue">{{ $req->getStatusLabel() }}</span>
                        @elseif($req->status === 'in_progress')
                            <span class="badge badge-amber">{{ $req->getStatusLabel() }}</span>
                        @elseif($req->status === 'resolved')
                            <span class="badge badge-green">{{ $req->getStatusLabel() }}</span>
                        @else
                            <span class="badge badge-gray">{{ $req->getStatusLabel() }}</span>
                        @endif
                    </td>
                    <td><span style="font-size:0.8125rem;color:var(--text-muted);">{{ $req->assignee->name ?? 'Unassigned' }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);font-size:0.8125rem;">No service requests found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:12px 16px;">{{ $this->requests()->links() }}</div>
    </div>

</div>
</div>
