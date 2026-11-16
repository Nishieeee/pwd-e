<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('User Management')]
class Users extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'role')]
    public string $role = 'all';

    public function mount(): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRole(): void
    {
        $this->resetPage();
    }

    public function suspend(int $userId): void
    {
        $user = User::findOrFail($userId);

        if ($user->isAdmin()) {
            Flux::toast(variant: 'danger', text: __('Admins cannot be suspended.'));

            return;
        }

        $user->update(['status' => 'suspended']);
        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->event('suspended')
            ->log('Suspended user '.$user->email);

        Flux::toast(variant: 'success', text: __('User suspended.'));
    }

    public function reinstate(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->update(['status' => 'active']);
        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->event('reinstated')
            ->log('Reinstated user '.$user->email);

        Flux::toast(variant: 'success', text: __('User reinstated.'));
    }

    public function render(): View
    {
        $query = User::query()->with('roles')->latest();

        if ($this->role !== 'all') {
            $query->where('role', $this->role);
        }

        if (filled($this->search)) {
            $term = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }

        return view('livewire.admin.users', [
            'users' => $query->paginate(15),
        ]);
    }
}
