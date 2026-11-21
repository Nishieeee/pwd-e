<?php

namespace App\Livewire\Candidate;

use App\Models\EmployerProfile;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Employer Profile')]
#[Layout('layouts.candidate')]
class EmployerShow extends Component
{
    public EmployerProfile $employer;

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
    }

    public function render(): View
    {
        $jobs = $this->employer->jobPostings;
        $accommodationsByCategory = $this->employer->accommodations->groupBy('category');

        return view('livewire.candidate.employer-show', [
            'jobs' => $jobs,
            'accommodationsByCategory' => $accommodationsByCategory,
        ]);
    }
}
