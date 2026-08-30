<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'privilege_level',
        'preferences',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'privilege_level' => 'integer',
            'preferences' => 'array',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->privilege_level >= 4;
    }

    public function isSecretary(): bool
    {
        return $this->privilege_level >= 3 && $this->privilege_level < 4;
    }

    public function isStaff(): bool
    {
        return $this->privilege_level <= 2;
    }

    // Privilege Level System
    // Level 4: Admin - Full access to everything
    // Level 3: Secretary / Treasurer / Auditor - Treasury, Officials, Reports, Documents
    // Level 2: Officials (Ranked LGU) - Residents, Documents, IDs, Blotter, Health, Services
    // Level 1: Staff - Dashboard, Residents list, Documents, IDs, basic operations

    const PRIVILEGE_LEVELS = [
        4 => 'Administrator',
        3 => 'Treasurer / Secretary',
        2 => 'Barangay Official',
        1 => 'Staff',
    ];

    public function getPrivilegeLevelLabelAttribute(): string
    {
        return self::PRIVILEGE_LEVELS[$this->privilege_level] ?? 'Unknown';
    }

    public function hasPrivilege(int $minLevel): bool
    {
        return $this->privilege_level >= $minLevel;
    }

    public function canAccessRevenue(): bool
    {
        return $this->privilege_level >= 3;
    }

    public function canManageOfficials(): bool
    {
        return $this->privilege_level >= 4;
    }

    public function canManageUsers(): bool
    {
        return $this->privilege_level >= 4;
    }

    public function canAccessSettings(): bool
    {
        return $this->privilege_level >= 4;
    }

    public function canAccessReports(): bool
    {
        return $this->privilege_level >= 3;
    }

    public function issuedDocuments()
    {
        return $this->hasMany(DocumentIssued::class, 'issued_by');
    }

    public function recordedBlotters()
    {
        return $this->hasMany(BlotterRecord::class, 'recorded_by');
    }

    // Default preferences
    const DEFAULT_PREFERENCES = [
        'theme' => 'light',
        'font_family' => 'figtree',
        'font_size' => 'default',
        'sidebar_compact' => false,
        'animations' => true,
        'date_format' => 'M d, Y',
        'rows_per_page' => 10,
        'show_welcome_tips' => true,
        'table_density' => 'comfortable',
    ];

    public function pref(string $key, mixed $default = null): mixed
    {
        return $this->preferences[$key] ?? ($default ?? self::DEFAULT_PREFERENCES[$key] ?? null);
    }

    public function setPref(string $key, mixed $value): void
    {
        $prefs = $this->preferences ?? [];
        $prefs[$key] = $value;
        $this->update(['preferences' => $prefs]);
    }
}
