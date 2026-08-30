<?php

use Livewire\Volt\Component;
use App\Models\Announcement;

new #[Layout("layouts.app")] class extends Component
{
    public string $title = '';
    public string $content = '';
    public string $type = 'general';
    public string $priority = 'normal';
    public string $publish_date = '';
    public string $expiry_date = '';

    public function save(): void
    {
        $this->validate([
            'title' => 'required',
            'content' => 'required',
            'publish_date' => 'required|date',
        ]);
        Announcement::create([
            'title' => $this->title,
            'content' => $this->content,
            'type' => $this->type,
            'priority' => $this->priority,
            'publish_date' => $this->publish_date,
            'expiry_date' => $this->expiry_date ?: null,
            'is_active' => true,
            'author_id' => auth()->id(),
        ]);
        $this->redirect(route('announcements.index'), navigate: true);
    }
}; ?>

<div>
<div style="max-width:800px;margin:0 auto;">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <div>
            <h1 style="font-size:1.125rem;font-weight:800;color:var(--text-primary);margin:0;">New Announcement</h1>
            <p style="font-size:0.8125rem;color:var(--text-secondary);margin:2px 0 0;">Create a community announcement</p>
        </div>
        <a href="{{ route('announcements.index') }}" wire:navigate class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back
        </a>
    </div>

    <form wire:submit="save">

        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                Announcement Details
            </div>
            <div class="form-group">
                <label class="form-label">Title *</label>
                <input type="text" wire:model="title" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Content *</label>
                <textarea wire:model="content" rows="6" class="form-textarea" placeholder="Announcement details..."></textarea>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                <div>
                    <label class="form-label">Type</label>
                    <select wire:model="type" class="form-select">
                        <option value="general">General</option>
                        <option value="emergency">Emergency</option>
                        <option value="event">Event</option>
                        <option value="meeting">Meeting</option>
                        <option value="health">Health</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Priority</label>
                    <select wire:model="priority" class="form-select">
                        <option value="normal">Normal</option>
                        <option value="important">Important</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Publish Date *</label>
                    <input type="date" wire:model="publish_date" class="form-input">
                </div>
            </div>
            <div class="form-group" style="margin-top:12px;">
                <label class="form-label">Expiry Date</label>
                <input type="date" wire:model="expiry_date" class="form-input">
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:8px;padding:16px 0 32px;">
            <a href="{{ route('announcements.index') }}" wire:navigate class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary btn-lg">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                Post Announcement
            </button>
        </div>

    </form>
</div>
</div>
