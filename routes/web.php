<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Volt::route('dashboard', 'pages.dashboard.dashboard-page')->name('dashboard');

    // Residents Module
    Volt::route('residents', 'pages.residents.resident-list')->name('residents.index');
    Volt::route('residents/create', 'pages.residents.resident-create')->name('residents.create');
    Volt::route('residents/{resident}/edit', 'pages.residents.resident-edit')->name('residents.edit');
    Volt::route('residents/{resident}', 'pages.residents.resident-show')->name('residents.show');

    // Households
    Volt::route('households', 'pages.residents.household-list')->name('households.index');

    // Documents Module
    Volt::route('documents', 'pages.documents.document-list')->name('documents.index');
    Volt::route('documents/issue', 'pages.documents.document-issue')->name('documents.issue');
    Volt::route('documents/{document}/print', 'pages.documents.document-print')->name('documents.print');

    // Barangay IDs
    Volt::route('ids', 'pages.ids.id-list')->name('ids.index');
    Volt::route('ids/issue', 'pages.ids.id-issue')->name('ids.issue');
    Volt::route('ids/scan', 'pages.ids.scan')->name('ids.scan');
    Volt::route('ids/mass-print', 'pages.ids.id-mass-print')->name('ids.mass-print');
    Volt::route('ids/{bId}/print', 'pages.ids.id-print')->name('ids.print');

    // Blotter
    Volt::route('blotter', 'pages.blotter.blotter-list')->name('blotter.index');
    Volt::route('blotter/create', 'pages.blotter.blotter-create')->name('blotter.create');
    Volt::route('blotter/{blotter}', 'pages.blotter.blotter-show')->name('blotter.show');
    Volt::route('blotter/{blotter}/print', 'pages.blotter.blotter-print')->name('blotter.print');

    // Businesses
    Volt::route('businesses', 'pages.businesses.business-list')->name('businesses.index');
    Volt::route('businesses/create', 'pages.businesses.business-create')->name('businesses.create');
    Volt::route('businesses/{business}/edit', 'pages.businesses.business-edit')->name('businesses.edit');


    // Health Records
    Volt::route('health', 'pages.health.health-list')->name('health.index');
    Volt::route('health/create', 'pages.health.health-create')->name('health.create');

    // Service Requests
    Volt::route('services', 'pages.services.service-list')->name('services.index');
    Volt::route('services/create', 'pages.services.service-create')->name('services.create');
    Volt::route('services/{service}', 'pages.services.service-show')->name('services.show');

    // Announcements
    Volt::route('announcements', 'pages.announcements.announcement-list')->name('announcements.index');
    Volt::route('announcements/create', 'pages.announcements.announcement-create')->name('announcements.create');

    // Revenue/Treasury (Level 3+)
    Route::middleware([\App\Http\Middleware\PrivilegeMiddleware::class . ':3'])->group(function () {
        Volt::route('revenue', 'pages.revenue.revenue-list')->name('revenue.index');
        Volt::route('revenue/create', 'pages.revenue.revenue-create')->name('revenue.create');
    });

    // Meeting Minutes
    Volt::route('meetings', 'pages.meetings.meeting-list')->name('meetings.index');
    Volt::route('meetings/create', 'pages.meetings.meeting-create')->name('meetings.create');

    // User Preferences (all users)
    Volt::route('account/preferences', 'pages.settings.user-preferences')->name('preferences.index');

    // Admin Only (Level 4)
    Route::middleware([\App\Http\Middleware\PrivilegeMiddleware::class . ':4'])->group(function () {
        Volt::route('admin', 'pages.admin.admin-dashboard')->name('admin.index');
        Volt::route('admin/users', 'pages.admin.user-manage')->name('admin.users');
        Volt::route('officials', 'pages.officials.official-list')->name('officials.index');
        Volt::route('settings', 'pages.settings.settings-page')->name('settings.index');
    });

    // Reports (Level 3+)
    Route::middleware([\App\Http\Middleware\PrivilegeMiddleware::class . ':3'])->group(function () {
        Volt::route('reports', 'pages.reports.reports-dashboard')->name('reports.index');
    });
});

// Public verification page (no auth required)
Route::get('verify/{token}', \App\Http\Controllers\VerificationController::class)->name('verify');

require __DIR__.'/auth.php';
