<?php

namespace App\Livewire\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

#[Title('Audit Log')]
class AuditLog extends Component
{
    use WithPagination;

    #[Url(as: 'user')]
    public string $userFilter = '';

    #[Url(as: 'action')]
    public string $actionFilter = '';

    #[Url(as: 'from')]
    public string $fromDate = '';

    #[Url(as: 'to')]
    public string $toDate = '';

    public function mount(): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }
    }

    public function updatingUserFilter(): void
    {
        $this->resetPage();
    }

    public function updatingActionFilter(): void
    {
        $this->resetPage();
    }

    public function updatingFromDate(): void
    {
        $this->resetPage();
    }

    public function updatingToDate(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->userFilter = '';
        $this->actionFilter = '';
        $this->fromDate = '';
        $this->toDate = '';
        $this->resetPage();
    }

    public function render(): View
    {
        $query = Activity::query()->latest();

        if (filled($this->userFilter)) {
            $query->whereHas('causer', function ($q) {
                $q->where('name', 'like', '%'.trim($this->userFilter).'%')
                    ->orWhere('email', 'like', '%'.trim($this->userFilter).'%');
            });
        }

        if (filled($this->actionFilter)) {
            $query->where('event', 'like', '%'.trim($this->actionFilter).'%')
                ->orWhere('description', 'like', '%'.trim($this->actionFilter).'%');
        }

        if (filled($this->fromDate)) {
            $query->whereDate('created_at', '>=', $this->fromDate);
        }

        if (filled($this->toDate)) {
            $query->whereDate('created_at', '<=', $this->toDate);
        }

        return view('livewire.admin.audit-log', [
            'activities' => $query->paginate(20),
        ]);
    }
}
