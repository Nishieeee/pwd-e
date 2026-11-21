<?php

namespace App\Livewire\Candidate;

use App\Models\EmployerProfile;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Employer Profile')]
class EmployerShow extends Component
{
    public EmployerProfile $employer;
    public bool $isOwnProfile = false;

    public function mount(EmployerProfile $employer): void
    {
        $this->employer = $employer->load([
            'accommodations',
            'jobPostings' => function ($query) {
                $query->where('status', 'published')
                    ->with('accommodations')
                    ->latest();
            },
        ]);

        $user = Auth::user();
        $this->isOwnProfile = (bool) ($user && $user->isEmployer() && $user->employerProfile?->id === $this->employer->id);
    }

    public function render(): View
    {
        $jobs = $this->employer->jobPostings;
        $accommodationsByCategory = $this->employer->accommodations->groupBy('category');
        $user = Auth::user();
        $layout = ($user && $user->isEmployer()) ? 'layouts.employer' : 'layouts.candidate';

        return view('livewire.candidate.employer-show', [
            'jobs' => $jobs,
            'accommodationsByCategory' => $accommodationsByCategory,
            'isOwnProfile' => $this->isOwnProfile,
        ])->layout($layout);
    }
}
