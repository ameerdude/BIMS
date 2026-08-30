<?php

use Livewire\Volt\Component;
use App\Models\BarangaySetting;
use App\Models\DocumentTemplate;
use Livewire\WithFileUploads;

new #[Layout("layouts.app")] class extends Component
{
    use WithFileUploads;

    // Tab
    public string $activeTab = 'barangay';

    // Barangay Info
    public string $barangay_name = '';
    public string $municipality = '';
    public string $province = '';
    public string $region = '';
    public string $contact_number = '';
    public string $email = '';
    public string $header_text = '';
    public $logo;
    public $seal;



    // Template editor state
    public int $expandedTemplateId = 0;
    public string $editHeader1 = '';
    public string $editHeader2 = '';
    public string $editHeader3 = '';
    public string $editHeader4 = '';
    public bool $editShowLogo = true;
    public bool $editShowSeal = true;
    public array $editBodyParagraphs = [];
    public string $editFooterText = '';
    public string $editPreparedByTitle = 'Barangay Staff';
    public string $editApprovedByTitle = 'Punong Barangay';
    public bool $editShowQr = true;
    public bool $editShowControl = true;
    public string $editFee = '';
    public bool $editActive = true;
    public string $editOrientation = 'portrait';
    public string $editPaperSize = 'letter';

    public function mount(): void
    {
        $settings = BarangaySetting::firstOrCreateDefault();
        $this->barangay_name = $settings->barangay_name;
        $this->municipality = $settings->municipality;
        $this->province = $settings->province;
        $this->region = $settings->region ?? '';
        $this->contact_number = $settings->contact_number ?? '';
        $this->email = $settings->email ?? '';
        $this->header_text = $settings->header_text ?? '';

    }

    // ---- Barangay Info ----
    public function saveBarangayInfo(): void
    {
        $this->validate(['barangay_name' => 'required', 'municipality' => 'required', 'province' => 'required']);
        $settings = BarangaySetting::firstOrCreateDefault();
        $data = [
            'barangay_name' => $this->barangay_name,
            'municipality' => $this->municipality,
            'province' => $this->province,
            'region' => $this->region,
            'contact_number' => $this->contact_number,
            'email' => $this->email,
            'header_text' => $this->header_text,

        ];
        if ($this->logo) $data['logo_path'] = $this->logo->store('settings', 'public');
        if ($this->seal) $data['seal_path'] = $this->seal->store('settings', 'public');
        $settings->update($data);
        session()->flash('success', 'Barangay information saved!');
    }



    // ---- Document Template Editor ----
    // Validity editor
    public int $editValidityValue = 6;
    public string $editValidityUnit = 'months';

    public function expandTemplate(DocumentTemplate $template): void
    {
        if ($this->expandedTemplateId === $template->id) {
            $this->expandedTemplateId = 0;
            return;
        }
        $this->expandedTemplateId = $template->id;
        $this->editHeader1 = $template->header_line_1 ?? 'Republic of the Philippines';
        $this->editHeader2 = $template->header_line_2 ?? '@{{municipality}}';
        $this->editHeader3 = $template->header_line_3 ?? '@{{province}}';
        $this->editHeader4 = $template->header_line_4 ?? 'Barangay @{{barangay}}';
        $this->editShowLogo = $template->show_logo;
        $this->editShowSeal = $template->show_seal;
        $this->editBodyParagraphs = $template->body_paragraphs ?? [['text' => '']];
        $this->editFooterText = $template->footer_text ?? '';
        $this->editPreparedByTitle = $template->prepared_by_title ?? 'Barangay Staff';
        $this->editApprovedByTitle = $template->approved_by_title ?? 'Punong Barangay';
        $this->editShowQr = $template->show_qr_code;
        $this->editShowControl = $template->show_control_number;
        $this->editFee = $template->fee ?? '';
        $this->editActive = $template->is_active;
        $this->editOrientation = $template->orientation ?? 'portrait';
        $this->editPaperSize = $template->paper_size ?? 'letter';
        $this->editValidityValue = $template->validity_value ?? 6;
        $this->editValidityUnit = $template->validity_unit ?? 'months';
    }

    public function addParagraph(): void
    {
        $this->editBodyParagraphs[] = ['text' => ''];
    }

    public function removeParagraph(int $index): void
    {
        if (count($this->editBodyParagraphs) > 1) {
            array_splice($this->editBodyParagraphs, $index, 1);
        }
    }

    public function saveTemplate(DocumentTemplate $template): void
    {
        $template->update([
            'header_line_1' => $this->editHeader1,
            'header_line_2' => $this->editHeader2,
            'header_line_3' => $this->editHeader3,
            'header_line_4' => $this->editHeader4,
            'show_logo' => $this->editShowLogo,
            'show_seal' => $this->editShowSeal,
            'body_paragraphs' => $this->editBodyParagraphs,
            'footer_text' => $this->editFooterText,
            'prepared_by_title' => $this->editPreparedByTitle,
            'approved_by_title' => $this->editApprovedByTitle,
            'show_qr_code' => $this->editShowQr,
            'show_control_number' => $this->editShowControl,
            'fee' => $this->editFee,
            'is_active' => $this->editActive,
            'orientation' => $this->editOrientation,
            'paper_size' => $this->editPaperSize,
            'validity_value' => $this->editValidityValue,
            'validity_unit' => $this->editValidityUnit,
        ]);
        $this->expandedTemplateId = 0;
        session()->flash('success', "Template \"{$template->label}\" saved!");
    }

    public function cancelTemplate(): void
    {
        $this->expandedTemplateId = 0;
    }
}; ?>

<div>
<div style="max-width:900px;margin:0 auto;">

    {{-- Header --}}
    <div style="margin-bottom:20px;">
        <h1 style="font-size:1.25rem;font-weight:800;color:var(--text-primary);margin:0;">Settings</h1>
        <p style="font-size:0.8125rem;color:var(--text-secondary);margin:2px 0 0;">Manage barangay configuration, document templates, and puroks</p>
    </div>

    @if(session('success'))
    <div class="alert alert-success" style="margin-bottom:16px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger" style="margin-bottom:16px;">
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Tabs --}}
    <div class="section-tabs" style="margin-bottom:20px;">
        <button type="button" @click="$wire.set('activeTab','barangay')" :class="$wire.activeTab==='barangay' && 'active'" class="section-tab">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            Barangay Info
        </button>
        <button type="button" @click="$wire.set('activeTab','documents')" :class="$wire.activeTab==='documents' && 'active'" class="section-tab">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Document Templates
        </button>

    </div>

    {{-- ================== BARANGAY INFO ================== --}}
    <div x-show="$wire.activeTab==='barangay'" x-cloak>
        <form wire:submit="saveBarangayInfo">
            <div class="section-card">
                <div class="section-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Barangay Information
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Barangay Name *</label>
                        <input type="text" wire:model="barangay_name" class="form-input">
                        @error('barangay_name') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Municipality/City *</label>
                        <input type="text" wire:model="municipality" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Province *</label>
                        <input type="text" wire:model="province" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Region</label>
                        <input type="text" wire:model="region" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Contact Number</label>
                        <input type="text" wire:model="contact_number" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" wire:model="email" class="form-input">
                    </div>
                </div>
                <div class="form-group" style="margin-top:12px;">
                    <label class="form-label">Header Text (shown on documents)</label>
                    <textarea wire:model="header_text" rows="2" class="form-textarea" placeholder="e.g., Republic of the Philippines..."></textarea>
                </div>
            </div>

            <div class="section-card">
                <div class="section-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    Logo & Seal
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Barangay Logo</label>
                        <input type="file" wire:model="logo" accept="image/*" class="form-input" style="padding:8px;">
                        @if($logo)
                        <div style="margin-top:8px;"><img src="{{ $logo->temporaryUrl() }}" style="width:96px;height:96px;object-fit:contain;border-radius:var(--radius);border:1px solid var(--border);padding:4px;"></div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label class="form-label">Official Seal</label>
                        <input type="file" wire:model="seal" accept="image/*" class="form-input" style="padding:8px;">
                        @if($seal)
                        <div style="margin-top:8px;"><img src="{{ $seal->temporaryUrl() }}" style="width:96px;height:96px;object-fit:contain;border-radius:var(--radius);border:1px solid var(--border);padding:4px;"></div>
                        @endif
                    </div>
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;padding:8px 0 32px;">
                <button type="submit" class="btn btn-primary btn-lg">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
                    Save Barangay Info
                </button>
            </div>
        </form>
    </div>

    {{-- ================== DOCUMENT TEMPLATES ================== --}}
    <div x-show="$wire.activeTab==='documents'" x-cloak>
        <div style="margin-bottom:12px;">
            <p style="font-size:0.8125rem;color:var(--text-secondary);">Click a document type to edit its full template: header, body text, logo/seal placement, signatures, and more.</p>
        </div>

        @php $templates = \App\Models\DocumentTemplate::orderBy('sort_order')->get(); @endphp
        @foreach($templates as $tpl)
        <div class="section-card" style="margin-bottom:12px;{{ !$tpl->is_active ? 'opacity:0.6;' : '' }}" x-data="{ expanded: {{ $expandedTemplateId === $tpl->id ? 'true' : 'false' }} }">

            {{-- Template Header (clickable) --}}
            <div wire:click="expandTemplate({{ $tpl->id }})" style="display:flex;justify-content:space-between;align-items:center;cursor:pointer;padding:4px 0;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div>
                        <div style="font-weight:700;color:var(--text-primary);font-size:0.9375rem;">{{ $tpl->label }}</div>
                        <div style="font-size:0.75rem;color:var(--text-tertiary);font-family:monospace;">{{ $tpl->slug }} · {{ $tpl->fee ?? 'No fee' }} · Valid: {{ $tpl->validity_value ?? 6 }} {{ str($tpl->validity_unit ?? 'months')->plural($tpl->validity_value ?? 6) }}</div>
                    </div>
                    <span class="badge {{ $tpl->is_active ? 'badge-green' : 'badge-gray' }}">{{ $tpl->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--text-tertiary)" stroke-width="2" style="transition:transform 0.2s;" :style="expanded && 'transform:rotate(180deg)'"><polyline points="6 9 12 15 18 9"/></svg>
            </div>

            {{-- Template Editor (expandable) --}}
            @if($expandedTemplateId === $tpl->id)
            <div style="border-top:1px solid var(--border);padding-top:16px;margin-top:12px;" x-transition>

                {{-- Toggle switches row --}}
                <div style="display:flex;flex-wrap:wrap;gap:16px;margin-bottom:16px;padding:12px;background:var(--gray-50);border-radius:var(--radius);">
                    <label class="form-check" style="margin:0;">
                        <input type="checkbox" wire:model="editActive" class="w-4 h-4">
                        <span style="font-size:0.8125rem;font-weight:600;">Active</span>
                    </label>
                    <label class="form-check" style="margin:0;">
                        <input type="checkbox" wire:model="editShowLogo" class="w-4 h-4">
                        <span style="font-size:0.8125rem;">Show Logo</span>
                    </label>
                    <label class="form-check" style="margin:0;">
                        <input type="checkbox" wire:model="editShowSeal" class="w-4 h-4">
                        <span style="font-size:0.8125rem;">Show Seal</span>
                    </label>
                    <label class="form-check" style="margin:0;">
                        <input type="checkbox" wire:model="editShowQr" class="w-4 h-4">
                        <span style="font-size:0.8125rem;">QR Code</span>
                    </label>
                    <label class="form-check" style="margin:0;">
                        <input type="checkbox" wire:model="editShowControl" class="w-4 h-4">
                        <span style="font-size:0.8125rem;">Control No.</span>
                    </label>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <label style="font-size:0.8125rem;font-weight:600;color:var(--text-secondary);">Fee:</label>
                        <input type="text" wire:model="editFee" class="form-input" style="width:100px;padding:4px 8px;font-size:0.8125rem;" placeholder="₱50.00">
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <label style="font-size:0.8125rem;font-weight:600;color:var(--text-secondary);">Orientation:</label>
                        <select wire:model="editOrientation" class="form-select" style="width:110px;padding:4px 8px;font-size:0.8125rem;">
                            <option value="portrait">Portrait</option>
                            <option value="landscape">Landscape</option>
                        </select>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <label style="font-size:0.8125rem;font-weight:600;color:var(--text-secondary);">Paper Size:</label>
                        <select wire:model="editPaperSize" class="form-select" style="width:100px;padding:4px 8px;font-size:0.8125rem;">
                            <option value="letter">Letter</option>
                            <option value="legal">Legal</option>
                            <option value="a4">A4</option>
                        </select>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <label style="font-size:0.8125rem;font-weight:600;color:var(--text-secondary);">Valid for:</label>
                        <input type="number" wire:model="editValidityValue" min="1" max="999" class="form-input" style="width:60px;padding:4px 8px;font-size:0.8125rem;">
                        <select wire:model="editValidityUnit" class="form-select" style="width:90px;padding:4px 8px;font-size:0.8125rem;">
                            <option value="days">Day(s)</option>
                            <option value="months">Month(s)</option>
                            <option value="years">Year(s)</option>
                        </select>
                    </div>
                </div>

                {{-- Header Lines --}}
                <div style="margin-bottom:16px;">
                    <div style="font-weight:700;font-size:0.8125rem;color:var(--text-primary);margin-bottom:8px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;"><path d="M4 7V4h16v3M9 20h6M12 4v16"/></svg>
                        Document Header
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                        <div class="form-group" style="margin:0;">
                            <label style="font-size:0.75rem;color:var(--text-tertiary);">Line 1 (Country)</label>
                            <input type="text" wire:model="editHeader1" class="form-input" style="padding:6px 10px;font-size:0.8125rem;">
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label style="font-size:0.75rem;color:var(--text-tertiary);">Line 2 (Municipality)</label>
                            <input type="text" wire:model="editHeader2" class="form-input" style="padding:6px 10px;font-size:0.8125rem;">
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label style="font-size:0.75rem;color:var(--text-tertiary);">Line 3 (Province)</label>
                            <input type="text" wire:model="editHeader3" class="form-input" style="padding:6px 10px;font-size:0.8125rem;">
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label style="font-size:0.75rem;color:var(--text-tertiary);">Line 4 (Barangay)</label>
                            <input type="text" wire:model="editHeader4" class="form-input" style="padding:6px 10px;font-size:0.8125rem;">
                        </div>
                    </div>
                </div>

                {{-- Body Paragraphs --}}
                <div style="margin-bottom:16px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                        <div style="font-weight:700;font-size:0.8125rem;color:var(--text-primary);">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                            Body Content (Paragraphs)
                        </div>
                        <button type="button" wire:click="addParagraph" class="btn btn-sm btn-outline" style="padding:3px 10px;font-size:0.75rem;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Add Paragraph
                        </button>
                    </div>
                    <p style="font-size:0.75rem;color:var(--text-tertiary);margin-bottom:8px;">💡 <strong>To bold text:</strong> wrap with **double asterisks** like **this becomes bold**. Placeholders are auto-bolded. Available: @{{full_name}} @{{purok}} @{{barangay}} @{{municipality}} @{{province}} @{{purpose}} @{{sex}} @{{civil_status}} @{{date_issued}} @{{date_today}} @{{control_number}} @{{prepared_by}} @{{approved_by}}</p>

                    @foreach($editBodyParagraphs as $idx => $para)
                    <div style="display:flex;gap:6px;margin-bottom:6px;align-items:flex-start;">
                        <span style="min-width:24px;height:28px;display:flex;align-items:center;justify-content:center;font-size:0.6875rem;color:var(--text-tertiary);background:var(--gray-100);border-radius:var(--radius);margin-top:4px;">{{ $idx + 1 }}</span>
                        <textarea
                            wire:model="editBodyParagraphs.{{ $idx }}.text"
                            rows="2"
                            class="form-textarea"
                            style="flex:1;padding:6px 10px;font-size:0.8125rem;min-height:48px;"
                            placeholder="Type your paragraph here..."
                        ></textarea>
                        @if(count($editBodyParagraphs) > 1)
                        <button type="button" wire:click="removeParagraph({{ $idx }})" style="min-width:28px;height:28px;display:flex;align-items:center;justify-content:center;border:none;background:var(--red-50);color:var(--red-600);border-radius:var(--radius);cursor:pointer;margin-top:4px;" title="Remove paragraph">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                        @endif
                    </div>
                    @endforeach
                </div>

                {{-- Footer & Signatures --}}
                <div style="margin-bottom:16px;">
                    <div style="font-weight:700;font-size:0.8125rem;color:var(--text-primary);margin-bottom:8px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;"><path d="M17 3a2.828 2.828 0 114 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                        Footer & Signatures
                    </div>
                    <div class="grid-2">
                        <div class="form-group" style="margin:0;">
                            <label style="font-size:0.75rem;color:var(--text-tertiary);">Prepared By Title</label>
                            <input type="text" wire:model="editPreparedByTitle" class="form-input" style="padding:6px 10px;font-size:0.8125rem;">
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label style="font-size:0.75rem;color:var(--text-tertiary);">Approved By Title</label>
                            <input type="text" wire:model="editApprovedByTitle" class="form-input" style="padding:6px 10px;font-size:0.8125rem;">
                        </div>
                    </div>
                    <div class="form-group" style="margin-top:8px;">
                        <label style="font-size:0.75rem;color:var(--text-tertiary);">Additional Footer Text (optional)</label>
                        <textarea wire:model="editFooterText" rows="2" class="form-textarea" style="padding:6px 10px;font-size:0.8125rem;" placeholder="e.g., This document is valid for 6 months from date of issue."></textarea>
                    </div>
                </div>

                {{-- Save/Cancel --}}
                <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:12px;border-top:1px solid var(--border);">
                    <button type="button" wire:click="cancelTemplate" class="btn btn-outline" style="padding:6px 16px;font-size:0.8125rem;">Cancel</button>
                    <button type="button" wire:click="saveTemplate({{ $tpl->id }})" class="btn btn-primary" style="padding:6px 16px;font-size:0.8125rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
                        Save Template
                    </button>
                </div>

            </div>
            @endif
        </div>
        @endforeach

        {{-- Placeholder reference card --}}
        <div class="section-card" style="background:var(--navy-50);border:1px dashed var(--navy-200);">
            <div style="font-weight:700;font-size:0.8125rem;color:var(--navy-700);margin-bottom:8px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                Available Placeholder Tokens
            </div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:4px;font-size:0.75rem;color:var(--navy-600);">
                <span><code style="background:white;padding:1px 4px;border-radius:3px;border:1px solid var(--navy-200);">@{{full_name}}</code> = Full name</span>
                <span><code style="background:white;padding:1px 4px;border-radius:3px;border:1px solid var(--navy-200);">@{{first_name}}</code> = First name</span>
                <span><code style="background:white;padding:1px 4px;border-radius:3px;border:1px solid var(--navy-200);">@{{last_name}}</code> = Last name</span>
                <span><code style="background:white;padding:1px 4px;border-radius:3px;border:1px solid var(--navy-200);">@{{sex}}</code> = Sex</span>
                <span><code style="background:white;padding:1px 4px;border-radius:3px;border:1px solid var(--navy-200);">@{{civil_status}}</code> = Civil status</span>
                <span><code style="background:white;padding:1px 4px;border-radius:3px;border:1px solid var(--navy-200);">@{{age}}</code> = Age</span>
                <span><code style="background:white;padding:1px 4px;border-radius:3px;border:1px solid var(--navy-200);">@{{purok}}</code> = Purok/Zone</span>
                <span><code style="background:white;padding:1px 4px;border-radius:3px;border:1px solid var(--navy-200);">@{{barangay}}</code> = Barangay name</span>
                <span><code style="background:white;padding:1px 4px;border-radius:3px;border:1px solid var(--navy-200);">@{{municipality}}</code> = Municipality</span>
                <span><code style="background:white;padding:1px 4px;border-radius:3px;border:1px solid var(--navy-200);">@{{province}}</code> = Province</span>
                <span><code style="background:white;padding:1px 4px;border-radius:3px;border:1px solid var(--navy-200);">@{{purpose}}</code> = Document purpose</span>
                <span><code style="background:white;padding:1px 4px;border-radius:3px;border:1px solid var(--navy-200);">@{{control_number}}</code> = Control number</span>
                <span><code style="background:white;padding:1px 4px;border-radius:3px;border:1px solid var(--navy-200);">@{{date_issued}}</code> = Date issued</span>
                <span><code style="background:white;padding:1px 4px;border-radius:3px;border:1px solid var(--navy-200);">@{{date_today}}</code> = Today's date</span>
                <span><code style="background:white;padding:1px 4px;border-radius:3px;border:1px solid var(--navy-200);">@{{prepared_by}}</code> = Staff name</span>
                <span><code style="background:white;padding:1px 4px;border-radius:3px;border:1px solid var(--navy-200);">@{{approved_by}}</code> = Captain name</span>
            </div>
        </div>
    </div>



</div>
</div>
