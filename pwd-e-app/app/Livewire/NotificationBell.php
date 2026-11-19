<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    public function getUnreadCountProperty(): int
    {
        $user = Auth::user();

        return $user ? $user->unreadNotifications()->count() : 0;
    }

    public function markAllRead(): void
    {
        Auth::user()?->unreadNotifications->markAsRead();
    }

    public function render(): View
    {
        return view('livewire.notification-bell', [
            'unreadCount' => $this->unreadCount,
            'notifications' => Auth::user()?->notifications()->latest()->limit(10)->get() ?? collect(),
        ]);
    }
}
