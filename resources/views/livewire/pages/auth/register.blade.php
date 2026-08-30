<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <form wire:submit="register" style="display:flex;flex-direction:column;gap:16px;">
        <!-- Name -->
        <div>
            <label style="display:block;font-size:0.875rem;font-weight:600;color:#374151;margin-bottom:6px;">Full Name</label>
            <input type="text" wire:model="name" placeholder="Juan Dela Cruz" class="form-input" required autofocus autocomplete="name">
            @error('name') <span style="font-size:0.75rem;color:#e17055;margin-top:4px;display:block;">{{ $message }}</span> @enderror
        </div>

        <!-- Email -->
        <div>
            <label style="display:block;font-size:0.875rem;font-weight:600;color:#374151;margin-bottom:6px;">Email Address</label>
            <input type="email" wire:model="email" placeholder="user@barangay.local" class="form-input" required autocomplete="username">
            @error('email') <span style="font-size:0.75rem;color:#e17055;margin-top:4px;display:block;">{{ $message }}</span> @enderror
        </div>

        <!-- Password -->
        <div>
            <label style="display:block;font-size:0.875rem;font-weight:600;color:#374151;margin-bottom:6px;">Password</label>
            <input type="password" wire:model="password" placeholder="••••••••" class="form-input" required autocomplete="new-password">
            @error('password') <span style="font-size:0.75rem;color:#e17055;margin-top:4px;display:block;">{{ $message }}</span> @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label style="display:block;font-size:0.875rem;font-weight:600;color:#374151;margin-bottom:6px;">Confirm Password</label>
            <input type="password" wire:model="password_confirmation" placeholder="••••••••" class="form-input" required autocomplete="new-password">
        </div>

        <!-- Register Button -->
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;font-size:1rem;">
            📝 Create Account
        </button>
    </form>

    <div style="text-align:center;margin-top:16px;">
        <a href="{{ route('login') }}" wire:navigate style="font-size:0.875rem;color:#6c5ce7;text-decoration:none;font-weight:500;">
            Already have an account? Sign in
        </a>
    </div>
</div>
