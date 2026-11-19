<?php

namespace App\Livewire\Admin;

use App\Models\Application;
use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\User;
use App\Models\VerificationRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Admin Dashboard')]
class Dashboard extends Component
{
    public function mount(): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }
    }

    public function render(): View
    {
        $usersByRole = [
            'candidates' => User::where('role', 'candidate')->count(),
            'employers' => User::where('role', 'employer')->count(),
            'admins' => User::where('role', 'admin')->count(),
        ];

        $stats = [
            'pending_verifications' => VerificationRequest::where('status', 'pending')->count(),
            'active_postings' => JobPosting::where('status', 'published')->count(),
            'applications_this_week' => Application::where('created_at', '>=', now()->subDays(7))->count(),
            'total_users' => User::count(),
        ];

        $topEmployers = EmployerProfile::query()
            ->withCount('jobPostings')
            ->orderByDesc('job_postings_count')
            ->limit(5)
            ->get();

        return view('livewire.admin.dashboard', [
            'usersByRole' => $usersByRole,
            'stats' => $stats,
            'topEmployers' => $topEmployers,
        ]);
    }
}
