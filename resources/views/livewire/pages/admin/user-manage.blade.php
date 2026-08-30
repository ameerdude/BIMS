<?php

use Livewire\Volt\Component;
use App\Models\User;
use Livewire\WithPagination;

new #[Layout("layouts.app")] class extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showCreateModal = false;
    public bool $showEditModal = false;

    // Form fields
    public string $formName = '';
    public string $formEmail = '';
    public string $formPassword = '';
    public string $formPasswordConfirmation = '';
    public string $formRole = 'staff';
    public int $formPrivilegeLevel = 1;
    public bool $formIsActive = true;
    public int $editUserId = 0;

    public function mount(): void
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403);
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreate(): void
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->formName = '';
        $this->formEmail = '';
        $this->formPassword = '';
        $this->formPasswordConfirmation = '';
        $this->formRole = 'staff';
        $this->formPrivilegeLevel = 1;
        $this->formIsActive = true;
        $this->editUserId = 0;
    }

    public function createUser(): void
    {
        $this->validate([
            'formName' => 'required|string|max:255',
            'formEmail' => 'required|email|unique:users,email',
            'formPassword' => 'required|string|min:6|confirmed',
            'formRole' => 'required|string|max:100',
        ]);

        User::create([
            'name' => $this->formName,
            'email' => $this->formEmail,
            'password' => $this->formPassword,
            'role' => $this->formRole,
            'privilege_level' => $this->formPrivilegeLevel,
            'is_active' => $this->formIsActive,
        ]);

        $this->showCreateModal = false;
        $this->resetForm();
        session()->flash('success', 'User account created successfully!');
    }

    public function openEdit(User $user): void
    {
        $this->editUserId = $user->id;
        $this->formName = $user->name;
        $this->formEmail = $user->email;
        $this->formPassword = '';
        $this->formPasswordConfirmation = '';
        $this->formRole = $user->role;
        $this->formPrivilegeLevel = $user->privilege_level;
        $this->formIsActive = $user->is_active;
        $this->showEditModal = true;
    }

    public function closeEdit(): void
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function updateUser(): void
    {
        $user = User::findOrFail($this->editUserId);

        $rules = [
            'formName' => 'required|string|max:255',
            'formEmail' => 'required|email|unique:users,email,' . $this->editUserId,
            'formRole' => 'required|string|max:100',
        ];

        if ($this->formPassword) {
            $rules['formPassword'] = 'required|string|min:6|confirmed';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->formName,
            'email' => $this->formEmail,
            'role' => $this->formRole,
            'privilege_level' => $this->formPrivilegeLevel,
            'is_active' => $this->formIsActive,
        ];

        if ($this->formPassword) {
            $data['password'] = $this->formPassword;
        }

        $user->update($data);

        $this->showEditModal = false;
        $this->resetForm();
        session()->flash('success', 'User account updated successfully!');
    }

    public function toggleActive(User $user): void
    {
        // Prevent deactivating yourself
        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot deactivate your own account.');
            return;
        }

        $user->update(['is_active' => !$user->is_active]);
        session()->flash('success', "User {$user->name} has been " . ($user->is_active ? 'activated' : 'deactivated') . '.');
    }

    public function deleteUser(User $user): void
    {
        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot delete your own account.');
            return;
        }

        $user->delete();
        session()->flash('success', 'User account deleted.');
    }

    public function getUsers()
    {
        return User::query()
            ->where(function ($w) {
                $w->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);
    }
}; ?>

<div>
<div style="max-width:900px;margin:0 auto;">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <div>
            <h1 style="font-size:1.25rem;font-weight:800;color:var(--text-primary);margin:0;">👥 User Accounts</h1>
            <p style="font-size:0.8125rem;color:var(--text-secondary);margin:2px 0 0;">Manage staff accounts, roles, and access</p>
        </div>
        <button wire:click="openCreate" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Account
        </button>
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

    {{-- Search --}}
    <div class="section-card" style="margin-bottom:16px;">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name or email..." class="form-input" style="width:100%;">
    </div>

    {{-- Users Table --}}
    <div class="section-card">
        <table style="width:100%;border-collapse:collapse;font-size:0.8125rem;">
            <thead>
                <tr style="border-bottom:2px solid var(--border);">
                    <th style="text-align:left;padding:10px 12px;font-weight:700;color:var(--text-secondary);">Name</th>
                    <th style="text-align:left;padding:10px 12px;font-weight:700;color:var(--text-secondary);">Email</th>
                    <th style="text-align:left;padding:10px 12px;font-weight:700;color:var(--text-secondary);">Role</th>
                    <th style="text-align:center;padding:10px 12px;font-weight:700;color:var(--text-secondary);">Level</th>
                    <th style="text-align:center;padding:10px 12px;font-weight:700;color:var(--text-secondary);">Status</th>
                    <th style="text-align:right;padding:10px 12px;font-weight:700;color:var(--text-secondary);">Actions</th>
                </tr>
            </thead>
            <tbody>
                @php $users = $this->getUsers(); @endphp
                @forelse($users as $user)
                <tr style="border-bottom:1px solid var(--border-light);">
                    <td style="padding:10px 12px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:32px;height:32px;border-radius:50%;background:{{ $user->isAdmin() ? 'var(--purple-100)' : ($user->isSecretary() ? 'var(--blue-100)' : 'var(--gray-100)') }};display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.75rem;color:{{ $user->isAdmin() ? 'var(--purple-700)' : ($user->isSecretary() ? 'var(--blue-700)' : 'var(--gray-700)') }};">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <span style="font-weight:600;color:var(--text-primary);">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td style="padding:10px 12px;color:var(--text-secondary);">{{ $user->email }}</td>
                    <td style="padding:10px 12px;">
                        <span style="font-size:0.8125rem;font-weight:600;color:var(--text-primary);">{{ $user->role }}</span>
                    </td>
                    <td style="padding:10px 12px;text-align:center;">
                        <span class="badge {{ $user->privilege_level >= 4 ? 'badge-purple' : ($user->privilege_level >= 3 ? 'badge-blue' : ($user->privilege_level >= 2 ? 'badge-green' : 'badge-gray')) }}">Lv.{{ $user->privilege_level }}</span>
                    </td>
                    <td style="padding:10px 12px;text-align:center;">
                        <span class="badge {{ $user->is_active ? 'badge-green' : 'badge-red' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                    </td>
                    <td style="padding:10px 12px;text-align:right;white-space:nowrap;">
                        <button wire:click="openEdit({{ $user->id }})" class="btn btn-sm btn-outline" style="padding:4px 8px;font-size:0.75rem;">Edit</button>
                        <button wire:click="toggleActive({{ $user->id }})" wire:confirm="Are you sure?" class="btn btn-sm {{ $user->is_active ? 'btn-outline' : 'btn-success' }}" style="padding:4px 8px;font-size:0.75rem;">{{ $user->is_active ? 'Deactivate' : 'Activate' }}</button>
                        @if($user->id !== auth()->id())
                        <button wire:click="deleteUser({{ $user->id }})" wire:confirm="Delete this user permanently?" class="btn btn-sm btn-danger" style="padding:4px 8px;font-size:0.75rem;">Delete</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding:24px;text-align:center;color:var(--text-muted);">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:12px;">{{ $users->links() }}</div>
    </div>

    {{-- Create Modal --}}
    @if($showCreateModal)
    <div style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;" wire:click.self="closeCreate">
        <div style="position:absolute;inset:0;background:rgba(0,0,0,0.6);backdrop-filter:blur(2px);"></div>
        <div style="position:relative;z-index:51;background:var(--bg-primary,#fff);border-radius:var(--radius-lg);padding:28px;width:520px;max-width:92vw;max-height:90vh;overflow-y:auto;box-shadow:0 25px 80px rgba(0,0,0,0.4);border:1px solid var(--border);">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                <h2 style="font-size:1.125rem;font-weight:700;margin:0;color:var(--text-primary);">Create User Account</h2>
                <button type="button" wire:click="closeCreate" style="background:none;border:none;cursor:pointer;padding:4px;border-radius:var(--radius);color:var(--text-muted);" title="Close">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <form wire:submit="createUser">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" wire:model="formName" class="form-input" placeholder="Juan Dela Cruz">
                    @error('formName') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" wire:model="formEmail" class="form-input" placeholder="juan@example.com">
                    @error('formEmail') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div style="padding:8px 12px 4px;font-size:0.75rem;font-weight:600;color:var(--text-secondary);">Password</div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" style="font-size:0.75rem;">Password *</label>
                        <input type="password" wire:model="formPassword" class="form-input">
                        @error('formPassword') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size:0.75rem;">Confirm Password *</label>
                        <input type="password" wire:model="formPasswordConfirmation" class="form-input">
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Position / Title *</label>
                        <input type="text" wire:model="formRole" class="form-input" placeholder="e.g. Barangay Captain, Treasurer, Staff">
                        @error('formRole') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Privilege Level *</label>
                        <select wire:model="formPrivilegeLevel" class="form-select">
                            <option value="1">Level 1</option>
                            <option value="2">Level 2</option>
                            <option value="3">Level 3</option>
                            <option value="4">Level 4</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select wire:model="formIsActive" class="form-select">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div style="padding:8px 12px;background:var(--navy-50);border-radius:var(--radius);font-size:0.75rem;color:var(--navy-600);margin-top:4px;">
                    <strong>Level Guide:</strong> Level 4 = Full Admin Access (everything) · Level 3 = Treasurer/Secretary access (Revenue, Treasury, Reports) · Level 2 = Barangay Official access (Residents, Documents, Services) · Level 1 = Staff access (basic operations only)
                </div>
                <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:20px;padding-top:16px;border-top:1px solid var(--border);">
                    <button type="button" wire:click="closeCreate" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Account</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Edit Modal --}}
    @if($showEditModal)
    <div style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;" wire:click.self="closeEdit">
        <div style="position:absolute;inset:0;background:rgba(0,0,0,0.6);backdrop-filter:blur(2px);"></div>
        <div style="position:relative;z-index:51;background:var(--bg-primary,#fff);border-radius:var(--radius-lg);padding:28px;width:520px;max-width:92vw;max-height:90vh;overflow-y:auto;box-shadow:0 25px 80px rgba(0,0,0,0.4);border:1px solid var(--border);">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                <h2 style="font-size:1.125rem;font-weight:700;margin:0;color:var(--text-primary);">Edit User Account</h2>
                <button type="button" wire:click="closeEdit" style="background:none;border:none;cursor:pointer;padding:4px;border-radius:var(--radius);color:var(--text-muted);" title="Close">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <form wire:submit="updateUser">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" wire:model="formName" class="form-input">
                    @error('formName') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" wire:model="formEmail" class="form-input">
                    @error('formEmail') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div style="padding:8px 12px 4px;font-size:0.75rem;font-weight:600;color:var(--text-secondary);">Password</div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" style="font-size:0.75rem;">New Password</label>
                        <input type="password" wire:model="formPassword" class="form-input" placeholder="Leave blank to keep current">
                        @error('formPassword') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size:0.75rem;">Confirm Password</label>
                        <input type="password" wire:model="formPasswordConfirmation" class="form-input" placeholder="Repeat password">
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Position / Title *</label>
                        <input type="text" wire:model="formRole" class="form-input" placeholder="e.g. Barangay Captain, Treasurer, Staff">
                        @error('formRole') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Privilege Level *</label>
                        <select wire:model="formPrivilegeLevel" class="form-select">
                            <option value="1">Level 1</option>
                            <option value="2">Level 2</option>
                            <option value="3">Level 3</option>
                            <option value="4">Level 4</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select wire:model="formIsActive" class="form-select">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div style="padding:8px 12px;background:var(--navy-50);border-radius:var(--radius);font-size:0.75rem;color:var(--navy-600);margin-top:4px;">
                    <strong>Level Guide:</strong> Level 4 = Full Admin Access (everything) · Level 3 = Treasurer/Secretary access (Revenue, Treasury, Reports) · Level 2 = Barangay Official access (Residents, Documents, Services) · Level 1 = Staff access (basic operations only)
                </div>
                <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:20px;padding-top:16px;border-top:1px solid var(--border);">
                    <button type="button" wire:click="closeEdit" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>
</div>
