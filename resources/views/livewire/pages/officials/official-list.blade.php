<?php

use Livewire\Volt\Component;
use App\Models\Official;
use App\Models\User;

new #[Layout("layouts.app")] class extends Component
{
    public $officials;
    public string $name = '';
    public string $position = '';
    public string $position_category = 'elected';
    public string $term_start = '';
    public string $term_end = '';

    public array $positionOptions = [
        'elected' => [
            'Punong Barangay (Barangay Captain)',
            'Kagawad (Councilor)',
            'SK Chairperson',
            'SK Kagawad',
        ],
        'appointed' => [
            'Barangay Secretary',
            'Barangay Treasurer',
            'Barangay Tanod Chief',
            'Barangay Tanod',
            'Lupong Tagapamayapa Member',
            'Barangay Health Worker',
            'Barangay Day Care Worker',
        ],
        'staff' => [
            'Utility Worker (Janitor)',
            'Messenger',
            'Driver',
            'Security Guard',
            'Clerk',
        ],
    ];

    public function mount(): void
    {
        $this->officials = Official::current()->orderBy('position_category')->orderBy('position')->get();
    }

    public function addOfficial(): void
    {
        $this->validate([
            'name' => 'required',
            'position' => 'required',
            'position_category' => 'required|in:elected,appointed,staff',
        ]);

        Official::create([
            'name' => $this->name,
            'position' => $this->position,
            'position_category' => $this->position_category,
            'term_start' => $this->term_start ?: null,
            'term_end' => $this->term_end ?: null,
        ]);

        $this->reset(['name', 'position', 'position_category', 'term_start', 'term_end']);
        $this->position_category = 'elected';
        $this->officials = Official::current()->orderBy('position_category')->orderBy('position')->get();
    }

    public function removeOfficial(Official $official): void
    {
        $official->update(['is_current' => false]);
        $this->officials = Official::current()->orderBy('position_category')->orderBy('position')->get();
    }
}; ?>

<div>
<div style="max-width:1200px;margin:0 auto;">

    <div class="page-header">
        <div>
            <h1 class="page-title">Officials & Staff</h1>
            <p class="page-subtitle">Manage barangay officials and system accounts</p>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:380px 1fr;gap:20px;">

    {{-- Left Column: Add Official Form --}}
    <div>
        <div class="card" style="padding:20px;">
            <h3 style="font-size:0.9375rem;font-weight:700;color:var(--text-primary);margin-bottom:16px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:-2px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Official
            </h3>
            <form wire:submit="addOfficial">
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div>
                        <label class="form-label">Full Name *</label>
                        <input type="text" wire:model="name" class="form-input" placeholder="e.g. Juan Dela Cruz">
                    </div>
                    <div>
                        <label class="form-label">Category *</label>
                        <select wire:model="position_category" class="form-select">
                            <option value="elected">🗳️ Elected Officials</option>
                            <option value="appointed">📋 Appointed Officials</option>
                            <option value="staff">🔧 Staff / Workers</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Position *</label>
                        <select wire:model="position" class="form-select">
                            <option value="">Select position</option>
                            @foreach($this->positionOptions[$position_category] ?? [] as $pos)
                                <option value="{{ $pos }}">{{ $pos }}</option>
                            @endforeach
                            <option value="__custom">Other (type below)</option>
                        </select>
                    </div>
                    @if($position === '__custom')
                    <div>
                        <label class="form-label">Custom Position *</label>
                        <input type="text" wire:model.live="position" class="form-input" placeholder="Type custom position">
                    </div>
                    @endif
                    <div>
                        <label class="form-label">Term Start</label>
                        <input type="date" wire:model="term_start" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Term End</label>
                        <input type="date" wire:model="term_end" class="form-input">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Add Official
                    </button>
                </div>
            </form>
        </div>

        {{-- Create Account (Admin Only) --}}

    </div>

    {{-- Right Column --}}
    <div>

        {{-- Current Officials --}}
        <div class="card" style="padding:20px;margin-bottom:16px;">
            <h3 style="font-size:0.9375rem;font-weight:700;color:var(--text-primary);margin-bottom:16px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:-2px;"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                Current Officials ({{ $officials->count() }})
            </h3>
            @php
                $grouped = $officials->groupBy(fn($o) => $o->position_category ?? 'elected');
                $categoryLabels = ['elected' => ['label' => '🗳️ Elected Officials', 'color' => 'var(--blue-600)'], 'appointed' => ['label' => '📋 Appointed Officials', 'color' => 'var(--amber-600)'], 'staff' => ['label' => '🔧 Staff / Workers', 'color' => 'var(--navy-500)']];
            @endphp
            @forelse($grouped as $cat => $catOfficials)
            <div style="margin-bottom:16px;">
                <div style="font-size:0.75rem;font-weight:700;color:{{ $categoryLabels[$cat]['color'] ?? 'var(--text-muted)' }};text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px;padding-bottom:4px;border-bottom:2px solid {{ $categoryLabels[$cat]['color'] ?? 'var(--border)' }};">
                    {{ $categoryLabels[$cat]['label'] ?? ucfirst($cat) }}
                </div>
                @foreach($catOfficials as $o)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border-light);">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:36px;height:36px;border-radius:50%;background:{{ $categoryLabels[$cat]['color'] ?? 'var(--navy-100)' }}15;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.8125rem;color:{{ $categoryLabels[$cat]['color'] ?? 'var(--navy-600)' }};">{{ substr($o->name, 0, 1) }}</div>
                        <div>
                            <div style="font-size:0.875rem;font-weight:600;color:var(--text-primary);">{{ $o->name }}</div>
                            <div style="font-size:0.75rem;color:var(--text-muted);">{{ $o->position }}@if($o->term_start) · {{ $o->term_start->format('Y') }}-{{ $o->term_end ? $o->term_end->format('Y') : 'present' }} @endif</div>
                        </div>
                    </div>
                    @if(auth()->user()->isAdmin())
                    <button wire:click="removeOfficial({{ $o->id }})" onclick="return confirm('Remove this official?')" class="btn btn-sm btn-ghost" style="color:var(--red-600);font-size:0.75rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Remove
                    </button>
                    @endif
                </div>
                @endforeach
            </div>
            @empty
            <div style="padding:20px 0;text-align:center;color:var(--text-muted);font-size:0.8125rem;">No officials recorded.</div>
            @endforelse
        </div>



    </div>
    </div>
</div>
</div>
