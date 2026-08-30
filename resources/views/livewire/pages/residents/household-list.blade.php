<?php

use Livewire\Volt\Component;
use App\Models\Household;
use App\Models\Purok;

new #[Layout("layouts.app")] class extends Component
{
    public string $search = '';
    public string $house_number = '';
    public string $street = '';
    public string $purok = '';
    public string $full_address = '';
    public int $editingId = 0;
    public string $edit_house_number = '';
    public string $edit_street = '';
    public string $edit_purok = '';
    public string $edit_full_address = '';

    // Purok management
    public string $newPurokName = '';
    public int $editingPurokId = 0;
    public string $editingPurokName = '';
    public bool $showPurokManager = false;

    public function togglePurokManager(): void
    {
        $this->showPurokManager = !$this->showPurokManager;
    }

    public function households()
    {
        return Household::withCount('residents')
            ->when($this->search, fn($q) => $q->where('full_address', 'like', "%{$this->search}%")
                ->orWhere('house_number', 'like', "%{$this->search}%")
                ->orWhere('street', 'like', "%{$this->search}%"))
            ->orderBy('full_address')
            ->paginate(15);
    }

    public function addHousehold(): void
    {
        $this->validate([
            'full_address' => 'required|string|max:255',
        ]);

        $addr = $this->full_address;
        if (!$addr && $this->house_number && $this->street) {
            $addr = trim("{$this->house_number} {$this->street}");
        }

        Household::create([
            'house_number' => $this->house_number ?: null,
            'street' => $this->street ?: null,
            'purok' => $this->purok ?: null,
            'full_address' => $addr,
        ]);

        $this->house_number = '';
        $this->street = '';
        $this->purok = '';
        $this->full_address = '';
        session()->flash('success', 'Household added!');
    }

    public function startEdit(Household $hh): void
    {
        $this->editingId = $hh->id;
        $this->edit_house_number = $hh->house_number ?? '';
        $this->edit_street = $hh->street ?? '';
        $this->edit_purok = $hh->purok ?? '';
        $this->edit_full_address = $hh->full_address ?? '';
    }

    public function saveEdit(): void
    {
        $this->validate(['edit_full_address' => 'required|string|max:255']);
        Household::find($this->editingId)->update([
            'house_number' => $this->edit_house_number ?: null,
            'street' => $this->edit_street ?: null,
            'purok' => $this->edit_purok ?: null,
            'full_address' => $this->edit_full_address,
        ]);
        $this->editingId = 0;
        session()->flash('success', 'Household updated!');
    }

    public function cancelEdit(): void
    {
        $this->editingId = 0;
    }

    public function deleteHousehold(Household $hh): void
    {
        $hh->delete();
        session()->flash('success', 'Household deleted.');
    }

    // ---- Purok Management ----
    public function addPurok(): void
    {
        $this->validate(['newPurokName' => 'required|string|max:255']);
        if (Purok::where('name', $this->newPurokName)->exists()) {
            session()->flash('error', 'Purok already exists.');
            return;
        }
        $maxOrder = Purok::max('sort_order') ?? 0;
        Purok::create(['name' => $this->newPurokName, 'is_active' => true, 'sort_order' => $maxOrder + 1]);
        $this->newPurokName = '';
        session()->flash('success', 'Purok added!');
    }

    public function startEditPurok(Purok $purok): void
    {
        $this->editingPurokId = $purok->id;
        $this->editingPurokName = $purok->name;
    }

    public function saveEditPurok(): void
    {
        $this->validate(['editingPurokName' => 'required|string|max:255']);
        Purok::find($this->editingPurokId)->update(['name' => $this->editingPurokName]);
        $this->editingPurokId = 0;
        $this->editingPurokName = '';
        session()->flash('success', 'Purok updated!');
    }

    public function cancelEditPurok(): void
    {
        $this->editingPurokId = 0;
        $this->editingPurokName = '';
    }

    public function togglePurok(Purok $purok): void
    {
        $purok->update(['is_active' => !$purok->is_active]);
    }

    public function deletePurok(Purok $purok): void
    {
        $purok->delete();
        session()->flash('success', 'Purok deleted.');
    }
}; ?>

<div>
<div style="max-width:1000px;margin:0 auto;">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <div>
            <h1 style="font-size:1.125rem;font-weight:800;color:var(--text-primary);margin:0;">Households</h1>
            <p style="font-size:0.8125rem;color:var(--text-secondary);margin:2px 0 0;">Manage household addresses for residents. Household is optional. Skip if not applicable in your area.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success" style="margin-bottom:16px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 380px;gap:20px;">

    {{-- Left: List --}}
    <div>
        <div class="section-card">
            <div style="margin-bottom:12px;">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by address..." class="form-input">
            </div>

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Full Address</th>
                            <th>Purok</th>
                            <th>Residents</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->households() as $idx => $hh)
                        <tr>
                            <td style="color:var(--text-muted);">{{ ($this->households()->currentPage() - 1) * 15 + $idx + 1 }}</td>
                            <td>
                                @if($editingId === $hh->id)
                                    <input type="text" wire:model="edit_full_address" class="form-input" style="padding:4px 10px;font-size:0.8125rem;">
                                @else
                                    <span style="font-weight:600;">{{ $hh->full_address }}</span>
                                @endif
                            </td>
                            <td>
                                @if($editingId === $hh->id)
                                    <input type="text" wire:model="edit_purok" class="form-input" style="padding:4px 10px;font-size:0.8125rem;width:100px;">
                                @else
                                    {{ $hh->purok ?? 'N/A' }}
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-blue">{{ $hh->residents_count }}</span>
                            </td>
                            <td>
                                @if($editingId === $hh->id)
                                    <div style="display:flex;gap:4px;">
                                        <button type="button" wire:click="saveEdit" class="btn btn-sm btn-primary" style="padding:4px 10px;font-size:0.75rem;">Save</button>
                                        <button type="button" wire:click="cancelEdit" class="btn btn-sm btn-outline" style="padding:4px 10px;font-size:0.75rem;">Cancel</button>
                                    </div>
                                @else
                                    <div style="display:flex;gap:4px;">
                                        <button type="button" wire:click="startEdit({{ $hh->id }})" class="btn-table btn-table-edit">Edit</button>
                                        <button type="button" wire:click="deleteHousehold({{ $hh->id }})" onclick="return confirm('Delete this household?')" class="btn-table btn-table-danger">Delete</button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" style="text-align:center;padding:32px;color:var(--text-muted);font-size:0.8125rem;">No households found. Add one on the right.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="padding:12px 16px;">{{ $this->households()->links() }}</div>
        </div>
    </div>

    {{-- Right: Add Form --}}
    <div>
        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Add Household
            </div>
            <form wire:submit="addHousehold">
                <div class="form-group">
                    <label class="form-label">Full Address *</label>
                    <input type="text" wire:model="full_address" placeholder="e.g. 123 Rizal St., Purok 1" class="form-input">
                    @error('full_address') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <div class="form-group">
                        <label class="form-label">House #</label>
                        <input type="text" wire:model="house_number" placeholder="Optional" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Purok</label>
                        <select wire:model="purok" class="form-select">
                            <option value="">Select...</option>
                            @foreach(Purok::active()->get() as $pk)
                            <option value="{{ $pk->name }}">{{ $pk->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Street</label>
                    <input type="text" wire:model="street" placeholder="Optional" class="form-input">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add Household
                </button>
            </form>
        </div>

        <div style="margin-top:16px;padding:16px;background:var(--blue-50);border:1px solid var(--blue-100);border-radius:var(--radius-lg);">
            <div style="font-size:0.8125rem;font-weight:600;color:var(--blue-700);margin-bottom:4px;">💡 Tip</div>
            <div style="font-size:0.75rem;color:var(--blue-600);line-height:1.5;">Household is <strong>optional</strong>. In rural barangays where household numbers aren't used, you can skip this entirely. Just fill in the Purok and Street Address directly on the resident form.</div>
        </div>
    </div>

    </div>

    @if(session('error'))
    <div class="alert alert-danger" style="margin-bottom:16px;">
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Purok Management Toggle --}}
    <div class="section-card" style="margin-top:20px;">
        <div wire:click="togglePurokManager" style="display:flex;justify-content:space-between;align-items:center;cursor:pointer;padding:4px 0;">
            <div style="display:flex;align-items:center;gap:10px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--text-secondary)" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span style="font-weight:700;color:var(--text-primary);">Purok / Zone / Sitio Management</span>
                <span style="font-size:0.75rem;color:var(--text-tertiary);">({{ Purok::count() }} puroks)</span>
            </div>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--text-tertiary)" stroke-width="2" style="transition:transform 0.2s;"            :style="$wire.showPurokManager && 'transform:rotate(180deg)'"><polyline points="6 9 12 15 18 9"/></svg>
        </div>

        @if($showPurokManager)
        <div style="border-top:1px solid var(--border);padding-top:16px;margin-top:12px;">
            <p style="font-size:0.8125rem;color:var(--text-secondary);margin-bottom:16px;">Add, edit, or remove puroks. Inactive puroks won't appear in dropdowns.</p>

            <form wire:submit="addPurok" style="display:flex;gap:8px;margin-bottom:20px;">
                <input type="text" wire:model="newPurokName" class="form-input" placeholder="e.g. Purok 11, Sitio Maligaya" style="flex:1;">
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add Purok
                </button>
            </form>

            @error('newPurokName') <span class="form-error" style="margin-top:-12px;margin-bottom:12px;display:block;">{{ $message }}</span> @enderror

            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>Purok Name</th>
                            <th style="width:100px;">Status</th>
                            <th style="width:160px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $puroks = \App\Models\Purok::orderBy('sort_order')->get(); @endphp
                        @forelse($puroks as $index => $purok)
                        <tr style="{{ !$purok->is_active ? 'opacity:0.5;' : '' }}">
                            <td style="color:var(--text-tertiary);">{{ $index + 1 }}</td>
                            <td>
                                @if($editingPurokId === $purok->id)
                                    <input type="text" wire:model="editingPurokName" class="form-input" style="padding:4px 10px;font-size:0.875rem;max-width:300px;">
                                @else
                                    <span style="font-weight:600;color:var(--text-primary);">{{ $purok->name }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $purok->is_active ? 'badge-green' : 'badge-gray' }}">{{ $purok->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td>
                                @if($editingPurokId === $purok->id)
                                    <div style="display:flex;gap:4px;">
                                        <button type="button" wire:click="saveEditPurok" class="btn btn-sm btn-primary" style="padding:4px 10px;font-size:0.75rem;">Save</button>
                                        <button type="button" wire:click="cancelEditPurok" class="btn btn-sm btn-outline" style="padding:4px 10px;font-size:0.75rem;">Cancel</button>
                                    </div>
                                @else
                                    <div style="display:flex;gap:4px;">
                                        <button type="button" wire:click="startEditPurok({{ $purok->id }})" class="btn-table-edit" style="font-size:0.75rem;">Edit</button>
                                        <button type="button" wire:click="togglePurok({{ $purok->id }})" class="btn-table-edit" style="font-size:0.75rem;color:{{ $purok->is_active ? 'var(--amber-600)' : 'var(--green-600)' }};">
                                            {{ $purok->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                        <button type="button" wire:click="deletePurok({{ $purok->id }})" onclick="return confirm('Delete this purok?')" class="btn-table-danger" style="font-size:0.75rem;">Delete</button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" style="text-align:center;padding:24px;color:var(--text-tertiary);">No puroks yet. Add one above.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

</div>
</div>
