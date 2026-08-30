<div style="width:100%;height:100%;display:flex;flex-direction:column;overflow:hidden;background:white;">
    {{-- Header bar --}}
    <div style="background:linear-gradient(135deg,#1e3a5f 0%,#0f2440 100%);padding:7px 14px;color:#fff;display:flex;align-items:center;gap:8px;flex-shrink:0;">
        <img src="{{ asset('images/mockup-municipality-logo.svg') }}" style="width:24px;height:24px;object-fit:contain;flex-shrink:0;border-radius:50%;opacity:0.85;">
        <div style="font-size:9.5px;font-weight:700;letter-spacing:0.06em;opacity:0.95;flex:1;text-align:center;">BARANGAY IDENTIFICATION CARD (BACK)</div>
        <img src="{{ asset('images/mockup-barangay-logo.svg') }}" style="width:24px;height:24px;object-fit:contain;flex-shrink:0;border-radius:50%;opacity:0.85;">
    </div>

    {{-- Body: two columns --}}
    <div style="flex:1;display:flex;padding:8px 14px;gap:0;min-height:0;">

        {{-- Left column: Certification + Emergency + Signatures --}}
        <div style="flex:1;display:flex;flex-direction:column;min-width:0;padding-right:10px;">
            <div style="font-size:7.5px;color:#333;line-height:1.5;text-align:justify;">
                THIS IS TO CERTIFY THAT THE PERSON WHOSE PICTURE AND SIGNATURE APPEAR ON THIS CARD IS A BONAFIDE RESIDENT OF
                <span style="font-weight:800;">BARANGAY {{ strtoupper(\App\Models\BarangaySetting::first()->barangay_name ?? 'MASAGANA') }}</span>,
                {{ strtoupper(\App\Models\BarangaySetting::first()->municipality ?? 'SAN PABLO CITY') }},
                {{ strtoupper(\App\Models\BarangaySetting::first()->province ?? 'LAGUNA') }}.
            </div>

            {{-- Emergency Contact --}}
            <div style="margin-top:6px;padding-top:5px;border-top:1px solid #e8edf0;">
                <div style="font-size:6.5px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:3px;">In Case of Emergency</div>
                <div style="font-size:8px;color:#222;line-height:1.5;">
                    <span style="font-weight:700;">{{ strtoupper($bId->resident->emergency_contact_name ?? 'N/A') }}</span>
                    <span style="color:#888;font-size:7px;">({{ $bId->resident->emergency_relationship ?? 'N/A' }})</span>
                </div>
                <div style="font-size:7.5px;color:#555;line-height:1.4;">Contact: {{ $bId->resident->emergency_contact_number ?? 'N/A' }}</div>
            </div>

            {{-- Signatures --}}
            <div style="flex:1;display:flex;align-items:flex-end;">
                <div style="display:flex;justify-content:space-between;width:100%;padding-top:6px;">
                    <div style="text-align:center;">
                        <div style="width:72px;border-top:1.5px solid #333;margin:0 auto 2px;"></div>
                        <div style="font-size:6.5px;color:#555;font-weight:600;">Cardholder's Signature</div>
                    </div>
                    <div style="text-align:center;">
                        <div style="width:72px;border-top:1.5px solid #333;margin:0 auto 2px;"></div>
                        <div style="font-size:6.5px;color:#555;font-weight:600;">Barangay Chairman</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right column: Info + Validity --}}
        <div style="width:38%;display:flex;flex-direction:column;border-left:1.5px solid #e2e8f0;padding-left:10px;">
            <div style="background:#f7f9fb;border-radius:5px;padding:8px 10px;border:1px solid #e8edf0;">
                <div style="font-size:6.5px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:5px;">Personal Information</div>
                <div style="font-size:8px;line-height:1.7;">
                    <div><span style="font-weight:700;color:#555;">Blood Type:</span> <span style="color:#111;font-weight:600;">{{ strtoupper($bId->resident->blood_type ?? 'N/A') }}</span></div>
                    <div><span style="font-weight:700;color:#555;">Contact:</span> <span style="color:#111;">{{ $bId->resident->contact_number ?? 'N/A' }}</span></div>
                    <div><span style="font-weight:700;color:#555;">Civil Status:</span> <span style="color:#111;">{{ strtoupper($bId->resident->civil_status ?? 'N/A') }}</span></div>
                </div>
            </div>

            {{-- Validity --}}
            <div style="flex:1;display:flex;flex-direction:column;justify-content:flex-end;">
                <div style="background:linear-gradient(135deg,#1e3a5f,#0f2440);border-radius:5px;padding:8px 10px;color:#fff;">
                    <div style="font-size:6.5px;font-weight:700;opacity:0.7;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:4px;">Validity Period</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:4px;">
                    @php
                        $settings = \App\Models\BarangaySetting::firstOrCreateDefault();
                        $validUntil = $settings->getIdExpiryDate($bId->issued_at);
                    @endphp
                    <div>
                        <div style="font-size:6px;opacity:0.6;">DATE ISSUED</div>
                        <div style="font-size:8px;font-weight:700;">{{ $bId->issued_at?->format('M d, Y') ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div style="font-size:6px;opacity:0.6;">VALID UNTIL</div>
                        <div style="font-size:8px;font-weight:700;">{{ $validUntil?->format('M d, Y') ?? 'N/A' }}</div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
