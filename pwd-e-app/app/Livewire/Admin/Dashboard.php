<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\Concerns\HandlesVerificationApprovals;
use App\Models\Application;
use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\PlatformSetting;
use App\Models\User;
use App\Models\VerificationRequest;
use App\Models\WorkforceRecord;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Admin Dashboard')]
class Dashboard extends Component
{
    use HandlesVerificationApprovals;
    use WithPagination;

    public string $pwdTaxDeductionRate = '20';

    public function mount(): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $rate = PlatformSetting::pwdTaxDeductionRate();
        $this->pwdTaxDeductionRate = floor($rate) == $rate ? (string) (int) $rate : (string) $rate;
    }

    public function saveTaxDeductionRate(): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            abort(403);
        }

        $this->validate([
            'pwdTaxDeductionRate' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $normalized = round((float) $this->pwdTaxDeductionRate, 2);
        PlatformSetting::setValue(PlatformSetting::KEY_PWD_TAX_RATE, $normalized, $user->id);

        $this->pwdTaxDeductionRate = floor($normalized) == $normalized ? (string) (int) $normalized : (string) $normalized;

        if (function_exists('activity')) {
            activity('platform_settings')
                ->causedBy($user)
                ->withProperties(['pwd_tax_deduction_rate' => $normalized])
                ->log('Updated statutory PWD wage tax deduction rate to '.$this->pwdTaxDeductionRate.'%');
        }

        Flux::toast(variant: 'success', text: __('Statutory PWD tax deduction rate updated to :rate%.', ['rate' => $this->pwdTaxDeductionRate]));
    }

    public function getSelectableRequestIds(): array
    {
        $query = VerificationRequest::whereIn('status', ['pending', 'needs_more_docs']);
        if ($this->category === 'pwd') {
            $query->whereHas('user', fn ($q) => $q->where('role', 'candidate'));
        } elseif ($this->category === 'employer') {
            $query->whereHas('user', fn ($q) => $q->where('role', 'employer'));
        }

        return $query->pluck('id')->all();
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
            'disputed_separations' => WorkforceRecord::where('separation_status', 'disputed')->count(),
        ];

        $topEmployers = EmployerProfile::query()
            ->withCount('jobPostings')
            ->orderByDesc('job_postings_count')
            ->limit(5)
            ->get();

        $pendingQuery = VerificationRequest::with(['user.candidateProfile', 'user.employerProfile'])
            ->whereIn('status', ['pending', 'needs_more_docs'])
            ->latest();

        if ($this->category === 'pwd') {
            $pendingQuery->whereHas('user', fn ($q) => $q->where('role', 'candidate'));
        } elseif ($this->category === 'employer') {
            $pendingQuery->whereHas('user', fn ($q) => $q->where('role', 'employer'));
        }

        $pendingVerifications = $pendingQuery->paginate(6, ['*'], 'verif_page');

        $pendingCounts = [
            'all' => VerificationRequest::whereIn('status', ['pending', 'needs_more_docs'])->count(),
            'pwd' => VerificationRequest::whereIn('status', ['pending', 'needs_more_docs'])
                ->whereHas('user', fn ($q) => $q->where('role', 'candidate'))
                ->count(),
            'employer' => VerificationRequest::whereIn('status', ['pending', 'needs_more_docs'])
                ->whereHas('user', fn ($q) => $q->where('role', 'employer'))
                ->count(),
        ];

        return view('livewire.admin.dashboard', [
            'usersByRole' => $usersByRole,
            'stats' => $stats,
            'topEmployers' => $topEmployers,
            'pendingVerifications' => $pendingVerifications,
            'pendingCounts' => $pendingCounts,
        ]);
    }
}
