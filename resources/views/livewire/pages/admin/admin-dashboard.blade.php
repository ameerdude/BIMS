<?php

use Livewire\Volt\Component;
use App\Models\Resident;
use App\Models\DocumentIssued;
use App\Models\BarangayId;
use App\Models\BlotterRecord;
use App\Models\Business;
use App\Models\RevenueRecord;
use App\Models\HealthRecord;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\Official;
use App\Models\Household;

new #[Layout("layouts.app")] class extends Component
{
    public function getStats(): array
    {
        $now = now();
        return [
            'total_residents' => Resident::count(),
            'active_residents' => Resident::where('is_active', true)->count(),
            'senior_citizens' => Resident::where('is_senior_citizen', true)->count(),
            'pwd' => Resident::where('is_pwd', true)->count(),
            'voters' => Resident::where('is_registered_voter', true)->count(),
            'households' => Household::count(),
            'documents_issued' => DocumentIssued::count(),
            'docs_this_month' => DocumentIssued::whereMonth('issued_at', $now->month)->whereYear('issued_at', $now->year)->count(),
            'ids_issued' => BarangayId::count(),
            'ids_active' => BarangayId::where('status', 'active')->count(),
            'blotter_total' => BlotterRecord::count(),
            'blotter_pending' => BlotterRecord::where('status', 'pending')->count(),
            'businesses' => Business::count(),
            'businesses_active' => Business::where('is_active', true)->count(),
            'revenue_total' => RevenueRecord::sum('amount'),
            'revenue_this_month' => RevenueRecord::whereMonth('payment_date', $now->month)->whereYear('payment_date', $now->year)->sum('amount'),
            'health_records' => HealthRecord::count(),
            'service_requests' => ServiceRequest::count(),
            'service_open' => ServiceRequest::where('status', 'open')->count(),
            'staff_users' => User::where('is_active', true)->count(),
            'officials' => Official::current()->count(),
        ];
    }

    public function getRecentActivity(): array
    {
        $residents = Resident::latest()->take(5)->get()->map(fn($r) => ['type' => 'resident', 'text' => "New resident: {$r->fullName()}", 'time' => $r->created_at->diffForHumans()]);
        $docs = DocumentIssued::latest()->take(5)->get()->map(fn($d) => ['type' => 'document', 'text' => "{$d->getDocumentTypeLabel()} issued to {$d->resident->fullName()}", 'time' => $d->created_at->diffForHumans()]);
        $blotters = BlotterRecord::latest()->take(5)->get()->map(fn($b) => ['type' => 'blotter', 'text' => "Blotter {$b->blotter_number}: " . str_replace('_', ' ', $b->incident_type), 'time' => $b->created_at->diffForHumans()]);

        return $residents->concat($docs)->concat($blotters)->sortByDesc('time')->take(10)->values()->toArray();
    }
}; ?>

<div>
<div style="max-width:1200px;margin:0 auto;">

    <div style="margin-bottom:20px;">
        <h1 style="font-size:1.25rem;font-weight:800;color:var(--text-primary);margin:0;">🛡️ Admin Dashboard</h1>
        <p style="font-size:0.8125rem;color:var(--text-secondary);margin:2px 0 0;">System overview and management. Admin only.</p>
    </div>

    @php $stats = $this->getStats(); @endphp

    {{-- Stats Grid --}}
    <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:20px;">
        <a href="{{ route('residents.index') }}" wire:navigate class="section-card" style="text-align:center;padding:16px 8px;text-decoration:none;cursor:pointer;transition:all 0.15s;">
            <div style="font-size:1.5rem;font-weight:800;color:var(--blue-600);">{{ number_format($stats['total_residents']) }}</div>
            <div style="font-size:0.6875rem;color:var(--text-muted);font-weight:600;">Total Residents</div>
        </a>
        <a href="{{ route('documents.index') }}" wire:navigate class="section-card" style="text-align:center;padding:16px 8px;text-decoration:none;cursor:pointer;transition:all 0.15s;">
            <div style="font-size:1.5rem;font-weight:800;color:var(--green-600);">{{ number_format($stats['documents_issued']) }}</div>
            <div style="font-size:0.6875rem;color:var(--text-muted);font-weight:600;">Documents Issued</div>
        </a>
        <a href="{{ route('ids.index') }}" wire:navigate class="section-card" style="text-align:center;padding:16px 8px;text-decoration:none;cursor:pointer;transition:all 0.15s;">
            <div style="font-size:1.5rem;font-weight:800;color:var(--purple-600);">{{ number_format($stats['ids_issued']) }}</div>
            <div style="font-size:0.6875rem;color:var(--text-muted);font-weight:600;">IDs Issued</div>
        </a>
        <a href="{{ route('revenue.index') }}" wire:navigate class="section-card" style="text-align:center;padding:16px 8px;text-decoration:none;cursor:pointer;transition:all 0.15s;">
            <div style="font-size:1.5rem;font-weight:800;color:var(--cyan-600);">₱{{ number_format($stats['revenue_total']) }}</div>
            <div style="font-size:0.6875rem;color:var(--text-muted);font-weight:600;">Total Revenue</div>
        </a>
        <a href="{{ route('blotter.index') }}" wire:navigate class="section-card" style="text-align:center;padding:16px 8px;text-decoration:none;cursor:pointer;transition:all 0.15s;">
            <div style="font-size:1.5rem;font-weight:800;color:var(--amber-600);">{{ $stats['blotter_pending'] }}</div>
            <div style="font-size:0.6875rem;color:var(--text-muted);font-weight:600;">Pending Blotters</div>
        </a>
        <a href="{{ route('services.index') }}" wire:navigate class="section-card" style="text-align:center;padding:16px 8px;text-decoration:none;cursor:pointer;transition:all 0.15s;">
            <div style="font-size:1.5rem;font-weight:800;color:var(--red-600);">{{ $stats['service_open'] }}</div>
            <div style="font-size:0.6875rem;color:var(--text-muted);font-weight:600;">Open Requests</div>
        </a>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">

        {{-- Demographics --}}
        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                Demographics Overview
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div style="padding:12px;background:var(--navy-50);border-radius:var(--radius);text-align:center;">
                    <div style="font-size:1.125rem;font-weight:800;color:var(--blue-600);">{{ number_format($stats['active_residents']) }}</div>
                    <div style="font-size:0.6875rem;color:var(--text-muted);">Active Residents</div>
                </div>
                <div style="padding:12px;background:var(--navy-50);border-radius:var(--radius);text-align:center;">
                    <div style="font-size:1.125rem;font-weight:800;color:var(--amber-600);">{{ number_format($stats['senior_citizens']) }}</div>
                    <div style="font-size:0.6875rem;color:var(--text-muted);">Senior Citizens</div>
                </div>
                <div style="padding:12px;background:var(--navy-50);border-radius:var(--radius);text-align:center;">
                    <div style="font-size:1.125rem;font-weight:800;color:var(--red-600);">{{ number_format($stats['pwd']) }}</div>
                    <div style="font-size:0.6875rem;color:var(--text-muted);">PWD</div>
                </div>
                <div style="padding:12px;background:var(--navy-50);border-radius:var(--radius);text-align:center;">
                    <div style="font-size:1.125rem;font-weight:800;color:var(--green-600);">{{ number_format($stats['voters']) }}</div>
                    <div style="font-size:0.6875rem;color:var(--text-muted);">Registered Voters</div>
                </div>
                <div style="padding:12px;background:var(--navy-50);border-radius:var(--radius);text-align:center;">
                    <div style="font-size:1.125rem;font-weight:800;color:var(--purple-600);">{{ number_format($stats['households']) }}</div>
                    <div style="font-size:0.6875rem;color:var(--text-muted);">Households</div>
                </div>
                <div style="padding:12px;background:var(--navy-50);border-radius:var(--radius);text-align:center;">
                    <div style="font-size:1.125rem;font-weight:800;color:var(--cyan-600);">{{ $stats['officials'] }}</div>
                    <div style="font-size:0.6875rem;color:var(--text-muted);">Officials</div>
                </div>
            </div>
        </div>

        {{-- Quick Summary --}}
        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                Operations Summary
            </div>
            <div style="display:flex;flex-direction:column;gap:10px;">
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 12px;background:var(--green-50);border-radius:var(--radius);border-left:3px solid var(--green-600);">
                    <span style="font-size:0.8125rem;font-weight:600;">Documents This Month</span>
                    <span style="font-size:0.9375rem;font-weight:800;color:var(--green-600);">{{ $stats['docs_this_month'] }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 12px;background:var(--blue-50);border-radius:var(--radius);border-left:3px solid var(--blue-600);">
                    <span style="font-size:0.8125rem;font-weight:600;">Active IDs</span>
                    <span style="font-size:0.9375rem;font-weight:800;color:var(--blue-600);">{{ $stats['ids_active'] }} / {{ $stats['ids_issued'] }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 12px;background:var(--cyan-50);border-radius:var(--radius);border-left:3px solid var(--cyan-600);">
                    <span style="font-size:0.8125rem;font-weight:600;">Revenue This Month</span>
                    <span style="font-size:0.9375rem;font-weight:800;color:var(--cyan-600);">₱{{ number_format($stats['revenue_this_month']) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 12px;background:var(--amber-50);border-radius:var(--radius);border-left:3px solid var(--amber-600);">
                    <span style="font-size:0.8125rem;font-weight:600;">Active Businesses</span>
                    <span style="font-size:0.9375rem;font-weight:800;color:var(--amber-600);">{{ $stats['businesses_active'] }} / {{ $stats['businesses'] }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 12px;background:var(--navy-50);border-radius:var(--radius);border-left:3px solid var(--navy-600);">
                    <span style="font-size:0.8125rem;font-weight:600;">Staff Accounts</span>
                    <span style="font-size:0.9375rem;font-weight:800;color:var(--navy-600);">{{ $stats['staff_users'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="section-card">
        <div class="section-card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Recent Activity
        </div>
        @php $activity = $this->getRecentActivity(); @endphp
        @forelse($activity as $a)
        <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--border-light);">
            <div style="width:8px;height:8px;border-radius:50%;flex-shrink:0;background:{{ match($a['type']) { 'resident' => 'var(--blue-600)', 'document' => 'var(--green-600)', 'blotter' => 'var(--amber-600)', default => 'var(--navy-400)' } }};"></div>
            <div style="flex:1;font-size:0.8125rem;color:var(--text-primary);">{{ $a['text'] }}</div>
            <div style="font-size:0.6875rem;color:var(--text-muted);white-space:nowrap;">{{ $a['time'] }}</div>
        </div>
        @empty
        <div style="text-align:center;padding:24px;color:var(--text-muted);font-size:0.8125rem;">No recent activity.</div>
        @endforelse
    </div>

    {{-- Quick Actions --}}
    <div class="section-card" style="margin-top:16px;">
        <div class="section-card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            Quick Actions
        </div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;">
            <a href="{{ route('residents.create') }}" wire:navigate class="btn btn-primary" style="justify-content:center;">+ Add Resident</a>
            <a href="{{ route('documents.issue') }}" wire:navigate class="btn btn-success" style="justify-content:center;">+ Issue Document</a>
            <a href="{{ route('ids.issue') }}" wire:navigate class="btn btn-primary" style="justify-content:center;background:linear-gradient(135deg,#9333ea,#7c3aed);border-color:#9333ea;" wire:navigate>+ Issue ID Card</a>
            <a href="{{ route('settings.index') }}" wire:navigate class="btn btn-outline" style="justify-content:center;">⚙️ System Settings</a>
        </div>
    </div>

</div>
</div>
