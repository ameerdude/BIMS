<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'BIMS'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600;700,800|inter:400,500,600;700|roboto:400,500;700|open+Sans:400,500;700|poppins:400,500;700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    @php
        $prefUser = auth()->check() ? auth()->user() : null;
        $prefTheme = $prefUser ? ($prefUser->pref('theme') ?? 'light') : 'light';
        $prefFont = $prefUser ? ($prefUser->pref('font_family') ?? 'figtree') : 'figtree';
        $prefSize = $prefUser ? ($prefUser->pref('font_size') ?? 'default') : 'default';

        $fontMap = [
            'figtree' => 'Figtree, sans-serif',
            'inter' => 'Inter, sans-serif',
            'roboto' => 'Roboto, sans-serif',
            'open-sans' => "'Open Sans', sans-serif",
            'poppins' => 'Poppins, sans-serif',
            'system' => 'system-ui, sans-serif',
        ];
        $sizeMap = [
            'small' => '13px',
            'default' => '14px',
            'large' => '15px',
            'xlarge' => '16px',
        ];
        $resolvedFont = $fontMap[$prefFont] ?? $fontMap['figtree'];
        $resolvedSize = $sizeMap[$prefSize] ?? $sizeMap['default'];
    @endphp
    <style>
        :root { --user-font: {{ $resolvedFont }}; --user-font-size: {{ $resolvedSize }}; }
        @if($prefTheme === 'dark')
        /* ══════ DARK MODE ══════ */
        [data-theme='dark'] {
            /* Core surfaces */
            --bg: #0f172a; --surface: #1e293b; --border: #334155; --border-light: #1e293b;
            /* Typography */
            --text-primary: #f1f5f9; --text-secondary: #94a3b8; --text-muted: #64748b; --text-inverse: #0f172a;
            /* Navy palette - darken */
            --navy-50: #1e293b; --navy-100: #1e293b; --navy-200: #334155; --navy-300: #475569;
            --navy-400: #64748b; --navy-500: #94a3b8; --navy-600: #cbd5e1; --navy-700: #e2e8f0;
            /* Semantic palette - darken light shades */
            --green-50: #052e16; --green-100: #14532d; --green-600: #4ade80;
            --red-50: #450a0a; --red-100: #7f1d1d; --red-600: #f87171; --red-700: #fca5a5;
            --blue-50: #172554; --blue-100: #1e3a5f; --blue-600: #60a5fa; --blue-700: #93c5fd;
            --amber-100: #78350f; --amber-600: #fbbf24;
            --purple-100: #3b0764; --purple-600: #c084fc;
            --cyan-50: #083344; --cyan-100: #155e75; --cyan-600: #22d3ee;
            --orange-100: #431407; --orange-600: #fb923c;
            color: #e2e8f0;
        }
        [data-theme='dark'] body { background: #0f172a; color: #e2e8f0; }
        [data-theme='dark'] .sidebar { background: #020617; }
        [data-theme='dark'] .nav-link { color: #cbd5e1; }
        [data-theme='dark'] .nav-link:hover { color: #fff; background: rgba(255,255,255,0.08); }
        [data-theme='dark'] .nav-link.active { color: #fff; }
        [data-theme='dark'] .nav-section-label { color: #64748b; }
        [data-theme='dark'] .sidebar-brand-name { color: #fff; }
        [data-theme='dark'] .sidebar-brand-name span { color: #60a5fa; }
        [data-theme='dark'] .sidebar-user-name { color: #f1f5f9; }
        [data-theme='dark'] .sidebar-user-role { color: #94a3b8; }
        [data-theme='dark'] .page-content { background: #0f172a; }
        [data-theme='dark'] .card { background: #1e293b; border-color: #334155; }
        [data-theme='dark'] .card-body { background: #1e293b; }
        [data-theme='dark'] .card-footer { background: #0f172a; border-color: #334155; }
        [data-theme='dark'] .card-header { background: #1e293b; border-color: #334155; }
        [data-theme='dark'] .section-card { background: #1e293b; border-color: #334155; }
        [data-theme='dark'] .form-input, [data-theme='dark'] .form-select, [data-theme='dark'] .form-textarea { background: #0f172a; border-color: #475569; color: #e2e8f0; }
        [data-theme='dark'] .form-input::placeholder { color: #64748b; }
        [data-theme='dark'] .form-input:focus, [data-theme='dark'] .form-select:focus, [data-theme='dark'] .form-textarea:focus { border-color: #60a5fa; box-shadow: 0 0 0 3px rgba(96,165,250,0.15); }
        /* Tables */
        [data-theme='dark'] .table thead th { background: #0f172a; border-color: #334155; color: #94a3b8; }
        [data-theme='dark'] .table tbody td { border-color: #1e293b; color: #e2e8f0; }
        [data-theme='dark'] .table tbody tr:hover { background: rgba(96,165,250,0.05); }
        /* Buttons */
        [data-theme='dark'] .btn-outline { background: #1e293b; border-color: #475569; color: #94a3b8; }
        [data-theme='dark'] .btn-outline:hover { background: #334155; border-color: #60a5fa; color: #60a5fa; }
        [data-theme='dark'] .btn-outline-danger { background: #1e293b; border-color: #7f1d1d; color: #f87171; }
        [data-theme='dark'] .btn-ghost:hover { background: #334155; color: #f1f5f9; }
        /* Badges */
        [data-theme='dark'] .badge-blue { background: #172554; color: #60a5fa; }
        [data-theme='dark'] .badge-green { background: #052e16; color: #4ade80; }
        [data-theme='dark'] .badge-amber { background: #78350f; color: #fbbf24; }
        [data-theme='dark'] .badge-red { background: #450a0a; color: #f87171; }
        [data-theme='dark'] .badge-purple { background: #3b0764; color: #c084fc; }
        [data-theme='dark'] .badge-cyan { background: #083344; color: #22d3ee; }
        [data-theme='dark'] .badge-orange { background: #431407; color: #fb923c; }
        [data-theme='dark'] .badge-gray { background: #334155; color: #94a3b8; }
        /* Alerts */
        [data-theme='dark'] .alert-success { background: #052e16; color: #4ade80; border-color: #14532d; }
        [data-theme='dark'] .alert-danger { background: #450a0a; color: #f87171; border-color: #7f1d1d; }
        /* Tabs */
        [data-theme='dark'] .section-tabs { background: #0f172a; }
        [data-theme='dark'] .section-tab { color: #94a3b8; }
        [data-theme='dark'] .section-tab:hover { color: #e2e8f0; background: #334155; }
        [data-theme='dark'] .section-tab.active { background: #1e293b; color: #60a5fa; }
        /* Upload zone */
        [data-theme='dark'] .upload-zone { background: #0f172a; border-color: #475569; color: #94a3b8; }
        /* Pagination */
        [data-theme='dark'] .pagination-btn { background: #1e293b; border-color: #334155; color: #94a3b8; }
        [data-theme='dark'] .pagination-btn:hover { background: #334155; border-color: #475569; }
        [data-theme='dark'] .pagination-btn.active { background: #2563eb; color: #fff; }
        /* Confirmation banner */
        [data-theme='dark'] .confirm-banner { background: #052e16; border-color: #14532d; color: #4ade80; }
        /* Info panel */
        [data-theme='dark'] .info-panel { background: #0f172a; }
        /* Detail table */
        [data-theme='dark'] .detail-table td:first-child { color: #64748b; }
        [data-theme='dark'] .detail-table td:last-child { color: #f1f5f9; }
        [data-theme='dark'] .detail-table tr { border-color: #1e293b; }
        /* Empty state */
        [data-theme='dark'] .empty-state { color: #64748b; }
        [data-theme='dark'] .empty-state-title { color: #94a3b8; }
        /* Table action buttons in dark mode */
        [data-theme='dark'] .btn-table-view { background: #172554; color: #60a5fa; border-color: #1e3a5f; }
        [data-theme='dark'] .btn-table-edit { background: #334155; color: #94a3b8; border-color: #475569; }
        [data-theme='dark'] .btn-table-edit:hover { background: #475569; color: #f1f5f9; }
        [data-theme='dark'] .btn-table-print { background: #052e16; color: #4ade80; border-color: #14532d; }
        [data-theme='dark'] .btn-table-danger { background: transparent; color: #f87171; }
        [data-theme='dark'] .btn-table-danger:hover { background: #450a0a; color: #fca5a5; }
        /* Dashboard stat cards */
        [data-theme='dark'] .section-card .stat-card, [data-theme='dark'] [style*="background:var(--navy-50)"] { background: #0f172a !important; }
        /* Links */
        [data-theme='dark'] a { color: inherit; }
        /* Scrollbar */
        [data-theme='dark'] ::-webkit-scrollbar { background: #1e293b; }
        [data-theme='dark'] ::-webkit-scrollbar-thumb { background: #475569; border-radius: 4px; }
        @endif
    </style>
</head>
<body x-data="{ sidebarOpen: false }" data-theme="{{ $prefTheme }}" style="font-family:var(--user-font);font-size:var(--user-font-size);">
<div class="app-layout">

    {{-- Desktop Sidebar --}}
    <aside class="sidebar no-print">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i data-lucide="landmark" style="width:18px;height:18px;"></i>
            </div>
            <div class="sidebar-brand-name">BIMS</div>
            <div style="font-size:0.5rem;color:rgba(255,255,255,0.4);line-height:1.2;margin-top:1px;">Barangay Info Mgmt System</div>
        </div>

        @php
        $user = auth()->check() ? auth()->user() : null;
        $privLevel = $user ? $user->privilege_level : 0;

        $navGroups = [];

        // Level 4 only - Admin group
        if ($privLevel >= 4) {
            $navGroups[] = ['label'=>'Admin', 'items'=>[
                ['route'=>'admin.index', 'label'=>'Admin Dashboard', 'icon'=>'layout-dashboard'],
                ['route'=>'officials.index', 'label'=>'Officials', 'icon'=>'shield-user'],
                ['route'=>'admin.users', 'label'=>'User Accounts', 'icon'=>'users'],
                ['route'=>'settings.index', 'label'=>'System Settings', 'icon'=>'settings'],
            ]];
        }

        $navGroups[] = ['label'=>'Main', 'items'=>[
            ['route'=>'dashboard', 'label'=>'Dashboard', 'icon'=>'house'],
        ]];

        $navGroups[] = ['label'=>'Records', 'items'=>[
            ['route'=>'residents.index', 'label'=>'Residents', 'icon'=>'users-round'],
            ['route'=>'households.index', 'label'=>'Households', 'icon'=>'building-2'],
        ]];

        $navGroups[] = ['label'=>'Services', 'items'=>[
            ['route'=>'documents.index', 'label'=>'Documents', 'icon'=>'file-text'],
            ['route'=>'ids.index', 'label'=>'Barangay IDs', 'icon'=>'badge-info'],
            ['route'=>'health.index', 'label'=>'Health Records', 'icon'=>'heart-pulse'],
            ['route'=>'services.index', 'label'=>'Service Requests', 'icon'=>'headphones'],
        ]];

        $opsItems = [
            ['route'=>'blotter.index', 'label'=>'Blotter', 'icon'=>'scroll-text'],
            ['route'=>'businesses.index', 'label'=>'Businesses', 'icon'=>'briefcase-business'],
        ];
        // Revenue/Treasury: Level 3+ only
        if ($privLevel >= 3) {
            $opsItems[] = ['route'=>'revenue.index', 'label'=>'Revenue / Treasury', 'icon'=>'banknote'];
        }
        $navGroups[] = ['label'=>'Operations', 'items'=>$opsItems];

        $govItems = [
            ['route'=>'announcements.index', 'label'=>'Announcements', 'icon'=>'megaphone'],
            ['route'=>'meetings.index', 'label'=>'Meeting Minutes', 'icon'=>'clipboard-list'],
        ];
        // Reports: Level 3+
        if ($privLevel >= 3) {
            $govItems[] = ['route'=>'reports.index', 'label'=>'Reports', 'icon'=>'bar-chart-3'];
        }
        $navGroups[] = ['label'=>'Governance', 'items'=>$govItems];

        // Compute which group is active for first-visit default
        $activeGroupIndex = -1;
        foreach($navGroups as $idx => $group) {
            foreach($group['items'] as $item) {
                if(request()->routeIs($item['route'])) { $activeGroupIndex = $idx; break; }
            }
            if($activeGroupIndex >= 0) break;
        }
        @endphp

        <nav class="sidebar-nav" x-data="{
            collapsed: {},
            init() {
                const saved = JSON.parse(localStorage.getItem('sidebar_collapsed') || 'null');
                if (saved && Object.keys(saved).length > 0) {
                    this.collapsed = saved;
                } else {
                    for (let i = 0; i < 8; i++) {
                        this.collapsed['nav_' + i] = (i !== {{ $activeGroupIndex }});
                    }
                }
                // Always ensure the active group is expanded
                this.collapsed['nav_{{ $activeGroupIndex }}'] = false;
                localStorage.setItem('sidebar_collapsed', JSON.stringify(this.collapsed));
            },
            toggle(key) {
                this.collapsed[key] = !this.collapsed[key];
                localStorage.setItem('sidebar_collapsed', JSON.stringify(this.collapsed));
            },
            isCollapsed(key) { return this.collapsed[key] || false; }
        }">
            @foreach($navGroups as $idx => $group)
            @php $groupKey = 'nav_' . $idx; @endphp
            <div class="nav-section" style="cursor:pointer;" @click="toggle('{{ $groupKey }}')">
                <div class="nav-section-label" style="display:flex;align-items:center;justify-content:space-between;">
                    <span>{{ $group['label'] }}</span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transition:transform 0.15s;" :style="isCollapsed('{{ $groupKey }}') ? '' : 'transform:rotate(90deg)'"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </div>
            <div x-show="!isCollapsed('{{ $groupKey }}')" x-transition.duration.150ms>
            @foreach($group['items'] as $item)
            <a href="{{ route($item['route']) }}" wire:navigate
               class="nav-link {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                <i data-lucide="{{ $item['icon'] }}" style="width:18px;height:18px;"></i>
                <span>{{ $item['label'] }}</span>
            </a>
            @endforeach
            </div>
            @endforeach

            {{-- Scan ID --}}
            <a href="{{ route('ids.scan') }}" wire:navigate class="nav-link {{ request()->routeIs('ids.scan') ? 'active' : '' }}">
                <i data-lucide="scan-line" style="width:18px;height:18px;"></i>
                <span>Scan ID</span>
            </a>

            {{-- Preferences --}}
            <a href="{{ route('preferences.index') }}" wire:navigate class="nav-link {{ request()->routeIs('preferences.*') ? 'active' : '' }}">
                <i data-lucide="sliders-horizontal" style="width:18px;height:18px;"></i>
                <span>Preferences</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                <div style="flex:1;min-width:0;">
                    <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                    <div class="sidebar-user-role">{{ $user->privilege_level_label }} · Lv.{{ $user->privilege_level }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" title="Sign Out" style="background:none;border:none;cursor:pointer;padding:4px;border-radius:var(--radius-sm);transition:background 0.12s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='none'">
                        <i data-lucide="log-out" style="width:16px;height:16px;color:var(--navy-400);"></i>
                    </button>
                </form>
            </div>        </div>
        <div style="text-align:center;padding:8px;font-size:0.5625rem;color:rgba(255,255,255,0.3);line-height:1.4;">
            BIMS v1.0 · by <span style="color:rgba(255,255,255,0.5);">ameerdude</span>
        </div>
    </aside>


    {{-- Main --}}
    <div class="app-main">

        {{-- Mobile Topbar --}}
        <header class="mobile-topbar no-print">
            <a href="{{ route('dashboard') }}" wire:navigate class="mobile-logo">
                <span>BIMS</span>
            </a>
            <button @click="sidebarOpen = !sidebarOpen" class="btn btn-ghost btn-icon">
                <i data-lucide="menu" style="width:20px;height:20px;"></i>
            </button>
        </header>

        {{-- Mobile sidebar overlay --}}
        <template x-if="sidebarOpen">
            <div class="sidebar-overlay lg:hidden" @click="sidebarOpen = false"></div>
        </template>

        {{-- Page Content --}}
        <main class="page-content">{{ $slot }}</main>
    </div>
</div>

{{-- Loading bar --}}
<div wire:loading class="loading-bar"></div>
<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(() => {});
}
document.addEventListener('DOMContentLoaded', () => { lucide.createIcons(); });
window.addEventListener('livewire:navigated', () => { lucide.createIcons(); });
window.addEventListener('livewire:loaded', () => { lucide.createIcons(); });
</script>
</body>
</html>
