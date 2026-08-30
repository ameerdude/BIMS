<?php

use Livewire\Volt\Component;
use App\Models\RevenueRecord;
use App\Models\Resident;

new #[Layout("layouts.app")] class extends Component
{
    public string $category = 'barangay_clearance';
    public string $description = '';
    public string $payer_name = '';
    public ?int $payer_id = null;
    public float $amount = 0;
    public string $payment_date = '';
    public string $payment_method = 'cash';
    public string $remarks = '';
    public string $searchTerm = '';
    public $selectedPayer = null;

    public function updatedPayerId($v) { $this->selectedPayer = $v ? Resident::find($v) : null; }

    public function residents()
    {
        if (strlen($this->searchTerm) < 2) return collect();
        return Resident::where('is_active', true)
            ->where(fn($q) => $q->where('first_name', 'like', "%{$this->searchTerm}%")->orWhere('last_name', 'like', "%{$this->searchTerm}%"))
            ->limit(10)->get();
    }

    public function save(): void
    {
        $this->validate(['payer_name' => 'required', 'amount' => 'required|numeric|min:0', 'payment_date' => 'required|date']);
        RevenueRecord::create([
            'or_number' => 'OR-' . date('Y') . '-' . str_pad(RevenueRecord::count() + 1, 6, '0', STR_PAD_LEFT),
            'category' => $this->category,
            'description' => $this->description,
            'payer_id' => $this->payer_id,
            'payer_name' => $this->payer_name,
            'amount' => $this->amount,
            'payment_date' => $this->payment_date,
            'payment_method' => $this->payment_method,
            'received_by' => auth()->id(),
            'remarks' => $this->remarks,
        ]);
        $this->redirect(route('revenue.index'), navigate: true);
    }
}; ?>

<div>
<div style="max-width:800px;margin:0 auto;">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <div>
            <h1 style="font-size:1.125rem;font-weight:800;color:var(--text-primary);margin:0;">Record Payment</h1>
            <p style="font-size:0.8125rem;color:var(--text-secondary);margin:2px 0 0;">Log a new payment transaction</p>
        </div>
        <a href="{{ route('revenue.index') }}" wire:navigate class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back
        </a>
    </div>

    <form wire:submit="save">

        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                Payer Information
            </div>
            <div>
                <label class="form-label">Link to Resident (Optional)</label>
                <input type="text" wire:model.live.debounce.300ms="searchTerm" placeholder="Type resident name to search..." class="form-input">
                @if($this->residents()->count())
                <div style="border:1px solid var(--border);border-radius:var(--radius);max-height:200px;overflow-y:auto;margin-top:4px;">
                    @foreach($this->residents() as $r)
                    <button type="button" wire:click="$set('payer_id', {{ $r->id }}); $set('payer_name', '{{ $r->fullName() }}'); $set('searchTerm', '')"
                        style="display:flex;justify-content:space-between;align-items:center;width:100%;text-align:left;padding:8px 12px;border:none;border-bottom:1px solid var(--border-light);background:{{ $payer_id == $r->id ? 'var(--blue-50)' : 'var(--surface)' }};cursor:pointer;font-family:inherit;font-size:0.8125rem;">
                        <div><strong>{{ $r->fullName() }}</strong> <span style="color:var(--text-muted);font-size:0.75rem;">{{ $r->purok ?? '' }}</span></div>
                        <span style="font-size:0.6875rem;color:var(--blue-600);font-weight:600;">Select</span>
                    </button>
                    @endforeach
                </div>
                @endif
            </div>
            <div class="form-group" style="margin-top:12px;">
                <label class="form-label">Payer Name *</label>
                <input type="text" wire:model="payer_name" class="form-input">
            </div>
        </div>

        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                Payment Details
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label class="form-label">Category *</label>
                    <select wire:model="category" class="form-select">
                        <option value="barangay_clearance">Barangay Clearance</option>
                        <option value="certificate">Certificate</option>
                        <option value="id_card">ID Card</option>
                        <option value="business_permit">Business Permit</option>
                        <option value="penalty">Penalty</option>
                        <option value="donation">Donation</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Amount (₱) *</label>
                    <input type="number" step="0.01" wire:model="amount" class="form-input">
                </div>
                <div>
                    <label class="form-label">Payment Date *</label>
                    <input type="date" wire:model="payment_date" class="form-input">
                </div>
                <div>
                    <label class="form-label">Payment Method</label>
                    <select wire:model="payment_method" class="form-select">
                        <option value="cash">Cash</option>
                        <option value="gcash">GCash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="check">Check</option>
                    </select>
                </div>
                <div style="grid-column:span 2;">
                    <label class="form-label">Description</label>
                    <input type="text" wire:model="description" class="form-input">
                </div>
                <div style="grid-column:span 2;">
                    <label class="form-label">Remarks</label>
                    <textarea wire:model="remarks" rows="2" class="form-textarea" placeholder="Additional notes..."></textarea>
                </div>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:8px;padding:16px 0 32px;">
            <a href="{{ route('revenue.index') }}" wire:navigate class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-success btn-lg">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                Record Payment
            </button>
        </div>

    </form>
</div>
</div>
