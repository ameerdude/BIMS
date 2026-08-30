<?php

use Livewire\Volt\Component;
use App\Models\BarangayId;
use App\Models\BarangaySetting;
use App\Models\Resident;
use App\Models\RevenueRecord;

ew #[Layout("layouts.app")] class extends Component
{
    public int $resident_id = 0;
    public string $reason = '';
    public string $searchTerm = '';
    public $selectedResident = null;
    public bool $recordPayment = true;

    public function updatedResidentId($v) { $this->selectedResident = $v ? Resident::find($v) : null; }

    public function residents()
    {
        if (!$this->searchTerm) return collect();
        return Resident::where('is_active', true)
            ->where(fn($q) => $q->where('first_name', 'like', "%{$this->searchTerm}%")
                ->orWhere('last_name', 'like', "%{$this->searchTerm}%"))
            ->limit(10)->get();
    }

    public function issue(): void
    {
        $this->validate(['resident_id' => 'required|exists:residents,id']);

        $existing = BarangayId::where('resident_id', $this->resident_id)->where('status', 'active')->first();
        $version = 1;

        if ($existing) {
            $existing->update(['status' => 'cancelled']);
            $version = $existing->version + 1;
        }

        $barangayId = BarangayId::create([
            'resident_id' => $this->resident_id,
            'id_number' => BarangayId::generateIdNumber(),
            'barcode_number' => BarangayId::generateBarcodeNumber(),
            'version' => $version,
            'reason' => $this->reason,
            'qr_token' => \Illuminate\Support\Str::random(32) . time(),
            'status' => 'active',
            'issued_at' => now(),
            'issued_by' => auth()->id(),
        ]);

        // Auto-log revenue for ID card fee
        if ($this->recordPayment) {
            $settings = BarangaySetting::firstOrCreateDefault();
            $fee = $settings->id_card_fee ?? 50;
            if ($fee > 0) {
                $resident = Resident::find($this->resident_id);
                RevenueRecord::create([
                    'or_number' => RevenueRecord::generateOrNumber(),
                    'category' => 'id_card_fee',
                    'description' => 'Barangay ID Card: ' . ($this->reason ?: 'First-time issue'),
                    'payer_id' => $this->resident_id,
                    'payer_name' => $resident?->fullName() ?? 'Unknown',
                    'amount' => $fee,
                    'payment_date' => now(),
                    'payment_method' => 'cash',
                    'received_by' => auth()->id(),
                    'remarks' => 'ID No: ' . $barangayId->id_number . ' (v' . $version . ')',
                ]);
            }
        }

        $this->redirect(route('ids.index'), navigate: true);
    }
}; ?>

<div>
<div class="max-w-3xl mx-auto">
<form wire:submit="issue" class="card p-6 space-y-6">
<div>
<label class="block text-sm font-bold text-gray-700 mb-1">Search Resident *</label>
<input type="text" wire:model.live="searchTerm" placeholder="Type name..." class="form-input">
@if($this->residents()->count())
<div class="card mt-2 max-h-48 overflow-y-auto">
@foreach($this->residents() as $r)
<button type="button" wire:click="$set('resident_id', {{ $r->id }})"
class="w-full text-left px-4 py-2 text-sm hover:bg-indigo-50 border-b last:border-0 {{ $resident_id == $r->id ? 'bg-indigo-50 font-bold' : '' }}">
{{ $r->fullName() }}
</button>
@endforeach
</div>
@endif
<input type="hidden" wire:model="resident_id">
</div>
@if($selectedResident)
<div class="alert alert alert-success text-sm">
<span>✅</span>
<span>Selected: <strong>{{ $selectedResident->fullName() }}</strong></span>
</div>
@endif
<div>
<label class="block text-sm font-bold text-gray-700">Reason (for reissue)</label>
<input type="text" wire:model="reason" placeholder="e.g., Lost ID, Damaged, First-time" class="form-input mt-1">
</div>
<div class="flex items-center gap-2">
<input type="checkbox" wire:model="recordPayment" id="recordPayment" class="w-4 h-4">
<label for="recordPayment" class="text-sm text-gray-700">Auto-record payment to Revenue/Treasury</label>
</div>
<div class="flex justify-end gap-3">
<a href="{{ route('ids.index') }}" wire:navigate class="btn btn btn-outline text-sm">Cancel</a>
<button type="submit" class="btn text-sm">Issue ID</button>
</div>
</form>
</div>
</div>

