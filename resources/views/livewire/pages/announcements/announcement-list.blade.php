<?php

use Livewire\Volt\Component;
use App\Models\Announcement;
use Livewire\WithPagination;

new #[Layout("layouts.app")] class extends Component
{
    use WithPagination;

    public string $filterType = '';

    // Edit state
    public int $editingId = 0;
    public string $editTitle = '';
    public string $editContent = '';
    public string $editType = 'general';
    public string $editPriority = 'normal';
    public bool $editIsActive = true;

    public function announcements()
    {
        return Announcement::with('author')
            ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
            ->latest()
            ->paginate(20);
    }

    public function startEdit(Announcement $ann): void
    {
        $this->editingId = $ann->id;
        $this->editTitle = $ann->title;
        $this->editContent = $ann->content;
        $this->editType = $ann->type;
        $this->editPriority = $ann->priority;
        $this->editIsActive = $ann->is_active;
    }

    public function saveEdit(Announcement $ann): void
    {
        $this->validate([
            'editTitle' => 'required',
            'editContent' => 'required',
        ]);
        $ann->update([
            'title' => $this->editTitle,
            'content' => $this->editContent,
            'type' => $this->editType,
            'priority' => $this->editPriority,
            'is_active' => $this->editIsActive,
        ]);
        $this->editingId = 0;
        session()->flash('success', 'Announcement updated!');
    }

    public function cancelEdit(): void
    {
        $this->editingId = 0;
    }

    public function deleteAnnouncement(Announcement $ann): void
    {
        $ann->delete();
        session()->flash('success', 'Announcement deleted.');
    }
}; ?>

<div>
<div style="max-width:1200px;margin:0 auto;">

    <div class="page-header">
        <div>
            <h1 class="page-title">Announcements</h1>
            <p class="page-subtitle">Community notices and public announcements</p>
        </div>
        <a href="{{ route('announcements.create') }}" wire:navigate class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Announcement
        </a>
    </div>

    <div class="card" style="margin-bottom:16px;max-width:250px;">
        <div class="card-body">
            <select wire:model.live="filterType" class="form-select">
                <option value="">All Types</option>
                <option value="general">General</option>
                <option value="emergency">Emergency</option>
                <option value="event">Event</option>
                <option value="meeting">Meeting</option>
                <option value="health">Health</option>
            </select>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:12px;">
        @forelse($this->announcements() as $ann)
        <div class="card" style="padding:20px;border-left:4px solid {{ $ann->priority === 'urgent' ? 'var(--red-600)' : ($ann->priority === 'important' ? 'var(--amber-600)' : 'var(--blue-600)') }};">
            @if($editingId === $ann->id)
                    {{-- Inline Edit Form --}}
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        <input type="text" wire:model="editTitle" class="form-input" placeholder="Title">
                        <textarea wire:model="editContent" rows="4" class="form-textarea" placeholder="Content"></textarea>
                        <div style="display:flex;gap:8px;">
                            <select wire:model="editType" class="form-select" style="flex:1;">
                                <option value="general">General</option>
                                <option value="emergency">Emergency</option>
                                <option value="event">Event</option>
                                <option value="meeting">Meeting</option>
                                <option value="health">Health</option>
                            </select>
                            <select wire:model="editPriority" class="form-select" style="flex:1;">
                                <option value="normal">Normal</option>
                                <option value="important">Important</option>
                                <option value="urgent">Urgent</option>
                            </select>
                            <label style="display:flex;align-items:center;gap:6px;font-size:0.8125rem;">
                                <input type="checkbox" wire:model="editIsActive" class="w-4 h-4"> Active
                            </label>
                        </div>
                        <div style="display:flex;gap:8px;justify-content:flex-end;">
                            <button wire:click="cancelEdit" class="btn btn-outline btn-sm">Cancel</button>
                            <button wire:click="saveEdit({{ $ann->id }})" class="btn btn-primary btn-sm">Save</button>
                        </div>
                    </div>
                    @else
                    {{-- View Mode --}}
                    <div style="display:flex;justify-content:space-between;align-items:start;">
                        <div>
                            <div style="display:flex;gap:6px;margin-bottom:8px;align-items:center;">
                                @if($ann->type === 'emergency')
                                    <span class="badge badge-red">{{ $ann->getTypeLabel() }}</span>
                                @elseif($ann->type === 'event')
                                    <span class="badge badge-green">{{ $ann->getTypeLabel() }}</span>
                                @else
                                    <span class="badge badge-gray">{{ $ann->getTypeLabel() }}</span>
                                @endif
                                @if($ann->priority === 'urgent')
                                    <span class="badge badge-red" style="font-weight:700;">URGENT</span>
                                @endif
                                @if(auth()->user()->hasPrivilege(4))
                                <button wire:click="startEdit({{ $ann->id }})" class="btn-table btn-table-edit" style="font-size:0.6875rem;">Edit</button>
                                <button wire:click="deleteAnnouncement({{ $ann->id }})" wire:confirm="Delete this announcement?" class="btn-table btn-table-danger" style="font-size:0.6875rem;">Delete</button>
                                @endif
                            </div>
                            <h3 style="font-size:0.9375rem;font-weight:700;color:var(--text-primary);margin:0 0 4px;">{{ $ann->title }}</h3>
                            <p style="font-size:0.8125rem;color:var(--text-secondary);margin:0;line-height:1.5;">{{ \Illuminate\Support\Str::limit($ann->content, 200) }}</p>
                            <div style="font-size:0.75rem;color:var(--text-muted);margin-top:8px;">Posted {{ $ann->publish_date->format('M d, Y') }} by {{ $ann->author->name ?? 'Staff' }}</div>
                        </div>
                        <span class="badge {{ $ann->is_active ? 'badge-green' : 'badge-gray' }}">{{ $ann->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                    @endif
        </div>
        @empty
        <div class="card" style="padding:48px;text-align:center;color:var(--text-muted);font-size:0.8125rem;">No announcements found.</div>
        @endforelse
    </div>

    <div style="margin-top:16px;">{{ $this->announcements()->links() }}</div>

</div>
</div>
