<div style="width:100%;height:100%;display:flex;flex-direction:column;overflow:hidden;background:white;">
    {{-- Header bar with dual logos --}}
    <div style="background:linear-gradient(135deg,#1e3a5f 0%,#0f2440 100%);padding:7px 14px;color:#fff;display:flex;align-items:center;gap:10px;flex-shrink:0;">
        <img src="{{ asset('images/mockup-municipality-logo.svg') }}" style="width:32px;height:32px;object-fit:contain;flex-shrink:0;border-radius:50%;">
        <div style="flex:1;min-width:0;text-align:center;">
            <div style="font-size:7.5px;opacity:0.8;letter-spacing:0.1em;line-height:1.3;">REPUBLIC OF THE PHILIPPINES</div>
            <div style="font-size:10px;font-weight:800;line-height:1.4;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">BARANGAY {{ strtoupper(\App\Models\BarangaySetting::first()->barangay_name ?? 'SAMPLE') }}</div>
            <div style="font-size:6.5px;opacity:0.6;line-height:1.2;">{{ \App\Models\BarangaySetting::first()->municipality ?? 'Municipality' }}, {{ \App\Models\BarangaySetting::first()->province ?? 'Province' }}</div>
        </div>
        <img src="{{ asset('images/mockup-barangay-logo.svg') }}" style="width:32px;height:32px;object-fit:contain;flex-shrink:0;border-radius:50%;">
    </div>

    {{-- Body: Photo left + Details right --}}
    <div style="flex:1;display:flex;padding:6px 14px;gap:12px;min-height:0;align-items:stretch;">
        {{-- Left: Photo --}}
        <div style="flex-shrink:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
            <div style="width:68px;height:80px;border-radius:4px;background:linear-gradient(135deg,#e8edf3,#d5dce6);border:2px solid #b0bcc8;display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:800;color:#3b5998;overflow:hidden;">
                {{ substr($bId->resident->first_name, 0, 1) }}{{ substr($bId->resident->last_name, 0, 1) }}
            </div>
            <div style="font-size:6px;color:#aaa;margin-top:2px;text-align:center;font-weight:500;">2×2 Photo</div>
        </div>

        {{-- Right: Details --}}
        <div style="flex:1;display:flex;flex-direction:column;justify-content:center;min-width:0;">
            <div style="font-size:13px;font-weight:800;color:#111;line-height:1.2;letter-spacing:0.02em;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ strtoupper($bId->resident->fullName()) }}</div>
            <div style="margin-top:4px;display:grid;grid-template-columns:auto 1fr;gap:2px 6px;font-size:8.5px;line-height:1.5;">
                <span style="font-weight:700;color:#555;">Sex:</span>
                <span style="color:#222;">{{ strtoupper($bId->resident->sex ?? '') }}</span>
                <span style="font-weight:700;color:#555;">Purok:</span>
                <span style="color:#222;">{{ $bId->resident->purok ?? '' }}</span>
                <span style="font-weight:700;color:#555;">Birthdate:</span>
                <span style="color:#222;">{{ $bId->resident->birthdate?->format('M d, Y') ?? '' }}</span>
            </div>
            <div style="margin-top:5px;font-size:9.5px;font-weight:800;font-family:'Courier New',monospace;letter-spacing:0.06em;color:#1e3a5f;background:#edf2f7;padding:4px 10px;border-radius:4px;display:inline-block;border:1px solid #d4dde6;">
                {{ $bId->id_number }}
            </div>
        </div>
    </div>

    {{-- Footer: Barcode + QR --}}
    <div style="padding:4px 14px 6px;background:#f7f9fb;border-top:1.5px solid #e2e8f0;display:flex;align-items:center;gap:10px;flex-shrink:0;">
        <div style="flex:1;text-align:center;">
            {!! $bId->barcode_number ? \App\Services\BarcodeService::svg($bId->barcode_number, 28, true) : \App\Services\BarcodeService::svg($bId->id_number, 28, true) !!}
        </div>
        <div style="width:1px;height:32px;background:#d0d5db;flex-shrink:0;"></div>
        <div style="flex-shrink:0;display:flex;align-items:center;justify-content:center;">
            @php
                $settings = \App\Models\BarangaySetting::first();
                $residentId = $bId->resident->resident_id_number ?? 'N/A';
                $idNumber = $bId->id_number ?? 'N/A';
                $data = "BARANGAY: {$settings?->barangay_name}\nRESIDENT ID: {$residentId}\nID NUMBER: {$idNumber}";
            @endphp
            {!! \App\Services\QrCodeService::svg($data, 48) !!}
        </div>
    </div>
</div>
