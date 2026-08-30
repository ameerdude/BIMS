<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ sidebarOpen: false }" class="bg-white border-b border-gray-100">
    <!-- Top Navigation Bar -->
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = !sidebarOpen" class="sm:hidden p-2 text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate>
                        <span class="text-lg font-bold text-indigo-600">🏛️ BIMS</span>
                    </a>
                </div>
            </div>
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                            <div>{{ auth()->user()->name }}</div>
                            <div class="ms-1 text-xs px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700">{{ ucfirst(auth()->user()->role) }}</div>
                            <svg class="ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>{{ __('Profile') }}</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>

    <!-- Sidebar (Mobile) -->
    <div x-show="sidebarOpen" x-cloak @click.away="sidebarOpen = false" class="sm:hidden bg-white border-b">
        <div class="px-4 py-3 space-y-1">
            @php
            $navItems = [
                ['route' => 'dashboard', 'label' => '📊 Dashboard', 'icon' => ''],
                ['route' => 'residents.index', 'label' => '👥 Residents', 'icon' => ''],
                ['route' => 'households.index', 'label' => '🏠 Households', 'icon' => ''],
                ['route' => 'documents.index', 'label' => '📄 Documents', 'icon' => ''],
                ['route' => 'ids.index', 'label' => '🪪 Barangay IDs', 'icon' => ''],
                ['route' => 'blotter.index', 'label' => '📋 Blotter', 'icon' => ''],
                ['route' => 'businesses.index', 'label' => '🏪 Businesses', 'icon' => ''],
                ['route' => 'officials.index', 'label' => '👨‍⚖️ Officials', 'icon' => ''],
                ['route' => 'reports.index', 'label' => '📈 Reports', 'icon' => ''],
            ];
            if (auth()->user()->isAdmin()) {
                $navItems[] = ['route' => 'settings.index', 'label' => '⚙️ Settings', 'icon' => ''];
            }
            @endphp
            @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}" wire:navigate
               class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs($item['route']) ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                {{ $item['label'] }}
            </a>
            @endforeach
        </div>
    </div>
</nav>
