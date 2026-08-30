<?php

use Livewire\Volt\Component;
use App\Models\BarangayId;
use App\Services\BarcodeService;
use App\Services\QrCodeService;

new #[Layout("layouts.app")] class extends Component
{
    public string $search = '';
    public array $selectedIds = [];
    public string $sheetSize = 'a4';
    public bool $printFronts = true;
    public bool $printBacks = true;

    public function activeIds()
    {
        return BarangayId::with('resident')
            ->where('status', 'active')
            ->when($this->search, fn($q) => $q->where(function ($w) {
                $w->where('id_number', 'like', "%{$this->search}%")
                  ->orWhereHas('resident', fn($r) => $r->where(fn($s) => $s->where('first_name', 'like', "%{$this->search}%")
                    ->orWhere('last_name', 'like', "%{$this->search}%")));
            }))
            ->latest()
            ->get();
    }

    public function toggleSelectAll(): void
    {
        $allIds = $this->activeIds()->pluck('id')->toArray();
        if (count($this->selectedIds) === count($allIds)) {
            $this->selectedIds = [];
        } else {
            $this->selectedIds = $allIds;
        }
    }

    public function toggleId(int $id): void
    {
        if (in_array($id, $this->selectedIds)) {
            $this->selectedIds = array_values(array_diff($this->selectedIds, [$id]));
        } else {
            $this->selectedIds[] = $id;
        }
    }

    public function printSheet(): void
    {
        $this->dispatch('print-sheet');
    }

    public function getSelectedBarangayIds()
    {
        return BarangayId::with('resident')
            ->whereIn('id', $this->selectedIds)
            ->where('status', 'active')
            ->get();
    }

    /** Pairs per row: 2 (front+back side by side) */
    public function getPairsPerRow(): int { return 2; }

    /** Rows per sheet */
    public function getRowsPerSheet(): int
    {
        return match($this->sheetSize) {
            'a4' => 5,
            'letter' => 5,
            'a5' => 3,
            default => 5,
        };
    }

    /** Total IDs per sheet (pairs × rows × 2 sides) */
    public function getIdsPerSheet(): int
    {
        return $this->getPairsPerRow() * $this->getRowsPerSheet() * 2;
    }

    public function getBarangaySetting()
    {
        return \App\Models\BarangaySetting::first();
    }

    public function renderBarcode($number): string
    {
        return BarcodeService::svg($number, 20, true);
    }

    public function renderQr($residentId, $idNumber): string
    {
        $settings = $this->getBarangaySetting();
        $data = "BARANGAY: {$settings?->barangay_name}\nRESIDENT ID: {$residentId}\nID NUMBER: {$idNumber}";
        return QrCodeService::svg($data, 32);
    }
}; ?>

<div>
<div style="max-width:1200px;margin:0 auto;">

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <div>
            <h1 style="font-size:1.25rem;font-weight:800;color:var(--text-primary);margin:0;">Mass Print Barangay IDs</h1>
            <p style="font-size:0.8125rem;color:var(--text-secondary);margin:2px 0 0;">Select multiple IDs and print them on a single sheet. Front + Back side by side.</p>
        </div>
        <a href="{{ route('ids.index') }}" wire:navigate class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back to IDs
        </a>
    </div>

    <div style="display:grid;grid-template-columns:380px 1fr;gap:20px;">

        {{-- Left: ID Selection --}}
        <div>
            <div class="section-card">
                <div class="section-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    Select IDs to Print
                    @if(count($selectedIds) > 0)
                        <span class="badge badge-blue" style="margin-left:auto;">{{ count($selectedIds) }} selected</span>
                    @endif
                </div>

                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name or ID..." class="form-input" style="margin-bottom:12px;">

                <div style="margin-bottom:8px;">
                    <label style="display:flex;align-items:center;gap:6px;font-size:0.8125rem;font-weight:600;cursor:pointer;">
                        <input type="checkbox" wire:click="toggleSelectAll" {{ count($selectedIds) === $this->activeIds()->count() && $this->activeIds()->count() > 0 ? 'checked' : '' }}
                            style="width:16px;height:16px;">
                        Select All ({{ $this->activeIds()->count() }})
                    </label>
                </div>

                <div style="max-height:400px;overflow-y:auto;">
                    @foreach($this->activeIds() as $id)
                    <label style="display:flex;align-items:center;gap:8px;padding:8px;border-bottom:1px solid var(--border);cursor:pointer;{{ in_array($id->id, $selectedIds) ? 'background:var(--blue-50);' : '' }}">
                        <input type="checkbox" wire:click="toggleId({{ $id->id }})"
                            {{ in_array($id->id, $selectedIds) ? 'checked' : '' }}
                            style="width:16px;height:16px;flex-shrink:0;">
                        <div style="min-width:0;flex:1;">
                            <div style="font-size:0.8125rem;font-weight:600;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $id->resident?->fullName() ?? 'N/A' }}</div>
                            <div style="font-size:0.6875rem;color:var(--text-muted);font-family:monospace;">{{ $id->id_number }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Sheet Config --}}
            <div class="section-card" style="margin-top:12px;">
                <div class="section-card-title">📄 Sheet Settings</div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    @foreach(['a4'=>'A4 (210×297mm)','letter'=>'Letter (216×279mm)','a5'=>'A5 (148×210mm)'] as $size => $label)
                    <button type="button" wire:click="$set('sheetSize','{{ $size }}')" style="padding:6px 14px;border-radius:6px;font-size:0.8125rem;font-weight:600;border:2px solid {{ $sheetSize===$size ? 'var(--blue-500)' : 'var(--border)' }};background:{{ $sheetSize===$size ? 'var(--blue-50)' : 'white' }};color:{{ $sheetSize===$size ? 'var(--blue-700)' : 'var(--text-secondary)' }};cursor:pointer;">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>

                @if(count($selectedIds) > 0)
                <div style="margin-top:12px;padding:10px 14px;border-radius:8px;background:var(--blue-50);border:1px solid var(--blue-100);font-size:0.8125rem;color:var(--blue-700);">
                    <strong>{{ count($selectedIds) }} IDs</strong> selected →
                    @php $sheets = ceil(count($selectedIds) / $this->getIdsPerSheet()); @endphp
                    <strong>{{ $sheets }}</strong> {{ str('sheet')->plural($sheets) }} needed
                    <span style="color:var(--blue-500);font-size:0.75rem;">({{ $this->getIdsPerSheet() }} IDs per {{ strtoupper($sheetSize) }} sheet)</span>
                </div>
                @endif

                <div style="margin-top:12px;display:flex;justify-content:flex-end;">
                    <button type="button" wire:click="printSheet" {{ count($selectedIds) === 0 ? 'disabled' : '' }}
                        class="btn btn-primary btn-lg" {{ count($selectedIds) === 0 ? 'style="opacity:0.5;cursor:not-allowed;"' : '' }}>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                        Print Selected ({{ count($selectedIds) }})
                    </button>
                </div>
            </div>
        </div>

        {{-- Right: Sheet Preview --}}
        <div>
            @if(count($selectedIds) > 0)
                @php
                    $ids = $this->getSelectedBarangayIds();
                    $perPage = $this->getIdsPerSheet();
                    $pages = $ids->chunk($perPage);
                @endphp

                @foreach($pages as $pageIdx => $pageIds)
                <div id="print-area-{{ $pageIdx }}" class="mass-print-sheet" data-sheet="{{ $sheetSize }}" style="background:white;border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:20px;">
                    <div class="no-print" style="padding:8px 12px;background:var(--gray-50);border-bottom:1px solid var(--border);font-size:0.75rem;color:var(--text-muted);">
                        Sheet {{ $pageIdx + 1 }} of {{ $pages->count() }} | {{ strtoupper($sheetSize) }}
                    </div>
                    <div class="sheet-grid" data-sheet="{{ $sheetSize }}" style="margin:0 auto;">
                        @php
                            // Pair IDs: person A front | person B front (row 1), person A back | person B back (row 2)
                            // More efficient: pair A front+back | pair B front+back
                            $pairs = $pageIds->chunk(2);
                        @endphp
                        @foreach($pairs as $pair)
                        <div class="pair-row">
                            @foreach($pair as $idItem)
                            <div class="pair-column">
                                {{-- Front --}}
                                <div class="card-slot mirror-image" style="width:85.6mm;height:53.98mm;overflow:hidden;">
                                    <div style="width:85.6mm;height:53.98mm;transform:scaleX(-1);">
                                        @include('livewire.pages.ids.partials.id-front', ['bId' => $idItem])
                                    </div>
                                </div>
                                {{-- Back --}}
                                <div class="card-slot mirror-image" style="width:85.6mm;height:53.98mm;overflow:hidden;margin-top:0;">
                                    <div style="width:85.6mm;height:53.98mm;transform:scaleX(-1);">
                                        @include('livewire.pages.ids.partials.id-back', ['bId' => $idItem])
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @if($pair->count() < 2)
                            {{-- Empty slot --}}
                            <div class="pair-column" style="width:85.6mm;height:107.96mm;border:1px dashed #ddd;display:flex;align-items:center;justify-content:center;">
                                <span style="color:#ccc;font-size:10px;">Empty</span>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach

            @else
                <div class="section-card" style="text-align:center;padding:60px 20px;">
                    <div style="font-size:2rem;margin-bottom:12px;">📋</div>
                    <div style="font-size:1rem;font-weight:600;color:var(--text-secondary);margin-bottom:4px;">No IDs Selected</div>
                    <div style="font-size:0.8125rem;color:var(--text-muted);">Select one or more active IDs from the left panel to preview and print.</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Print Styles --}}
    <style>
        /* Screen layout: pair columns side by side */
        .pair-row {
            display: flex;
            gap: 4px;
            padding: 4px;
            justify-content: center;
        }
        .pair-column {
            flex-shrink: 0;
        }
        .pair-column > div { margin: 0; }

        /* Sheet grids */
        .sheet-grid[data-sheet="a4"] { width: 210mm; padding: 13.55mm 19.4mm; background: white; }
        .sheet-grid[data-sheet="letter"] { width: 215.9mm; padding: 4.75mm 22.35mm; background: white; }
        .sheet-grid[data-sheet="a5"] { width: 148mm; padding: 24.03mm 31.2mm; background: white; }

        .card-slot > div { width: 100% !important; height: 100% !important; }

        /* Print */
        @media print {
            .no-print, .sidebar, .mobile-topbar { display: none !important; }
            body { margin: 0 !important; padding: 0 !important; background: #fff !important; }
            .app-main { margin-left: 0 !important; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }

            .mass-print-sheet {
                border: none !important; border-radius: 0 !important;
                box-shadow: none !important; margin: 0 !important;
                page-break-after: always;
            }
            .sheet-grid[data-sheet="a4"] {
                width: 210mm !important; padding: 13.55mm 19.4mm !important;
            }
            .sheet-grid[data-sheet="letter"] {
                width: 215.9mm !important; padding: 4.75mm 22.35mm !important;
            }
            .sheet-grid[data-sheet="a5"] {
                width: 148mm !important; padding: 24.03mm 31.2mm !important;
            }
            .pair-row {
                gap: 0 !important; padding: 0 !important;
                justify-content: center !important;
            }
            .card-slot {
                width: 85.6mm !important; height: 53.98mm !important;
                overflow: hidden !important; border: none !important;
            }
            .card-slot > div {
                width: 85.6mm !important; height: 53.98mm !important;
                transform: scaleX(-1) !important;
            }
            .card-slot > div > div { width: 85.6mm !important; height: 53.98mm !important; }
        }

        @media screen and (max-width: 1200px) {
            .sheet-grid { transform: scale(0.5); transform-origin: top center; }
        }
        @media screen and (min-width: 1201px) and (max-width: 1600px) {
            .sheet-grid { transform: scale(0.65); transform-origin: top center; }
        }
    </style>

    @script
    <script>
    window.addEventListener('print-sheet', () => { window.print(); });
    </script>
    @endscript
</div>
</div>
