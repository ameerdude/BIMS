<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public function render()
    {
        return view('livewire.auth.login-form');
    }
}; ?>
