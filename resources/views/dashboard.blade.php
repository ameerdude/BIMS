<x-app-layout>
    <x-slot name="header"><h2 class="text-2xl font-extrabold text-gray-800">📊 Dashboard</h2></x-slot>
    <div class="max-w-7xl mx-auto">
        <!-- Welcome -->
        <div class="card p-6 mb-6">
            <h1 class="text-3xl font-extrabold text-gray-800 mb-1">Welcome back, {{ auth()->user()->name }}! 👋</h1>
            <p class="text-gray-500">Here's what's happening in <span class="font-bold text-indigo-600">{{ \App\Models\BarangaySetting::first()->barangay_name ?? 'your barangay' }}</span> today.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            @php
            $stats = [
                ['route'=>'residents.index','icon'=>'👥','count'=>\App\Models\Resident::where('is_active',true)->count(),'label'=>'Residents','gradient'=>'bg-gradient-to-br from-blue-500 to-indigo-600'],
                ['route'=>'documents.index','icon'=>'📄','count'=>\App\Models\DocumentIssued::count(),'label'=>'Documents','gradient'=>'bg-gradient-to-br from-emerald-400 to-green-600'],
                ['route'=>'ids.index','icon'=>'🪪','count'=>\App\Models\BarangayId::count(),'label'=>'IDs Issued','gradient'=>'bg-gradient-to-br from-orange-400 to-amber-600'],
                ['route'=>'health.index','icon'=>'🏥','count'=>\App\Models\HealthRecord::count(),'label'=>'Health Records','gradient'=>'bg-gradient-to-br from-cyan-400 to-blue-600'],
                ['route'=>'services.index','icon'=>'🔧','count'=>\App\Models\ServiceRequest::where('status','open')->count(),'label'=>'Open Requests','gradient'=>'bg-gradient-to-br from-orange-400 to-amber-600'],
                ['route'=>'revenue.index','icon'=>'💰','count'=>'₱'.number_format(\App\Models\RevenueRecord::sum('amount'),0),'label'=>'Revenue','gradient'=>'bg-gradient-to-br from-emerald-400 to-green-600'],
            ];
            @endphp
            @foreach($stats as $s)
            <a href="{{ route($s['route']) }}" wire:navigate class="stat-card group">
                <div class="w-12 h-12 rounded-2xl {{ $s['gradient'] }} flex items-center justify-center text-2xl text-white mx-auto mb-3 shadow-lg group-hover:scale-110 transition-transform">{{ $s['icon'] }}</div>
                <div class="text-2xl font-extrabold text-gray-800">{{ $s['count'] }}</div>
                <div class="text-xs font-semibold text-gray-500 mt-0.5">{{ $s['label'] }}</div>
            </a>
            @endforeach
        </div>

        <!-- Activity Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Announcements -->
            <div class="card p-5">
                <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">📢 Announcements</h3>
                @forelse(\App\Models\Announcement::where('is_active',true)->latest()->take(3)->get() as $ann)
                <div class="py-2.5 border-b border-gray-100 last:border-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="badge {{ $ann->type==='emergency'?'badge-danger':($ann->type==='event'?'badge-success':'badge-info') }}">{{ $ann->getTypeLabel() }}</span>
                        @if($ann->priority==='urgent')<span class="badge badge-danger">URGENT</span>@endif
                    </div>
                    <div class="text-sm font-semibold text-gray-700">{{ $ann->title }}</div>
                    <div class="text-[11px] text-gray-400">{{ $ann->publish_date->format('M d, Y') }}</div>
                </div>
                @empty<p class="text-sm text-gray-400 py-2">No announcements.</p>@endforelse
            </div>

            <!-- Blotter -->
            <div class="card p-5">
                <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">📋 Recent Blotters</h3>
                @forelse(\App\Models\BlotterRecord::latest()->take(3)->get() as $b)
                <div class="flex justify-between items-center py-2.5 border-b border-gray-100 last:border-0">
                    <div><div class="text-sm font-mono font-bold text-indigo-600">{{ $b->blotter_number }}</div><div class="text-xs text-gray-400 capitalize">{{ str_replace('_',' ',$b->incident_type) }}</div></div>
                    <span class="badge {{ $b->status==='pending'?'badge-warning':($b->status==='settled'?'badge-success':'badge-danger') }}">{{ $b->getStatusLabel() }}</span>
                </div>
                @empty<p class="text-sm text-gray-400 py-2">No blotters.</p>@endforelse
            </div>

            <!-- Service Requests -->
            <div class="card p-5">
                <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">🔧 Pending Requests</h3>
                @forelse(\App\Models\ServiceRequest::where('status','open')->latest()->take(3)->get() as $sr)
                <div class="py-2.5 border-b border-gray-100 last:border-0">
                    <div class="text-sm font-semibold text-gray-700">{{ $sr->subject }}</div>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="badge {{ $sr->priority==='urgent'?'badge-danger':($sr->priority==='high'?'badge-orange':'badge-gray') }}">{{ ucfirst($sr->priority) }}</span>
                        <span class="text-xs text-gray-400">{{ $sr->getCategoryLabel() }}</span>
                    </div>
                </div>
                @empty<p class="text-sm text-gray-400 py-2">No pending requests.</p>@endforelse
            </div>
        </div>
    </div>
</x-app-layout>
