<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Computed;

new #[Layout("layouts.app")] class extends Component
{
    #[Computed]
    public function stats(): array
    {
        $counts = \Illuminate\Support\Facades\DB::selectOne('
            SELECT 
                (SELECT count(*) FROM residents WHERE is_active = true) AS residents,
                (SELECT count(*) FROM documents_issued) AS documents,
                (SELECT count(*) FROM barangay_ids) AS ids,
                (SELECT count(*) FROM health_records) AS health,
                (SELECT count(*) FROM service_requests WHERE status = ?) AS open_requests,
                (SELECT COALESCE(SUM(amount), 0) FROM revenue_records) AS revenue
        ', ['open']);

        return [
            [
                'route' => 'residents.index',
                'icon' => '<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>',
                'count' => $counts->residents,
                'label' => 'Total Residents',
                'color' => '#2563eb',
                'bg' => '#eff6ff',
            ],
            [
                'route' => 'documents.index',
                'icon' => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
                'count' => $counts->documents,
                'label' => 'Documents Issued',
                'color' => '#16a34a',
                'bg' => '#f0fdf4',
            ],
            [
                'route' => 'ids.index',
                'icon' => '<rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/>',
                'count' => $counts->ids,
                'label' => 'IDs Issued',
                'color' => '#ea580c',
                'bg' => '#fff7ed',
            ],
            [
                'route' => 'health.index',
                'icon' => '<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>',
                'count' => $counts->health,
                'label' => 'Health Records',
                'color' => '#0891b2',
                'bg' => '#ecfeff',
            ],
            [
                'route' => 'services.index',
                'icon' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
                'count' => $counts->open_requests,
                'label' => 'Open Requests',
                'color' => '#d97706',
                'bg' => '#fffbeb',
            ],
            [
                'route' => 'revenue.index',
                'icon' => '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>',
                'count' => '₱' . number_format($counts->revenue, 0),
                'label' => 'Total Revenue',
                'color' => '#16a34a',
                'bg' => '#f0fdf4',
            ],
        ];
    }

    #[Computed]
    public function barangayName(): string
    {
        return \App\Models\BarangaySetting::first()->barangay_name ?? 'your barangay';
    }

    #[Computed]
    public function announcements()
    {
        return \App\Models\Announcement::where('is_active', true)->latest()->take(4)->get();
    }

    #[Computed]
    public function blotters()
    {
        return \App\Models\BlotterRecord::with('parties')->latest()->take(5)->get();
    }

    #[Computed]
    public function requests()
    {
        return \App\Models\ServiceRequest::where('status', 'open')->latest()->take(5)->get();
    }

    #[Computed]
    public function recentResidents()
    {
        return \App\Models\Resident::where('is_active', true)->latest()->take(5)->get();
    }
}; ?>

<div>
    {{-- Welcome Banner --}}
    <div style="background:linear-gradient(135deg,#1e3a5f 0%,#2563eb 100%);border-radius:16px;padding:28px 32px;margin-bottom:24px;color:#fff;">
        <h1 style="font-size:1.375rem;font-weight:800;margin:0 0 4px;">Welcome back, {{ auth()->user()->name }}</h1>
        <p style="font-size:0.875rem;color:rgba(255,255,255,0.7);margin:0;">Here's what's happening in <strong style="color:#fff;">{{ $this->barangayName() }}</strong> today.</p>
    </div>

    {{-- Stats Row --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">
        @foreach($this->stats() as $i => $s)
        @if($i === 3)
        </div><div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">
        @endif
        <a href="{{ route($s['route']) }}" wire:navigate style="display:flex;align-items:center;gap:14px;background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:18px 20px;text-decoration:none;color:inherit;transition:all 0.2s var(--ease);" onmouseover="this.style.borderColor='{{ $s['color'] }}';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.06)';this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='var(--border)';this.style.boxShadow='none';this.style.transform='none'">
            <div style="width:48px;height:48px;border-radius:12px;background:{{ $s['bg'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="{{ $s['color'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $s['icon'] !!}</svg>
            </div>
            <div>
                <div style="font-size:1.375rem;font-weight:800;color:var(--text-primary);line-height:1.1;">{{ $s['count'] }}</div>
                <div style="font-size:0.75rem;font-weight:500;color:var(--text-muted);margin-top:2px;">{{ $s['label'] }}</div>
            </div>
        </a>
        @endforeach
    </div>

    {{-- Bottom Section: 3 columns --}}
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">

        {{-- Announcements --}}
        <div class="card">
            <div class="card-header">
                <span class="card-header-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--blue-600)" stroke-width="2" style="display:inline;vertical-align:-2px;margin-right:4px;"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                    Announcements
                </span>
                <a href="{{ route('announcements.index') }}" wire:navigate style="font-size:0.75rem;font-weight:600;color:var(--blue-600);text-decoration:none;">View all</a>
            </div>
            <div style="padding:0;">
                @forelse($this->announcements() as $ann)
                <div style="padding:14px 20px;border-bottom:1px solid var(--border-light);">
                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">
                        <span class="badge {{ $ann->type==='emergency'?'badge-red':($ann->type==='event'?'badge-green':($ann->type==='health'?'badge-cyan':'badge-gray')) }}" style="font-size:0.625rem;">{{ $ann->getTypeLabel() }}</span>
                        @if($ann->priority==='urgent')<span class="badge badge-red" style="font-size:0.625rem;font-weight:700;">URGENT</span>@endif
                    </div>
                    <div style="font-size:0.8125rem;font-weight:600;color:var(--text-primary);line-height:1.3;">{{ $ann->title }}</div>
                    <div style="font-size:0.6875rem;color:var(--text-muted);margin-top:4px;">{{ $ann->publish_date->format('M d, Y') }}</div>
                </div>
                @empty
                <div style="padding:32px 20px;text-align:center;color:var(--text-muted);font-size:0.8125rem;">No announcements.</div>
                @endforelse
            </div>
        </div>

        {{-- Recent Blotters --}}
        <div class="card">
            <div class="card-header">
                <span class="card-header-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--amber-600)" stroke-width="2" style="display:inline;vertical-align:-2px;margin-right:4px;"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                    Recent Blotters
                </span>
                <a href="{{ route('blotter.index') }}" wire:navigate style="font-size:0.75rem;font-weight:600;color:var(--blue-600);text-decoration:none;">View all</a>
            </div>
            <div style="padding:0;">
                @forelse($this->blotters() as $b)
                <a href="{{ route('blotter.show', $b) }}" wire:navigate style="display:flex;justify-content:space-between;align-items:center;padding:14px 20px;border-bottom:1px solid var(--border-light);text-decoration:none;color:inherit;transition:background 0.1s;" onmouseover="this.style.background='var(--navy-50)'" onmouseout="this.style.background='transparent'">
                    <div>
                        <div style="font-size:0.8125rem;font-weight:600;color:var(--blue-600);font-family:'JetBrains Mono',monospace;">{{ $b->blotter_number }}</div>
                        <div style="font-size:0.75rem;color:var(--text-muted);text-transform:capitalize;margin-top:2px;">{{ str_replace('_', ' ', $b->incident_type) }}</div>
                    </div>
                    <span class="badge {{ $b->status==='pending'?'badge-amber':($b->status==='settled'?'badge-green':'badge-red') }}">{{ $b->getStatusLabel() }}</span>
                </a>
                @empty
                <div style="padding:32px 20px;text-align:center;color:var(--text-muted);font-size:0.8125rem;">No blotters.</div>
                @endforelse
            </div>
        </div>

        {{-- Recent Residents --}}
        <div class="card">
            <div class="card-header">
                <span class="card-header-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--blue-600)" stroke-width="2" style="display:inline;vertical-align:-2px;margin-right:4px;"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    Recent Residents
                </span>
                <a href="{{ route('residents.index') }}" wire:navigate style="font-size:0.75rem;font-weight:600;color:var(--blue-600);text-decoration:none;">View all</a>
            </div>
            <div style="padding:0;">
                @forelse($this->recentResidents() as $r)
                <a href="{{ route('residents.show', $r) }}" wire:navigate style="display:flex;align-items:center;gap:12px;padding:12px 20px;border-bottom:1px solid var(--border-light);text-decoration:none;color:inherit;transition:background 0.1s;" onmouseover="this.style.background='var(--navy-50)'" onmouseout="this.style.background='transparent'">
                    <div style="width:36px;height:36px;border-radius:50%;background:var(--blue-600);display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:#fff;flex-shrink:0;">{{ substr($r->first_name,0,1) }}{{ substr($r->last_name,0,1) }}</div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:0.8125rem;font-weight:600;color:var(--text-primary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $r->fullName() }}</div>
                        <div style="font-size:0.6875rem;color:var(--text-muted);">{{ $r->purok ?? 'N/A' }} · {{ ucfirst($r->sex) }} · {{ $r->getAge() }} yrs</div>
                    </div>
                    @if($r->getSectorBadges())
                    <span class="badge badge-blue" style="font-size:0.5625rem;flex-shrink:0;">{{ $r->getSectorBadges()[0] ?? '' }}</span>
                    @endif
                </a>
                @empty
                <div style="padding:32px 20px;text-align:center;color:var(--text-muted);font-size:0.8125rem;">No residents.</div>
                @endforelse
            </div>
        </div>

    </div>
</div>
