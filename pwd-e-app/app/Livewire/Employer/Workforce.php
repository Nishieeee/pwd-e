<?php

namespace App\Livewire\Employer;

use App\Models\WorkforceRecord;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Workforce & Tax Incentives')]
#[Layout('layouts.employer')]
class Workforce extends Component
{
    public function mount(): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isEmployer()) {
            abort(403);
        }
    }

    public function removeRecord(int $id): void
    {
        $record = WorkforceRecord::where('employer_profile_id', Auth::user()->employerProfile?->id)->findOrFail($id);

        if (function_exists('activity')) {
            activity('workforce')
                ->performedOn($record)
                ->causedBy(Auth::user())
                ->withProperties([
                    'candidate_profile_id' => $record->candidate_profile_id,
                    'annual_wage' => $record->annual_wage,
                ])
                ->log('Workforce record removed');
        }

        $record->delete();
        $this->dispatch('toast', message: __('Record removed.'));
    }

    public function syncPwdVerification(int $id): void
    {
        $record = WorkforceRecord::with('candidateProfile.user')
            ->where('employer_profile_id', Auth::user()->employerProfile?->id)
            ->findOrFail($id);

        $user = $record->candidateProfile?->user;
        $isVerified = (bool) ($user && $user->isVerified() && ! empty($user->pwd_id_number));

        $record->update(['pwd_verified' => $isVerified]);

        if (function_exists('activity')) {
            activity('workforce')
                ->performedOn($record)
                ->causedBy(Auth::user())
                ->withProperties(['pwd_verified' => $isVerified])
                ->log('Workforce PWD verification status synchronized');
        }

        $this->dispatch('toast', message: $isVerified
            ? __('PWD status verified against registry.')
            : __('Candidate does not have verified PWD credentials.'));
    }

    public function togglePwdVerified(int $id): void
    {
        $this->syncPwdVerification($id);
    }

    public function render(): View
    {
        $profile = Auth::user()->employerProfile;
        $records = WorkforceRecord::with('candidateProfile.user')
            ->where('employer_profile_id', $profile?->id)
            ->whereNull('ended_at')
            ->latest('hired_at')
            ->get();

        $totalEmployees = $records->count();
        $pwdCount = $records->where('pwd_verified', true)->count();
        $pwdPercent = $totalEmployees > 0 ? round(($pwdCount / $totalEmployees) * 100, 1) : 0;
        $totalWages = $records->where('pwd_verified', true)->sum('annual_wage');
        // RA 10524: additional 25% gross deduction on wages paid to PWD workers
        $deduction = round($totalWages * 0.25, 2);

        return view('livewire.employer.workforce', [
            'records' => $records,
            'totalEmployees' => $totalEmployees,
            'pwdCount' => $pwdCount,
            'pwdPercent' => $pwdPercent,
            'totalWages' => $totalWages,
            'deduction' => $deduction,
        ]);
    }
}
