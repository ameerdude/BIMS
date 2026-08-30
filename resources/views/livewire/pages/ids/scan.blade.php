<?php

use Livewire\Volt\Component;
use App\Models\BarangayId;
use App\Models\Resident;

new #[Layout("layouts.app")] class extends Component
{
    public string $scanInput = '';
    public ?Resident $foundResident = null;
    public ?BarangayId $foundId = null;
    public string $scanStatus = ''; // 'success', 'error', ''
    public string $scanMessage = '';

    public function lookupBarcode(): void
    {
        $code = trim($this->scanInput);
        if (empty($code)) return;

        $this->foundResident = null;
        $this->foundId = null;

        // Try barcode_number first
        $barangayId = BarangayId::where('barcode_number', $code)
            ->where('status', 'active')
            ->with('resident')
            ->first();

        // Fallback: try id_number
        if (!$barangayId) {
            $barangayId = BarangayId::where('id_number', $code)
                ->where('status', 'active')
                ->with('resident')
                ->first();
        }

        // Fallback: try resident_id_number
        if (!$barangayId) {
            $resident = Resident::where('resident_id_number', $code)->where('is_active', true)->first();
            if ($resident) {
                $this->foundResident = $resident;
                $this->foundId = $resident->barangayIds()->where('status', 'active')->latest()->first();
                $this->scanStatus = 'success';
                $this->scanMessage = 'Resident found: ' . $resident->fullName();
                return;
            }
        }

        if ($barangayId && $barangayId->resident) {
            $this->foundResident = $barangayId->resident;
            $this->foundId = $barangayId;
            $this->scanStatus = 'success';
            $this->scanMessage = 'Resident found: ' . $barangayId->resident->fullName();
        } else {
            $this->scanStatus = 'error';
            $this->scanMessage = 'No resident found for code: ' . $code;
        }

        $this->scanInput = '';
    }

    public function startCamera(): void
    {
        $this->dispatch('start-camera');
    }
}; ?>

<div>
<div style="max-width:800px;margin:0 auto;">

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <div>
            <h1 style="font-size:1.25rem;font-weight:800;color:var(--text-primary);margin:0;">Scan ID</h1>
            <div style="font-size:0.8125rem;color:var(--text-secondary);margin-top:2px;">Use a barcode scanner or camera to look up a resident</div>
        </div>
        <a href="{{ route('residents.index') }}" wire:navigate class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back to Residents
        </a>
    </div>

    {{-- Scanner Section --}}
    <div class="section-card" style="text-align:center;">
        <div style="width:120px;height:120px;border-radius:var(--radius-xl);background:var(--navy-50);border:2px dashed var(--navy-300);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--navy-400)" stroke-width="1.5">
                <rect x="2" y="4" width="20" height="16" rx="2"/>
                <path d="M7 8h10M7 12h10M7 16h4"/>
                <circle cx="18" cy="18" r="4" fill="var(--blue-600)" stroke="var(--blue-600)"/>
                <path d="M16.5 18h3M18 16.5v3" stroke="#fff" stroke-width="1.5"/>
            </svg>
        </div>

        <div style="font-size:0.875rem;font-weight:600;color:var(--text-primary);margin-bottom:4px;">Barcode / ID Scanner</div>
        <div style="font-size:0.8125rem;color:var(--text-secondary);margin-bottom:20px;">Connect a USB barcode scanner and scan the ID card, or type the code manually below</div>

        {{-- Manual Input --}}
        <form wire:submit="lookupBarcode" style="display:flex;gap:8px;max-width:480px;margin:0 auto;">
            <input type="text" wire:model="scanInput" autofocus placeholder="Scan barcode or type ID number..."
                   style="flex:1;padding:12px 16px;font-size:1rem;font-family:'JetBrains Mono',monospace;font-weight:600;border:2px solid var(--navy-200);border-radius:var(--radius-lg);outline:none;text-align:center;letter-spacing:0.05em;"
                   onfocus="this.style.borderColor='var(--blue-500)'" onblur="this.style.borderColor='var(--navy-200)'">
            <button type="submit" class="btn btn-primary" style="padding:12px 24px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Look Up
            </button>
        </form>

        {{-- Status Message --}}
        @if($scanStatus)
        <div style="margin-top:16px;">
            @if($scanStatus === 'success')
            <div class="alert alert-success" style="max-width:480px;margin:0 auto;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                {{ $scanMessage }}
            </div>
            @else
            <div class="alert alert-danger" style="max-width:480px;margin:0 auto;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                {{ $scanMessage }}
            </div>
            @endif
        </div>
        @endif
    </div>

    {{-- Found Resident Card --}}
    @if($foundResident)
    <div class="section-card" style="margin-top:16px;">
        <div style="display:grid;grid-template-columns:100px 1fr;gap:20px;">
            {{-- Photo --}}
            <div>
                @if($foundResident->photo_path)
                <div style="width:100px;height:100px;border-radius:var(--radius-lg);overflow:hidden;border:2px solid var(--border);background:#fff;">
                    <img src="{{ asset('storage/' . $foundResident->photo_path) }}" style="width:100%;height:100%;object-fit:cover;">
                </div>
                @else
                <div style="width:100px;height:100px;border-radius:var(--radius-lg);background:var(--blue-600);display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:800;color:#fff;">
                    {{ substr($foundResident->first_name, 0, 1) }}{{ substr($foundResident->last_name, 0, 1) }}
                </div>
                @endif
            </div>

            {{-- Info --}}
            <div>
                <div style="font-size:1.125rem;font-weight:800;color:var(--text-primary);">{{ $foundResident->fullName() }}</div>
                <div style="font-size:0.75rem;font-family:'JetBrains Mono',monospace;color:var(--blue-600);margin:2px 0 8px;">{{ $foundResident->resident_id_number }}</div>

                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;font-size:0.8125rem;">
                    <div>
                        <div style="color:var(--text-muted);font-size:0.6875rem;">Sex / Age</div>
                        <div style="font-weight:600;">{{ ucfirst($foundResident->sex) }} / {{ $foundResident->getAge() }} yrs</div>
                    </div>
                    <div>
                        <div style="color:var(--text-muted);font-size:0.6875rem;">Purok</div>
                        <div style="font-weight:600;">{{ $foundResident->purok ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div style="color:var(--text-muted);font-size:0.6875rem;">Civil Status</div>
                        <div style="font-weight:600;">{{ ucfirst($foundResident->civil_status) }}</div>
                    </div>
                    <div>
                        <div style="color:var(--text-muted);font-size:0.6875rem;">Contact</div>
                        <div style="font-weight:600;">{{ $foundResident->contact_number ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div style="color:var(--text-muted);font-size:0.6875rem;">Blood Type</div>
                        <div style="font-weight:600;">{{ $foundResident->blood_type ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div style="color:var(--text-muted);font-size:0.6875rem;">Status</div>
                        <div style="font-weight:600;">{{ $foundResident->is_active ? 'Active' : 'Inactive' }}</div>
                    </div>
                </div>

                {{-- Sector badges --}}
                @if(count($foundResident->getSectorBadges()))
                <div style="display:flex;flex-wrap:wrap;gap:4px;margin-top:8px;">
                    @foreach($foundResident->getSectorBadges() as $badge)
                    <span class="badge badge-blue" style="font-size:0.625rem;">{{ $badge }}</span>
                    @endforeach
                </div>
                @endif

                {{-- Actions --}}
                <div style="display:flex;gap:8px;margin-top:12px;">
                    <a href="{{ route('residents.show', $foundResident) }}" wire:navigate class="btn btn-primary btn-sm">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        View Full Profile
                    </a>
                    @if($foundId)
                    <a href="{{ route('ids.print', $foundId) }}" wire:navigate class="btn btn-outline btn-sm">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v6"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                        Print ID
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Help Text --}}
    <div style="margin-top:24px;padding:16px;background:var(--navy-50);border-radius:var(--radius-lg);font-size:0.8125rem;color:var(--text-secondary);">
        <div style="font-weight:600;margin-bottom:6px;color:var(--text-primary);">How to use:</div>
        <ol style="margin:0;padding-left:20px;line-height:1.8;">
            <li>Connect a USB barcode scanner to this computer</li>
            <li>Click inside the scan input field (or it auto-focuses)</li>
            <li>Scan the barcode on the Barangay ID card</li>
            <li>The system will look up the resident and display their profile</li>
            <li>Or manually type the ID number and press Enter</li>
        </ol>
        <div style="margin-top:8px;font-size:0.75rem;color:var(--text-muted);">
            Supported codes: Barcode Number, ID Number, or Resident ID Number
        </div>
    </div>
</div>
</div>
