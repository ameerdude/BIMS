<?php

use Livewire\Volt\Component;
use App\Models\DocumentIssued;
use App\Models\DocumentTemplate;
use App\Models\Resident;
use App\Models\RevenueRecord;

ew #[Layout("layouts.app")] class extends Component
{
    public int $resident_id = 0;
    public string $document_type = 'barangay_clearance';
    public string $purpose = '';
    public string $remarks = '';
    public string $searchTerm = '';
    public $selectedResident = null;
    public bool $recordPayment = true;

    public function updatedResidentId($v)
    {
        $this->selectedResident = $v ? Resident::find($v) : null;
    }

    public function residents()
    {
        if (!$this->searchTerm) return collect();
        return Resident::where('is_active', true)
            ->where(fn($q) => $q->where('first_name', 'like', "%{$this->searchTerm}%")
                ->orWhere('last_name', 'like', "%{$this->searchTerm}%"))
            ->limit(10)
            ->get();
    }

    public function issue(): void
    {
        $this->validate([
            'resident_id' => 'required|exists:residents,id',
            'document_type' => 'required',
            'purpose' => 'required',
        ]);

        $doc = DocumentIssued::create([
            'resident_id' => $this->resident_id,
            'document_type' => $this->document_type,
            'control_number' => DocumentIssued::generateControlNumber($this->document_type),
            'issued_by' => auth()->id(),
            'issued_at' => now(),
            'purpose' => $this->purpose,
            'remarks' => $this->remarks,
            'qr_token' => DocumentIssued::generateQrToken(),
            'status' => 'active',
        ]);

        // Auto-log revenue if fee exists and payment recording is enabled
        if ($this->recordPayment) {
            $template = DocumentTemplate::where('slug', $this->document_type)->first();
            $fee = $template?->fee ? (float) preg_replace('/[^0-9.]/', '', $template->fee) : 0;
            if ($fee > 0) {
                $resident = Resident::find($this->resident_id);
                RevenueRecord::create([
                    'or_number' => RevenueRecord::generateOrNumber(),
                    'category' => 'document_fee',
                    'description' => ($template->label ?? $this->document_type) . ': ' . $this->purpose,
                    'payer_id' => $this->resident_id,
                    'payer_name' => $resident?->fullName() ?? 'Unknown',
                    'amount' => $fee,
                    'payment_date' => now(),
                    'payment_method' => 'cash',
                    'received_by' => auth()->id(),
                    'remarks' => 'Control No: ' . $doc->control_number,
                ]);
            }
        }

        $this->redirect(route('documents.index'), navigate: true);
    }
}; ?>

<div>
<div class="max-w-3xl mx-auto">
<form wire:submit="issue" class="card p-6 space-y-6">
{{-- Resident Search --}}
<div>
<label class="block text-sm font-bold text-gray-700 mb-1">Search Resident *</label>
<input type="text" wire:model.live="searchTerm" placeholder="Type resident name..."
class="form-input">
@if($this->residents()->count())
<div class="card mt-2 max-h-48 overflow-y-auto">
@foreach($this->residents() as $r)
<button type="button" wire:click="$set('resident_id', {{ $r->id }})"
class="w-full text-left px-4 py-2 text-sm hover:bg-indigo-50 border-b border-gray-100 last:border-0 {{ $resident_id == $r->id ? 'bg-indigo-50 font-bold' : '' }}">
{{ $r->fullName() }}
<span class="text-gray-400 text-xs ml-2">Age {{ $r->getAge() }}</span>
</button>
@endforeach
</div>
@endif
<input type="hidden" wire:model="resident_id">
</div>
@if($selectedResident)
<div class="alert alert alert-success text-sm">
<span class="text-lg">✅</span>
<span>Selected: <strong>{{ $selectedResident->fullName() }}</strong> | {{ $selectedResident->purok ?? 'No purok' }}</span>
</div>
@endif
{{-- Document Details --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<div>
<label class="block text-sm font-bold text-gray-700">Document Type *</label>
<select wire:model="document_type" class="form-input mt-1">
<option value="barangay_clearance">Barangay Clearance</option>
<option value="certificate_of_residency">Certificate of Residency</option>
<option value="certificate_of_indigency">Certificate of Indigency</option>
<option value="business_clearance">Business Clearance</option>
</select>
</div>
<div>
<label class="block text-sm font-bold text-gray-700">Purpose *</label>
<input type="text" wire:model="purpose" placeholder="e.g., Employment, Loan, etc."
class="form-input mt-1">
</div>
</div>
<div>
<label class="block text-sm font-bold text-gray-700">Remarks</label>
<textarea wire:model="remarks" rows="2" class="form-input mt-1" placeholder="Additional notes..."></textarea>
</div>
<div class="flex items-center gap-2">
<input type="checkbox" wire:model="recordPayment" id="recordPayment" class="w-4 h-4">
<label for="recordPayment" class="text-sm text-gray-700">Auto-record payment to Revenue/Treasury</label>
</div>
@php
    $tpl = \App\Models\DocumentTemplate::where('slug', $document_type)->first();
    $fee = $tpl?->fee ?? 'Free';
@endphp
@if($recordPayment && $fee !== 'Free')
<div class="alert alert-success text-sm" style="padding:8px 12px;">
    💰 Will record <strong>{{ $fee }}</strong> payment for this document.
</div>
@endif
<div class="flex justify-end gap-3">
<a href="{{ route('documents.index') }}" wire:navigate class="btn btn btn-outline text-sm">Cancel</a>
<button type="submit" class="btn text-sm">🖨️ Issue Document</button>
</div>
</form>
</div>
</div>

