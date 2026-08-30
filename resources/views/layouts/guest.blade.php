<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'BIMS') }} | {{ $title ?? 'Login' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600;700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important;}</style>
</head>
<body class="font-sans antialiased neu" style="min-height:100vh;display:flex;align-items:center;justify-content:center;">
    <div style="width:100%;max-width:420px;padding:24px;">
        <!-- Logo & Branding -->
        <div style="text-align:center;margin-bottom:32px;">
            <div class="stat-icon" style="width:72px;height:72px;font-size:2rem;margin:0 auto 16px;background:linear-gradient(135deg,#6c5ce7,#a29bfe);color:#fff;border-radius:20px;box-shadow:var(--shadow-sm);">
                🏛️
            </div>
            <h1 style="font-size:1.5rem;font-weight:800;color:#2d3436;margin-bottom:4px;">BIMS</h1>
            <p style="font-size:0.875rem;color:#636e72;">Barangay Information Management System</p>
            <p style="font-size:0.6875rem;color:#b2bec3;margin-top:4px;">by ameerdude</p>
        </div>

        <!-- Content Card -->
        <div class="card" style="padding:32px;">
            {{ $slot }}
        </div>

        <p style="text-align:center;font-size:0.75rem;color:#a0aec0;margin-top:24px;">BIMS v1.0 · by ameerdude</p>
    </div>
</body>
</html>
