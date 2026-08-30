<?php

use Livewire\Volt\Component;
use App\Models\Resident;
use App\Models\DocumentIssued;
use App\Models\BlotterRecord;
use App\Models\HealthRecord;
use App\Models\ServiceRequest;
use App\Models\RevenueRecord;
use App\Models\Business;
use App\Models\Announcement;

new #[Layout("layouts.app")] class extends Component
{
    public string $reportType = 'overview';

    public function getTotalResidents(): int { return Resident::where('is_active', true)->count(); }
    public function getMaleCount(): int { return Resident::where('is_active', true)->where('sex', 'male')->count(); }
    public function getFemaleCount(): int { return Resident::where('is_active', true)->where('sex', 'female')->count(); }

    public function getPurokStats(): \Illuminate\Support\Collection
    {
        return Resident::where('is_active', true)->selectRaw('purok, COUNT(*) as count')->groupBy('purok')->orderBy('purok')->get();
    }

    public function getBlotterStats(): array
    {
        return [
            'total' => BlotterRecord::count(),
            'pending' => BlotterRecord::where('status', 'pending')->count(),
            'under_mediation' => BlotterRecord::where('status', 'under_mediation')->count(),
            'settled' => BlotterRecord::where('status', 'settled')->count(),
        ];
    }

    public function getSpecialNeedsStats(): array
    {
        return [
            'pwd' => Resident::where('is_active', true)->where('is_pwd', true)->count(),
            'senior' => Resident::where('is_active', true)->where('is_senior_citizen', true)->count(),
            'solo_parent' => Resident::where('is_active', true)->where('is_solo_parent', true)->count(),
            'voter' => Resident::where('is_active', true)->where('is_registered_voter', true)->count(),
        ];
    }
}; ?>

<div>
<div style="max-width:1200px;margin:0 auto;">

    <div style="margin-bottom:20px;">
        <h1 style="font-size:1.125rem;font-weight:800;color:var(--text-primary);margin:0;">Reports</h1>
        <p style="font-size:0.8125rem;color:var(--text-secondary);margin:2px 0 0;">Analytics and statistics for {{ \App\Models\BarangaySetting::first()->barangay_name ?? 'the barangay' }}</p>
    </div>

    {{-- Report Tabs --}}
    <div class="section-tabs" style="margin-bottom:20px;">
        @foreach(['overview' => 'Overview', 'demographics' => 'Demographics', 'blotter' => 'Blotter', 'revenue' => 'Revenue'] as $key => $label)
        <button wire:click="$set('reportType', '{{ $key }}')" class="section-tab {{ $reportType === $key ? 'active' : '' }}">{{ $label }}</button>
        @endforeach
    </div>

    @if($reportType === 'overview')
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;">
        <div class="card" style="padding:16px;display:flex;align-items:center;gap:12px;">
            <div style="width:40px;height:40px;border-radius:var(--radius);background:var(--blue-100);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--blue-600)" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            </div>
            <div>
                <div style="font-size:1.25rem;font-weight:800;color:var(--text-primary);">{{ number_format($this->getTotalResidents()) }}</div>
                <div style="font-size:0.6875rem;color:var(--text-muted);font-weight:600;">Residents</div>
            </div>
        </div>
        <div class="card" style="padding:16px;display:flex;align-items:center;gap:12px;">
            <div style="width:40px;height:40px;border-radius:var(--radius);background:var(--green-100);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--green-600)" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <div>
                <div style="font-size:1.25rem;font-weight:800;color:var(--text-primary);">{{ number_format(DocumentIssued::count()) }}</div>
                <div style="font-size:0.6875rem;color:var(--text-muted);font-weight:600;">Documents Issued</div>
            </div>
        </div>
        <div class="card" style="padding:16px;display:flex;align-items:center;gap:12px;">
            <div style="width:40px;height:40px;border-radius:var(--radius);background:var(--amber-100);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--amber-600)" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
            </div>
            <div>
                <div style="font-size:1.25rem;font-weight:800;color:var(--text-primary);">{{ number_format(BlotterRecord::count()) }}</div>
                <div style="font-size:0.6875rem;color:var(--text-muted);font-weight:600;">Blotter Records</div>
            </div>
        </div>
        <div class="card" style="padding:16px;display:flex;align-items:center;gap:12px;">
            <div style="width:40px;height:40px;border-radius:var(--radius);background:var(--green-100);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--green-600)" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            </div>
            <div>
                <div style="font-size:1.25rem;font-weight:800;color:var(--text-primary);">₱{{ number_format(RevenueRecord::sum('amount'), 0) }}</div>
                <div style="font-size:0.6875rem;color:var(--text-muted);font-weight:600;">Total Revenue</div>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><rect x="7" y="7" width="3" height="9"/><rect x="14" y="7" width="3" height="5"/></svg>
                Purok Distribution
            </div>
            @foreach($this->getPurokStats() as $p)
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                <span style="font-size:0.8125rem;font-weight:600;width:80px;color:var(--text-secondary);">{{ $p->purok ?? 'N/A' }}</span>
                <div style="flex:1;height:20px;background:var(--navy-100);border-radius:var(--radius-sm);overflow:hidden;">
                    <div style="height:100%;background:linear-gradient(135deg,var(--blue-500),var(--blue-600));border-radius:var(--radius-sm);transition:width 0.3s;width:{{ $this->getTotalResidents() > 0 ? ($p->count / $this->getTotalResidents() * 100) : 0 }}%;"></div>
                </div>
                <span style="font-size:0.8125rem;font-weight:700;color:var(--text-primary);width:24px;text-align:right;">{{ $p->count }}</span>
            </div>
            @endforeach
        </div>

        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6"/><path d="M23 11h-6"/></svg>
                Special Categories
            </div>
            @php $special = $this->getSpecialNeedsStats(); @endphp
            @foreach(['pwd' => 'PWD', 'senior' => 'Senior Citizens', 'solo_parent' => 'Solo Parents', 'voter' => 'Registered Voters'] as $key => $label)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border-light);">
                <span style="font-size:0.8125rem;color:var(--text-secondary);">{{ $label }}</span>
                <span style="font-size:0.875rem;font-weight:700;color:var(--text-primary);">{{ $special[$key] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    @elseif($reportType === 'demographics')
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
        <div class="section-card" style="text-align:center;">
            <div class="section-card-title" style="justify-content:center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4-4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Gender Breakdown
            </div>
            <div style="display:flex;justify-content:center;gap:32px;margin-top:16px;">
                <div>
                    <div style="font-size:2rem;font-weight:800;color:var(--blue-600);">{{ $this->getMaleCount() }}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted);font-weight:600;">Male</div>
                </div>
                <div>
                    <div style="font-size:2rem;font-weight:800;color:#ec4899;">{{ $this->getFemaleCount() }}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted);font-weight:600;">Female</div>
                </div>
            </div>
        </div>

        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6"/><path d="M23 11h-6"/></svg>
                Service Records
            </div>
            @foreach([
                'Health Records' => \App\Models\HealthRecord::count(),
                'Service Requests' => \App\Models\ServiceRequest::count(),
                'Businesses' => Business::count(),
                'Announcements' => Announcement::count(),
            ] as $label => $count)
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border-light);">
                <span style="font-size:0.8125rem;color:var(--text-secondary);">{{ $label }}</span>
                <span style="font-size:0.875rem;font-weight:700;">{{ $count }}</span>
            </div>
            @endforeach
        </div>

        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6"/><path d="M23 11h-6"/></svg>
                Special Categories
            </div>
            @php $special = $this->getSpecialNeedsStats(); @endphp
            @foreach(['pwd' => 'PWD', 'senior' => 'Senior Citizens', 'solo_parent' => 'Solo Parents', 'voter' => 'Registered Voters'] as $key => $label)
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border-light);">
                <span style="font-size:0.8125rem;color:var(--text-secondary);">{{ $label }}</span>
                <span style="font-size:0.875rem;font-weight:700;">{{ $special[$key] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    @elseif($reportType === 'blotter')
    @php $bs = $this->getBlotterStats(); @endphp
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;">
        <div class="card" style="padding:16px;text-align:center;">
            <div style="font-size:1.5rem;font-weight:800;color:var(--text-primary);">{{ $bs['total'] }}</div>
            <div style="font-size:0.75rem;color:var(--text-muted);font-weight:600;">Total</div>
        </div>
        <div class="card" style="padding:16px;text-align:center;">
            <div style="font-size:1.5rem;font-weight:800;color:var(--amber-600);">{{ $bs['pending'] }}</div>
            <div style="font-size:0.75rem;color:var(--text-muted);font-weight:600;">Pending</div>
        </div>
        <div class="card" style="padding:16px;text-align:center;">
            <div style="font-size:1.5rem;font-weight:800;color:var(--blue-600);">{{ $bs['under_mediation'] }}</div>
            <div style="font-size:0.75rem;color:var(--text-muted);font-weight:600;">Under Mediation</div>
        </div>
        <div class="card" style="padding:16px;text-align:center;">
            <div style="font-size:1.5rem;font-weight:800;color:var(--green-600);">{{ $bs['settled'] }}</div>
            <div style="font-size:0.75rem;color:var(--text-muted);font-weight:600;">Settled</div>
        </div>
    </div>

    @elseif($reportType === 'revenue')
    @php $total = RevenueRecord::sum('amount'); @endphp
    <div class="card" style="padding:24px;background:linear-gradient(135deg,#f0fdf4 0%,#dcfce7 100%);border-color:#bbf7d0;">
        <div style="font-size:0.75rem;font-weight:600;color:var(--green-700);text-transform:uppercase;letter-spacing:0.04em;">Total Revenue</div>
        <div style="font-size:2rem;font-weight:800;color:var(--green-700);margin-top:4px;">₱ {{ number_format($total, 2) }}</div>
    </div>
    @endif

</div>
</div>
