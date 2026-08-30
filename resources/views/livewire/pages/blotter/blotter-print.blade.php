<?php

use Livewire\Volt\Component;
use App\Models\BlotterRecord;

new #[Layout("layouts.app")] class extends Component
{
    public BlotterRecord $blotter;

    public function mount(BlotterRecord $blotter): void
    {
        $this->blotter = $blotter->load(['parties', 'recorder']);
    }
}; ?>

<div>
<div class="max-w-4xl mx-auto">
<div class="card" style="overflow:hidden;">
<div class="bg-white p-10" style="font-family: 'Times New Roman', serif;">
<div class="text-center mb-6 border-b-2 border-gray-800 pb-4">
<div class="text-3xl font-bold uppercase">Republic of the Philippines</div>
<div class="text-lg mt-1">{{ \App\Models\BarangaySetting::first()->municipality ?? 'Municipality' }}</div>
<div class="mt-3 text-xl font-extrabold uppercase">Barangay {{ \App\Models\BarangaySetting::first()->barangay_name ?? '_____' }}</div>
</div>
<div class="text-center my-6">
<h1 class="text-2xl font-extrabold uppercase underline">BLOTTER CERTIFICATION</h1>
<div class="text-sm text-gray-500 mt-1">{{ $this->blotter->blotter_number }}</div>
</div>
<div class="text-justify leading-relaxed space-y-4 text-lg">
<p>TO WHOM IT MAY CONCERN:</p>
<p>This is to certify that an incident of <strong>{{ str_replace('_', ' ', $this->blotter->incident_type) }}</strong> was recorded at the Barangay Hall on <strong>{{ $this->blotter->incident_datetime->format('F d, Y') }}</strong>, involving the following parties:</p>
@forelse($this->blotter->parties as $p)
<p><strong>{{ ucfirst($p->role) }}:</strong> {{ $p->name }}</p>
@empty
<p>Parties: Not recorded</p>
@endforelse
<p><strong>Location:</strong> {{ $this->blotter->location }}</p>
<p><strong>Narrative:</strong> {{ $this->blotter->narrative }}</p>
<p><strong>Status:</strong> {{ $this->blotter->getStatusLabel() }}</p>
</div>
<div class="flex justify-between items-end mt-12 pt-6">
<div class="text-center">
<div class="border-t-2 border-gray-800 w-48 mb-1"></div>
<div class="text-sm font-bold">Recorded by:</div>
<div class="text-sm">{{ $this->blotter->recorder->name ?? 'Staff' }}</div>
</div>
<div class="text-center">
<div class="border-t-2 border-gray-800 w-48 mb-1"></div>
<div class="text-sm font-bold">Approved by:</div>
<div class="text-sm">HON. {{ strtoupper(\App\Models\Official::current()->where('position', 'Barangay Captain')->first()->name ?? '_______________') }}</div>
<div class="text-xs text-gray-500">Punong Barangay</div>
</div>
</div>
</div>
</div>
</div>
</div>

