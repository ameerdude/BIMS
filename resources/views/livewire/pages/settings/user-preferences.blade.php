<?php

use Livewire\Volt\Component;

new #[Layout("layouts.app")] class extends Component
{
    // Theme
    public string $theme = 'light';
    // Font
    public string $fontFamily = 'figtree';
    public string $fontSize = 'default';
    // Layout
    public bool $sidebarCompact = false;
    public bool $animations = true;
    // Table
    public string $tableDensity = 'comfortable';
    public int $rowsPerPage = 10;
    // Date
    public string $dateFormat = 'M d, Y';
    // Dashboard
    public bool $showWelcomeTips = true;

    public function mount(): void
    {
        $user = auth()->user();
        $prefs = $user->preferences ?? [];
        $defaults = \App\Models\User::DEFAULT_PREFERENCES;

        $this->theme = $prefs['theme'] ?? $defaults['theme'];
        $this->fontFamily = $prefs['font_family'] ?? $defaults['font_family'];
        $this->fontSize = $prefs['font_size'] ?? $defaults['font_size'];
        $this->sidebarCompact = $prefs['sidebar_compact'] ?? $defaults['sidebar_compact'];
        $this->animations = $prefs['animations'] ?? $defaults['animations'];
        $this->tableDensity = $prefs['table_density'] ?? $defaults['table_density'];
        $this->rowsPerPage = $prefs['rows_per_page'] ?? $defaults['rows_per_page'];
        $this->dateFormat = $prefs['date_format'] ?? $defaults['date_format'];
        $this->showWelcomeTips = $prefs['show_welcome_tips'] ?? $defaults['show_welcome_tips'];
    }

    public function save(): void
    {
        $data = [
            'theme' => $this->theme,
            'font_family' => $this->fontFamily,
            'font_size' => $this->fontSize,
            'sidebar_compact' => $this->sidebarCompact,
            'animations' => $this->animations,
            'table_density' => $this->tableDensity,
            'rows_per_page' => $this->rowsPerPage,
            'date_format' => $this->dateFormat,
            'show_welcome_tips' => $this->showWelcomeTips,
        ];

        auth()->user()->update(['preferences' => $data]);

        // Also dispatch event for live preview
        $this->dispatch('preferencesSaved', $data);

        session()->flash('success', 'Preferences saved successfully!');
    }

    public function resetDefaults(): void
    {
        $defaults = \App\Models\User::DEFAULT_PREFERENCES;
        $this->theme = $defaults['theme'];
        $this->fontFamily = $defaults['font_family'];
        $this->fontSize = $defaults['font_size'];
        $this->sidebarCompact = $defaults['sidebar_compact'];
        $this->animations = $defaults['animations'];
        $this->tableDensity = $defaults['table_density'];
        $this->rowsPerPage = $defaults['rows_per_page'];
        $this->dateFormat = $defaults['date_format'];
        $this->showWelcomeTips = $defaults['show_welcome_tips'];

        $this->save();
    }
}; ?>

<div>
<div style="max-width:700px;margin:0 auto;">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <div>
            <h1 style="font-size:1.25rem;font-weight:800;color:var(--text-primary);margin:0;">⚙️ Preferences</h1>
            <p style="font-size:0.8125rem;color:var(--text-secondary);margin:2px 0 0;">Customize your personal experience. Settings are saved per account.</p>
        </div>
        <button wire:click="resetDefaults" class="btn btn-outline" style="font-size:0.8125rem;">
            Reset to Defaults
        </button>
    </div>

    @if(session('success'))
    <div class="alert alert-success" style="margin-bottom:16px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- Appearance --}}
    <div class="section-card" style="margin-bottom:16px;">
        <div class="section-card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
            Appearance
        </div>

        <div class="form-group">
            <label class="form-label">Theme</label>
            <div style="display:flex;gap:8px;">
                @foreach(['light' => '☀️ Light', 'dark' => '🌙 Dark', 'auto' => '🖥️ System'] as $val => $label)
                <button type="button" wire:click="$set('theme','{{ $val }}')" wire:loading.attr="disabled"
                    style="flex:1;padding:12px 16px;border:2px solid {{ $theme === $val ? 'var(--blue-500)' : 'var(--border)' }};border-radius:var(--radius);background:{{ $theme === $val ? 'var(--blue-50)' : 'var(--bg-secondary)' }};cursor:pointer;font-size:0.875rem;font-weight:{{ $theme === $val ? '700' : '500' }};color:{{ $theme === $val ? 'var(--blue-700)' : 'var(--text-secondary)' }};transition:all 0.15s;">
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Font Family</label>
                <select wire:model="fontFamily" wire:change="save()" class="form-select">
                    <option value="figtree" style="font-family:Figtree;">Figtree (Default)</option>
                    <option value="inter" style="font-family:Inter;">Inter</option>
                    <option value="roboto" style="font-family:Roboto;">Roboto</option>
                    <option value="open-sans" style="font-family:'Open Sans';">Open Sans</option>
                    <option value="poppins" style="font-family:Poppins;">Poppins</option>
                    <option value="system" style="font-family:system-ui;">System Default</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Font Size</label>
                <select wire:model="fontSize" wire:change="save()" class="form-select">
                    <option value="small">Small (13px)</option>
                    <option value="default">Default (14px)</option>
                    <option value="large">Large (15px)</option>
                    <option value="xlarge">Extra Large (16px)</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Layout --}}
    <div class="section-card" style="margin-bottom:16px;">
        <div class="section-card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
            Layout & Behavior
        </div>

        <div style="display:flex;flex-direction:column;gap:12px;">
            <label style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:var(--gray-50);border-radius:var(--radius);cursor:pointer;">
                <div>
                    <div style="font-size:0.875rem;font-weight:600;color:var(--text-primary);">Compact Sidebar</div>
                    <div style="font-size:0.75rem;color:var(--text-muted);">Reduce sidebar width for more content space</div>
                </div>
                <input type="checkbox" wire:model="sidebarCompact" wire:change="save()" class="w-4 h-4" style="accent-color:var(--blue-600);">
            </label>

            <label style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:var(--gray-50);border-radius:var(--radius);cursor:pointer;">
                <div>
                    <div style="font-size:0.875rem;font-weight:600;color:var(--text-primary);">Animations</div>
                    <div style="font-size:0.75rem;color:var(--text-muted);">Enable transitions and hover effects</div>
                </div>
                <input type="checkbox" wire:model="animations" wire:change="save()" class="w-4 h-4" style="accent-color:var(--blue-600);">
            </label>

            <label style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:var(--gray-50);border-radius:var(--radius);cursor:pointer;">
                <div>
                    <div style="font-size:0.875rem;font-weight:600;color:var(--text-primary);">Show Welcome Tips</div>
                    <div style="font-size:0.75rem;color:var(--text-muted);">Display helpful tips on the dashboard</div>
                </div>
                <input type="checkbox" wire:model="showWelcomeTips" wire:change="save()" class="w-4 h-4" style="accent-color:var(--blue-600);">
            </label>
        </div>
    </div>

    {{-- Data Display --}}
    <div class="section-card" style="margin-bottom:16px;">
        <div class="section-card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            Data Display
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Date Format</label>
                <select wire:model="dateFormat" wire:change="save()" class="form-select">
                    <option value="M d, Y">Aug 29, 2026</option>
                    <option value="d/m/Y">29/08/2026</option>
                    <option value="m/d/Y">08/29/2026</option>
                    <option value="Y-m-d">2026-08-29</option>
                    <option value="d-M-Y">29-Aug-2026</option>
                    <option value="F d, Y">August 29, 2026</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Rows Per Page</label>
                <select wire:model="rowsPerPage" wire:change="save()" class="form-select">
                    <option value="5">5 rows</option>
                    <option value="10">10 rows</option>
                    <option value="15">15 rows</option>
                    <option value="25">25 rows</option>
                    <option value="50">50 rows</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Table Density</label>
            <div style="display:flex;gap:8px;">
                @foreach(['compact' => '📏 Compact', 'comfortable' => '📐 Comfortable', 'spacious' => '🖼️ Spacious'] as $val => $label)
                <button type="button" wire:click="$set('tableDensity','{{ $val }}')" wire:loading.attr="disabled"
                    style="flex:1;padding:10px 12px;border:2px solid {{ $tableDensity === $val ? 'var(--blue-500)' : 'var(--border)' }};border-radius:var(--radius);background:{{ $tableDensity === $val ? 'var(--blue-50)' : 'var(--bg-secondary)' }};cursor:pointer;font-size:0.8125rem;font-weight:{{ $tableDensity === $val ? '700' : '500' }};color:{{ $tableDensity === $val ? 'var(--blue-700)' : 'var(--text-secondary)' }};transition:all 0.15s;">
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Save Button --}}
    <div style="display:flex;justify-content:flex-end;padding:8px 0 32px;">
        <button wire:click="save" class="btn btn-primary btn-lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
            Save Preferences
        </button>
    </div>

</div>
</div>
