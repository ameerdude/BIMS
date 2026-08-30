<?php

use Livewire\Volt\Component;
use App\Models\MeetingMinute;
use Livewire\WithPagination;

new #[Layout("layouts.app")] class extends Component
{
    use WithPagination;

    public string $filterType = '';

    public function meetings()
    {
        return MeetingMinute::with('recorder')
            ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
            ->latest('meeting_date')
            ->paginate(20);
    }
}; ?>

<div>
<div style="max-width:1200px;margin:0 auto;">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <div>
            <h1 style="font-size:1.125rem;font-weight:800;color:var(--text-primary);margin:0;">Meeting Minutes</h1>
            <p style="font-size:0.8125rem;color:var(--text-secondary);margin:2px 0 0;">Barangay session records and resolutions</p>
        </div>
        <a href="{{ route('meetings.create') }}" wire:navigate class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Minutes
        </a>
    </div>

    <div class="card" style="padding:16px;margin-bottom:16px;">
        <div style="max-width:250px;">
            <select wire:model.live="filterType" class="form-select">
                <option value="">All Types</option>
                <option value="regular">Regular Session</option>
                <option value="special">Special Session</option>
                <option value="committee">Committee</option>
                <option value="emergency">Emergency</option>
            </select>
        </div>
    </div>

    <div class="card" style="overflow:hidden;">
        <table class="table">
            <thead>
                <tr>
                    <th>Meeting No.</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Venue</th>
                    <th>Recorded By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->meetings() as $m)
                <tr>
                    <td><span class="font-mono" style="font-size:0.75rem;font-weight:700;color:var(--blue-600);">{{ $m->meeting_number }}</span></td>
                    <td>
                        @if($m->type === 'emergency')
                            <span class="badge badge-red">{{ ucfirst($m->type) }}</span>
                        @else
                            <span class="badge badge-blue">{{ ucfirst($m->type) }}</span>
                        @endif
                    </td>
                    <td><span style="font-size:0.8125rem;color:var(--text-muted);">{{ $m->meeting_date->format('M d, Y') }}</span></td>
                    <td><span style="font-size:0.8125rem;color:var(--text-secondary);">{{ $m->venue ?? 'N/A' }}</span></td>
                    <td><span style="font-size:0.8125rem;color:var(--text-muted);">{{ $m->recorder->name ?? 'Staff' }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted);font-size:0.8125rem;">No meeting minutes found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:12px 16px;">{{ $this->meetings()->links() }}</div>
    </div>

</div>
</div>
