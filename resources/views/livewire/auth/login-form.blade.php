<div>
    <form wire:submit="login" style="display:flex;flex-direction:column;gap:16px;">
        <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" wire:model="email" placeholder="admin@barangay.local"
                   class="form-input" autocomplete="username">
            @error('email') <span class="form-error">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" wire:model="password" placeholder="••••••••"
                   class="form-input" autocomplete="current-password">
            @error('password') <span class="form-error">{{ $message }}</span> @enderror
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            <input type="checkbox" wire:model="remember" id="remember"
                   style="width:16px;height:16px;accent-color:var(--blue-600);">
            <label for="remember" class="form-check-label" style="font-size:0.8125rem;color:var(--text-secondary);font-weight:400;">Remember me</label>
        </div>
        <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">
            Sign In
        </button>
    </form>
    @if (Route::has('password.request'))
        <div style="text-align:center;margin-top:12px;">
            <a href="{{ route('password.request') }}" wire:navigate
               style="font-size:0.8125rem;color:var(--blue-600);text-decoration:none;font-weight:500;">
                Forgot your password?
            </a>
        </div>
    @endif
</div>
