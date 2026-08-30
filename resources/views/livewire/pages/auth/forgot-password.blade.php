<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink($this->only('email'));

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));
            return;
        }

        $this->reset('email');
        session()->flash('status', __($status));
    }
}; ?>

<div>
    <h2 style="font-size:1.125rem;font-weight:700;color:#2d3436;margin-bottom:12px;text-align:center;">Forgot your password?</h2>
    <p style="font-size:0.8125rem;color:#636e72;margin-bottom:20px;text-align:center;">
        Enter your email and we'll send you a password reset link.
    </p>

    @if (session('status'))
        <div class="alert alert-success" style="margin-bottom:16px;font-size:0.875rem;">{{ session('status') }}</div>
    @endif

    <form wire:submit="sendPasswordResetLink" style="display:flex;flex-direction:column;gap:16px;">
        <div>
            <label style="display:block;font-size:0.875rem;font-weight:600;color:#374151;margin-bottom:6px;">Email Address</label>
            <input type="email" wire:model="email" class="form-input" required autofocus placeholder="you@example.com">
            @error('email') <span style="font-size:0.75rem;color:#e17055;margin-top:4px;display:block;">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;font-size:0.875rem;">
            Send Reset Link
        </button>

        <div style="text-align:center;">
            <a href="{{ route('login') }}" wire:navigate style="font-size:0.8125rem;color:#6c5ce7;text-decoration:none;font-weight:500;">
                ← Back to Sign In
            </a>
        </div>
    </form>
</div>
