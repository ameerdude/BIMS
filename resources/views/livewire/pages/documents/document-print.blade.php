<?php

use Livewire\Volt\Component;
use App\Models\DocumentIssued;
use App\Models\DocumentTemplate;
use App\Models\BarangaySetting;
use App\Models\Official;
use App\Services\QrCodeService;

new #[Layout("layouts.app")] class extends Component
{
    public DocumentIssued $document;
    public ?DocumentTemplate $template = null;

    public function mount(DocumentIssued $document): void
    {
        $this->document = $document->load(['resident', 'issuer']);
        $this->template = DocumentTemplate::where('slug', $this->document->document_type)->first();
    }

    public function printPage(): void
    {
        $this->dispatch('print-doc');
    }

    public function getTemplateData(): array
    {
        $resident = $this->document->resident;
        $settings = BarangaySetting::first();
        $captain = Official::current()->where('position', 'Barangay Captain')->first();

        return [
            'full_name' => $resident->fullName(),
            'first_name' => $resident->first_name,
            'last_name' => $resident->last_name,
            'sex' => ucfirst($resident->sex ?? ''),
            'civil_status' => ucfirst($resident->civil_status ?? ''),
            'purok' => $resident->purok ?? 'Purok ___',
            'barangay' => $settings->barangay_name ?? '_____',
            'municipality' => $settings->municipality ?? '_____',
            'province' => $settings->province ?? '_____',
            'purpose' => $this->document->purpose ?? '',
            'control_number' => $this->document->control_number ?? '',
            'date_issued' => $this->document->issued_at?->format('F d, Y') ?? date('F d, Y'),
            'date_today' => date('F, Y'),
            'prepared_by' => $this->document->issuer->name ?? 'Staff',
            'approved_by' => $captain->name ?? '_______________',
            'age' => $resident->birthdate ? Carbon\Carbon::parse($resident->birthdate)->age : '',
            'validity' => $this->template?->getValidityDescription() ?? '6 months',
            'valid_until' => $this->template?->getExpiryDate($this->document->issued_at)?->format('F d, Y') ?? '',
        ];
    }

    public function getPaperStyle(): string
    {
        $dims = $this->template ? $this->template->getPaperDimensions() : ['w' => '8.5in', 'h' => '11in'];
        $orientation = $this->template?->orientation ?? 'portrait';
        $padding = $orientation === 'landscape' ? '36px 48px' : '48px 56px';
        return "width:{$dims['w']};min-height:{$dims['h']};padding:{$padding};margin:0 auto;font-family:'Times New Roman',Georgia,serif;color:#1a1a1a;background:#fff;";
    }

    /**
     * Replace placeholders in a single line (safe from Blade escaping).
     */
    public function renderLine(string $line, array $data): string
    {
        $replacements = [
            '{{full_name}}' => $data['full_name'] ?? '',
            '{{first_name}}' => $data['first_name'] ?? '',
            '{{last_name}}' => $data['last_name'] ?? '',
            '{{sex}}' => $data['sex'] ?? '',
            '{{civil_status}}' => $data['civil_status'] ?? '',
            '{{purok}}' => $data['purok'] ?? '',
            '{{barangay}}' => $data['barangay'] ?? '',
            '{{municipality}}' => $data['municipality'] ?? '',
            '{{province}}' => $data['province'] ?? '',
            '{{purpose}}' => $data['purpose'] ?? '',
            '{{control_number}}' => $data['control_number'] ?? '',
            '{{date_issued}}' => $data['date_issued'] ?? '',
            '{{date_today}}' => $data['date_today'] ?? '',
            '{{prepared_by}}' => $data['prepared_by'] ?? '',
            '{{approved_by}}' => $data['approved_by'] ?? '',
            '{{age}}' => $data['age'] ?? '',
        ];
        return str_replace(array_keys($replacements), array_values($replacements), $line);
    }

    public function getQrSvg(): string
    {
        $settings = BarangaySetting::first();
        $resident = $this->document->resident;
        $barangay = $settings?->barangay_name ?? 'Barangay';
        $residentId = $resident->resident_id_number ?? 'N/A';
        $data = "BARANGAY: {$barangay}\nRESIDENT ID: {$residentId}\nCONTROL NO: {$this->document->control_number}";
        return QrCodeService::svg($data, 80);
    }
}; ?>

<div>
<div style="max-width:900px;margin:0 auto;">

    {{-- Controls --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <a href="{{ route('residents.show', $document->resident) }}#documents" wire:navigate class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back to Resident
        </a>
        <div style="display:flex;gap:8px;align-items:center;">
            @if($template)
            <span class="badge badge-gray" style="font-size:0.6875rem;">
                {{ strtoupper($template->orientation ?? 'portrait') }} · {{ strtoupper($template->paper_size ?? 'letter') }}
            </span>
            @endif
            <button type="button" wire:click="printPage" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Print
            </button>
        </div>
    </div>

    {{-- Document --}}
    <div style="background:white;border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;">
    <div id="print-area" style="{{ $this->getPaperStyle() }}">

        @php
            $data = $this->getTemplateData();
            $settings = \App\Models\BarangaySetting::first();
            $logoPath = $settings?->logo_path ? asset('storage/' . $settings->logo_path) : asset('images/mockup-logo.svg');
            $sealPath = $settings?->seal_path ? asset('storage/' . $settings->seal_path) : asset('images/mockup-seal.svg');
        @endphp

        @if($template)
            {{-- ============ TEMPLATE-BASED RENDERING ============ --}}

            {{-- Philippine Standard Header: Left Logo | Center Text | Right Logo --}}
            <div style="margin-bottom:24px;padding-bottom:16px;border-bottom:2px solid #1a1a1a;">
                <div style="display:flex;align-items:center;gap:16px;">
                    {{-- Left: Municipality/City Logo --}}
                    @if($template->show_logo)
                    <div style="flex:0 0 80px;text-align:center;">
                        <img src="{{ $logoPath }}" style="width:80px;height:80px;object-fit:contain;">
                    </div>
                    @endif

                    {{-- Center: Republic Header Text --}}
                    <div style="flex:1;text-align:center;">
                        @if($template->header_line_1)
                        <div style="font-size:0.9375rem;text-transform:uppercase;letter-spacing:0.1em;">{!! $this->renderLine($template->header_line_1, $data) !!}</div>
                        @endif
                        @if($template->header_line_2)
                        <div style="font-size:0.8125rem;margin-top:3px;">{!! $this->renderLine($template->header_line_2, $data) !!}</div>
                        @endif
                        @if($template->header_line_3)
                        <div style="font-size:0.8125rem;">{!! $this->renderLine($template->header_line_3, $data) !!}</div>
                        @endif
                        @if($template->header_line_4)
                        <div style="font-size:1rem;font-weight:bold;text-transform:uppercase;margin-top:10px;letter-spacing:0.05em;">{!! $this->renderLine($template->header_line_4, $data) !!}</div>
                        @endif
                    </div>

                    {{-- Right: Barangay Logo/Seal --}}
                    @if($template->show_seal)
                    <div style="flex:0 0 80px;text-align:center;">
                        <img src="{{ $sealPath }}" style="width:80px;height:80px;object-fit:contain;">
                    </div>
                    @endif
                </div>
            </div>

            {{-- Document Title --}}
            <div style="text-align:center;margin:24px 0;">
                <h1 style="font-size:1.375rem;font-weight:bold;text-transform:uppercase;text-decoration:underline;margin:0;">{{ $template->label }}</h1>
                @if($template->show_control_number)
                <div style="font-size:0.875rem;color:#666;margin-top:6px;">Control No: <strong>{{ $data['control_number'] }}</strong></div>
                @endif
            </div>

            {{-- Body --}}
            <div style="font-size:1.0625rem;line-height:1.8;text-align:justify;margin:24px 0;">
                {!! $template->renderBody($data) !!}
            </div>

            {{-- Custom footer text --}}
            @if($template->footer_text)
            <div style="font-size:0.875rem;color:#555;margin:16px 0;padding:12px;background:#f9f9f9;border-left:3px solid #ccc;">
                {!! nl2br(e($template->footer_text)) !!}
            </div>
            @endif

            {{-- Signatures --}}
            <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-top:48px;padding-top:24px;">
                <div style="text-align:center;">
                    <div style="border-top:2px solid #1a1a1a;width:200px;margin-bottom:4px;"></div>
                    <div style="font-size:0.8125rem;font-weight:bold;">Prepared by:</div>
                    <div style="font-size:0.875rem;">{{ $data['prepared_by'] }}</div>
                    <div style="font-size:0.75rem;color:#888;">{{ $template->prepared_by_title }}</div>
                </div>

                @if($template->show_qr_code)
                <div style="text-align:center;">
                    <div style="font-size:0.6875rem;color:#aaa;margin-bottom:4px;">Scan to verify</div>
                    <div style="display:flex;justify-content:center;">
                        {!! $this->getQrSvg() !!}
                    </div>
                    <div style="font-size:0.5625rem;color:#bbb;margin-top:4px;">{{ $data['control_number'] }}</div>
                </div>
                @endif

                <div style="text-align:center;">
                    <div style="border-top:2px solid #1a1a1a;width:200px;margin-bottom:4px;"></div>
                    <div style="font-size:0.8125rem;font-weight:bold;">Approved by:</div>
                    <div style="font-size:0.875rem;">HON. {{ strtoupper($data['approved_by']) }}</div>
                    <div style="font-size:0.75rem;color:#888;">{{ $template->approved_by_title }}</div>
                </div>
            </div>

            <div style="text-align:center;margin-top:32px;font-size:0.75rem;color:#aaa;">
                Date Issued: {{ $data['date_issued'] }}@if($data['valid_until']) | Valid Until: {{ $data['valid_until'] }}@endif | Document ID: {{ $document->id }}
            </div>

        @else
            {{-- ============ FALLBACK: NO TEMPLATE ============ --}}
            <div style="text-align:center;margin-bottom:24px;padding-bottom:16px;border-bottom:2px solid #1a1a1a;">
                <div style="margin-bottom:12px;">
                    <img src="{{ $logoPath }}" style="width:80px;height:80px;object-fit:contain;">
                </div>
                <div style="font-size:1.25rem;font-weight:bold;text-transform:uppercase;">Republic of the Philippines</div>
                <div style="font-size:1rem;margin-top:4px;">{{ $settings?->municipality ?? 'Municipality' }}</div>
                <div style="font-size:1rem;">{{ $settings?->province ?? 'Province' }}</div>
                <div style="font-size:1.125rem;font-weight:bold;text-transform:uppercase;margin-top:12px;">Barangay {{ $settings?->barangay_name ?? '_____' }}</div>
                <div style="margin-top:12px;">
                    <img src="{{ $sealPath }}" style="width:60px;height:60px;object-fit:contain;">
                </div>
            </div>

            <div style="text-align:center;margin:24px 0;">
                <h1 style="font-size:1.375rem;font-weight:bold;text-transform:uppercase;text-decoration:underline;">{{ $document->getDocumentTypeLabel() }}</h1>
                <div style="font-size:0.875rem;color:#666;margin-top:6px;">Control No: <strong>{{ $document->control_number }}</strong></div>
            </div>

            <div style="font-size:1.0625rem;line-height:1.8;text-align:justify;margin:24px 0;">
                <p>TO WHOM IT MAY CONCERN:</p>
                <p>This is to certify the following: <strong>{{ $document->purpose }}</strong></p>
            </div>

            <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-top:48px;padding-top:24px;">
                <div style="text-align:center;">
                    <div style="border-top:2px solid #1a1a1a;width:200px;margin-bottom:4px;"></div>
                    <div style="font-size:0.8125rem;font-weight:bold;">Prepared by:</div>
                    <div style="font-size:0.875rem;">{{ $data['prepared_by'] }}</div>
                </div>
                <div style="text-align:center;">
                    <div style="border-top:2px solid #1a1a1a;width:200px;margin-bottom:4px;"></div>
                    <div style="font-size:0.8125rem;font-weight:bold;">Approved by:</div>
                    <div style="font-size:0.875rem;">HON. {{ strtoupper($data['approved_by']) }}</div>
                </div>
            </div>
        @endif

    </div>
    </div>
</div>
</div>

@script
<script>
window.addEventListener('print-doc', () => { window.print(); });
</script>
@endscript
