<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BIMS | Sign In</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600;700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
<div class="login-layout">

    {{-- Left hero panel --}}
    <div class="login-hero">
        <div class="login-hero-content">
            <div class="login-hero-icon">
                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 21h18M3 7v1a3 3 0 006 0V7m0 0V4h6v3m0 0v1a3 3 0 006 0V7M6 21V10m6 11V10m6 11V10"/>
                </svg>
            </div>
            <h1>BIMS</h1>
            <p>Barangay Information Management System<br>by ameerdude</p>
            <div class="login-stats">
                <div class="login-stat">
                    <div class="login-stat-value">50+</div>
                    <div class="login-stat-label">Residents</div>
                </div>
                <div class="login-stat-divider"></div>
                <div class="login-stat">
                    <div class="login-stat-value">15</div>
                    <div class="login-stat-label">Modules</div>
                </div>
                <div class="login-stat-divider"></div>
                <div class="login-stat">
                    <div class="login-stat-value">24/7</div>
                    <div class="login-stat-label">Access</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right form panel --}}
    <div class="login-form-side">
        <div class="login-form-container">

            {{-- Mobile-only brand --}}
            <div class="login-form-brand">
                <div style="width:56px;height:56px;border-radius:14px;background:var(--blue-600);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 21h18M3 7v1a3 3 0 006 0V7m0 0V4h6v3m0 0v1a3 3 0 006 0V7M6 21V10m6 11V10m6 11V10"/>
                    </svg>
                </div>
                <div style="font-size:1.125rem;font-weight:800;color:var(--text-primary);">BIMS</div>
                <div style="font-size:0.6875rem;color:var(--text-muted);margin-top:2px;">Barangay Information Management System</div>
            </div>

            <div class="login-form-card">
                <div class="login-form-title">Welcome back</div>
                <div class="login-form-subtitle">Sign in to your account to continue</div>

                @if (session('status'))
                    <div class="alert alert-success" style="margin-bottom:16px;">{{ session('status') }}</div>
                @endif

                <livewire:auth.login-form />
            </div>

            <div class="login-demo-card">
                <div class="login-demo-title">Demo Credentials</div>
                <div class="login-demo-item"><strong>Admin:</strong> admin@barangay.local / password</div>
                <div class="login-demo-item"><strong>Secretary:</strong> secretary@barangay.local / password</div>
                <div class="login-demo-item"><strong>Staff:</strong> staff@barangay.local / password</div>
            </div>

            <div class="login-footer">Barangay Information Management System v1.0 · by ameerdude</div>
        </div>
    </div>
</div>
@livewireScripts
</body>
</html>
