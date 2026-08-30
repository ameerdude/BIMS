<?php

use Livewire\Volt\Component;
use App\Models\BarangayId;
use App\Models\BarangaySetting;
use App\Services\BarcodeService;
use App\Services\QrCodeService;

new #[Layout("layouts.app")] class extends Component
{
    public BarangayId $bId;

    // Printing method: 'inkjet' or 'direct'
    public string $printMethod = 'inkjet';
    // Direct-to-Card step: 'front' or 'back'
    public string $directStep = 'front';
    // Inkjet single step: 'front' or 'back'
    public string $singleStep = 'front';

    public function mount(BarangayId $bId): void
    {
        $this->bId = $bId->load(['resident', 'issuer']);
    }

    public function printId(): void
    {
        $this->dispatch('print-id');
    }

    public function getBarcodeSvg(): string
    {
        $barcode = $this->bId->barcode_number ?? $this->bId->id_number;
        return BarcodeService::svg($barcode, 28, true);
    }

    public function getQrSvg(): string
    {
        $settings = BarangaySetting::first();
        $resident = $this->bId->resident;
        $barangay = $settings?->barangay_name ?? 'Barangay';
        $residentId = $resident->resident_id_number ?? 'N/A';
        $idNumber = $this->bId->id_number ?? 'N/A';
        $data = "BARANGAY: {$barangay}\nRESIDENT ID: {$residentId}\nID NUMBER: {$idNumber}";
        return QrCodeService::svg($data, 48);
    }

    /** Get card inner HTML (front side) */
    public function getFrontHtml(): string
    {
        return view('livewire.pages.ids.partials.id-front', ['bId' => $this->bId])->render();
    }

    /** Get card inner HTML (back side) */
    public function getBackHtml(): string
    {
        return view('livewire.pages.ids.partials.id-back', ['bId' => $this->bId])->render();
    }


}; ?>

<div>
<div style="max-width:1000px;margin:0 auto;">

    {{-- Controls --}}
    <div class="no-print" style="margin-bottom:20px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <div>
                <h1 style="font-size:1.25rem;font-weight:800;color:var(--text-primary);margin:0;">Print Barangay ID</h1>
                <p style="font-size:0.8125rem;color:var(--text-secondary);margin:2px 0 0;">{{ $this->bId->resident->fullName() }} | {{ $this->bId->id_number }}</p>
            </div>
            <a href="{{ route('residents.show', $this->bId->resident) }}#documents" wire:navigate class="btn btn-outline">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                Back
            </a>
        </div>

        {{-- Method Selector --}}
        <div class="section-card" style="margin-bottom:0;">
            <div style="display:flex;flex-wrap:wrap;gap:20px;">

                {{-- Printing Method --}}
                <div>
                    <div style="font-size:0.75rem;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px;">Printing Method</div>
                    <div style="display:flex;gap:8px;">
                        <button type="button" wire:click="$set('printMethod','inkjet')" style="padding:8px 16px;border-radius:8px;font-size:0.8125rem;font-weight:600;border:2px solid {{ $printMethod==='inkjet' ? 'var(--blue-500)' : 'var(--border)' }};background:{{ $printMethod==='inkjet' ? 'var(--blue-50)' : 'white' }};color:{{ $printMethod==='inkjet' ? 'var(--blue-700)' : 'var(--text-secondary)' }};cursor:pointer;transition:all 0.15s;">
                            🖨️ Inkjet + Lamination
                        </button>
                        <button type="button" wire:click="$set('printMethod','direct')" style="padding:8px 16px;border-radius:8px;font-size:0.8125rem;font-weight:600;border:2px solid {{ $printMethod==='direct' ? 'var(--green-500)' : 'var(--border)' }};background:{{ $printMethod==='direct' ? 'var(--green-50)' : 'white' }};color:{{ $printMethod==='direct' ? 'var(--green-700)' : 'var(--text-secondary)' }};cursor:pointer;transition:all 0.15s;">
                            💳 Direct-to-Card
                        </button>
                    </div>
                </div>

                @if($printMethod === 'inkjet')
                {{-- Single Step --}}
                <div>
                    <div style="font-size:0.75rem;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px;">Card Side</div>
                    <div style="display:flex;gap:8px;">
                        <button type="button" wire:click="$set('singleStep','front')" style="padding:8px 16px;border-radius:8px;font-size:0.8125rem;font-weight:600;border:2px solid {{ $singleStep==='front' ? 'var(--blue-500)' : 'var(--border)' }};background:{{ $singleStep==='front' ? 'var(--blue-50)' : 'white' }};color:{{ $singleStep==='front' ? 'var(--blue-700)' : 'var(--text-secondary)' }};cursor:pointer;">
                            Front
                        </button>
                        <button type="button" wire:click="$set('singleStep','back')" style="padding:8px 16px;border-radius:8px;font-size:0.8125rem;font-weight:600;border:2px solid {{ $singleStep==='back' ? 'var(--blue-500)' : 'var(--border)' }};background:{{ $singleStep==='back' ? 'var(--blue-50)' : 'white' }};color:{{ $singleStep==='back' ? 'var(--blue-700)' : 'var(--text-secondary)' }};cursor:pointer;">
                            Back
                        </button>
                    </div>
                </div>
                @endif

                @if($printMethod === 'direct')
                {{-- Direct Step --}}
                <div>
                    <div style="font-size:0.75rem;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px;">Print Step</div>
                    <div style="display:flex;gap:8px;">
                        <button type="button" wire:click="$set('directStep','front')" style="padding:8px 16px;border-radius:8px;font-size:0.8125rem;font-weight:600;border:2px solid {{ $directStep==='front' ? 'var(--green-500)' : 'var(--border)' }};background:{{ $directStep==='front' ? 'var(--green-50)' : 'white' }};color:{{ $directStep==='front' ? 'var(--green-700)' : 'var(--text-secondary)' }};cursor:pointer;">
                            Step 1: Print Front →
                        </button>
                        <button type="button" wire:click="$set('directStep','back')" style="padding:8px 16px;border-radius:8px;font-size:0.8125rem;font-weight:600;border:2px solid {{ $directStep==='back' ? 'var(--green-500)' : 'var(--border)' }};background:{{ $directStep==='back' ? 'var(--green-50)' : 'white' }};color:{{ $directStep==='back' ? 'var(--green-700)' : 'var(--text-secondary)' }};cursor:pointer;">
                            ← Step 2: Print Back
                        </button>
                    </div>
                </div>
                @endif
            </div>

            {{-- Method Info --}}
            <div style="margin-top:12px;padding:10px 14px;border-radius:8px;font-size:0.8125rem;line-height:1.5;@if($printMethod==='inkjet') background:var(--blue-50);color:var(--blue-700);border:1px solid var(--blue-100);@else background:var(--green-50);color:var(--green-700);border:1px solid var(--green-100);@endif">
                @if($printMethod==='inkjet')
                    <strong>Inkjet + Lamination Sheet</strong>: Card is printed in <strong>mirror image</strong> (flipped horizontally) for lamination. Print {{ strtoupper($singleStep) }} side first, then flip the lamination sheet and print the other side.
                @else
                    <strong>Direct-to-Card</strong>: Card is printed <strong>normal (non-mirrored)</strong>. Print Front first → remove card from printer → flip card horizontally → print Back.
                    @if($directStep==='front') Showing <strong>Front side</strong>. Print this side first. @else Showing <strong>Back side</strong>. Flip the card horizontally and print this side. @endif
                @endif
            </div>

            {{-- Print Button --}}
            <div style="margin-top:12px;display:flex;justify-content:flex-end;">
                <button type="button" wire:click="printId" class="btn btn-primary btn-lg">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    Print Now
                </button>
            </div>
        </div>
    </div>

    {{-- =================== PREVIEW AREA =================== --}}

    @if($printMethod === 'inkjet')
        {{-- ========== INKJET SINGLE CARD: MIRRORED ========== --}}
        <div id="print-area" class="print-single" style="margin-bottom:24px;">
            <div class="no-print" style="font-size:0.8125rem;font-weight:600;color:var(--text-secondary);margin-bottom:8px;">
                {{ strtoupper($singleStep) }} | Mirror Image (for lamination)
            </div>
            <div class="id-card-preview mirror-image" style="width:85.6mm;height:53.98mm;overflow:hidden;margin:0 auto;box-shadow:0 2px 12px rgba(0,0,0,0.12);border-radius:4px;">
                <div style="width:85.6mm;height:53.98mm;transform:scaleX(-1);">
                    @if($singleStep === 'front')
                        {!! $this->getFrontHtml() !!}
                    @else
                        {!! $this->getBackHtml() !!}
                    @endif
                </div>
            </div>
        </div>

    @elseif($printMethod === 'direct')
        {{-- ========== DIRECT-TO-CARD: NON-MIRRORED ========== --}}
        <div id="print-area" class="print-single" style="margin-bottom:24px;">
            <div class="no-print" style="font-size:0.8125rem;font-weight:600;color:var(--text-secondary);margin-bottom:8px;">
                @if($directStep==='front') STEP 1: Print this side FIRST → @else STEP 2: Flip card horizontally, then print ← @endif
            </div>
            <div class="id-card-preview" style="width:85.6mm;height:53.98mm;overflow:hidden;margin:0 auto;box-shadow:0 2px 12px rgba(0,0,0,0.12);border-radius:4px;">
                @if($directStep === 'front')
                    {!! $this->getFrontHtml() !!}
                @else
                    {!! $this->getBackHtml() !!}
                @endif
            </div>
        </div>
    @endif

    {{-- Card Info --}}
    <div class="no-print" style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:20px;">
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;font-size:0.8125rem;">
            <div>
                <div style="color:var(--text-muted);font-size:0.6875rem;margin-bottom:2px;">ID Number</div>
                <div style="font-weight:700;font-family:'JetBrains Mono',monospace;color:var(--blue-600);">{{ $this->bId->id_number }}</div>
            </div>
            <div>
                <div style="color:var(--text-muted);font-size:0.6875rem;margin-bottom:2px;">Barcode</div>
                <div style="font-weight:700;font-family:'JetBrains Mono',monospace;">{{ $this->bId->barcode_number ?? 'N/A' }}</div>
            </div>
            <div>
                <div style="color:var(--text-muted);font-size:0.6875rem;margin-bottom:2px;">Status</div>
                <div><span class="badge {{ $this->bId->status === 'active' ? 'badge-green' : 'badge-red' }}">{{ ucfirst($this->bId->status) }}</span></div>
            </div>
        </div>
    </div>

    {{-- Print Styles --}}
    <style>
        /* ID card preview sizing */
        .id-card-preview > div { width: 100% !important; height: 100% !important; }

        /* ========== PRINT MEDIA QUERIES ========== */
        @media print {
            .no-print, .sidebar, .mobile-topbar { display: none !important; }
            body { margin: 0 !important; padding: 0 !important; background: #fff !important; }
            .app-main { margin-left: 0 !important; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }

            /* ---- Inkjet Single / Direct-to-Card: Single card ---- */
            .print-single {
                border: none !important; border-radius: 0 !important;
                box-shadow: none !important; margin: 0 !important;
                text-align: center;
            }
            .id-card-preview {
                width: 85.6mm !important; height: 53.98mm !important;
                border: none !important; border-radius: 0 !important;
                box-shadow: none !important; margin: 0 auto !important;
            }
            .id-card-preview > div {
                width: 85.6mm !important; height: 53.98mm !important;
            }
        }


    </style>

    @script
    <script>
    window.addEventListener('print-id', () => { window.print(); });
    </script>
    @endscript
</div>
</div>
