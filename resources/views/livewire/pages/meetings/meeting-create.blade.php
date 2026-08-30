<?php

use Livewire\Volt\Component;
use App\Models\MeetingMinute;

new #[Layout("layouts.app")] class extends Component
{
    public string $type = 'regular';
    public string $meeting_date = '';
    public string $start_time = '';
    public string $end_time = '';
    public string $venue = '';
    public string $agenda = '';
    public string $minutes_content = '';
    public string $resolutions = '';
    public string $attendees = '';

    public function save(): void
    {
        $this->validate([
            'meeting_date' => 'required|date',
            'agenda' => 'required',
            'minutes_content' => 'required',
        ]);
        MeetingMinute::create([
            'meeting_number' => MeetingMinute::generateNumber(),
            'type' => $this->type,
            'meeting_date' => $this->meeting_date,
            'start_time' => $this->start_time ?: null,
            'end_time' => $this->end_time ?: null,
            'venue' => $this->venue,
            'agenda' => $this->agenda,
            'minutes_content' => $this->minutes_content,
            'resolutions' => $this->resolutions,
            'attendees' => $this->attendees,
            'recorded_by' => auth()->id(),
        ]);
        $this->redirect(route('meetings.index'), navigate: true);
    }
}; ?>

<div>
<div style="max-width:800px;margin:0 auto;">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <div>
            <h1 style="font-size:1.125rem;font-weight:800;color:var(--text-primary);margin:0;">New Meeting Minutes</h1>
            <p style="font-size:0.8125rem;color:var(--text-secondary);margin:2px 0 0;">Record meeting details and resolutions</p>
        </div>
        <a href="{{ route('meetings.index') }}" wire:navigate class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back
        </a>
    </div>

    <form wire:submit="save">

        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/></svg>
                Meeting Details
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                <div>
                    <label class="form-label">Meeting Type *</label>
                    <select wire:model="type" class="form-select">
                        <option value="regular">Regular Session</option>
                        <option value="special">Special Session</option>
                        <option value="committee">Committee</option>
                        <option value="emergency">Emergency</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Date *</label>
                    <input type="date" wire:model="meeting_date" class="form-input">
                </div>
                <div>
                    <label class="form-label">Venue</label>
                    <input type="text" wire:model="venue" class="form-input">
                </div>
                <div>
                    <label class="form-label">Start Time</label>
                    <input type="time" wire:model="start_time" class="form-input">
                </div>
                <div>
                    <label class="form-label">End Time</label>
                    <input type="time" wire:model="end_time" class="form-input">
                </div>
            </div>
        </div>

        <div class="section-card">
            <div class="section-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Agenda & Minutes
            </div>
            <div class="form-group">
                <label class="form-label">Agenda *</label>
                <textarea wire:model="agenda" rows="3" class="form-textarea" placeholder="Meeting agenda items..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Minutes Content *</label>
                <textarea wire:model="minutes_content" rows="6" class="form-textarea" placeholder="Detailed minutes..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Resolutions</label>
                <textarea wire:model="resolutions" rows="3" class="form-textarea" placeholder="Resolutions passed..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Attendees</label>
                <textarea wire:model="attendees" rows="2" class="form-textarea" placeholder="List of attendees..."></textarea>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:8px;padding:16px 0 32px;">
            <a href="{{ route('meetings.index') }}" wire:navigate class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary btn-lg">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Save Minutes
            </button>
        </div>

    </form>
</div>
</div>
