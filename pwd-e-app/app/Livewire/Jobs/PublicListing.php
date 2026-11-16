<?php

namespace App\Livewire\Jobs;

use App\Models\JobPosting;
use Livewire\Component;
use Livewire\WithPagination;

class PublicListing extends Component
{
    use WithPagination;

    public string $search = '';
    public string $accommodationFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingAccommodationFilter(): void
    {
        $this->resetPage();
    }

    public function filterByAccommodation(string $name): void
    {
        $this->accommodationFilter = $name;
        $this->resetPage();
    }

    public function render()
    {
        $query = JobPosting::query()
            ->where('status', 'published')
            ->with(['employerProfile', 'accommodations'])
            ->latest('published_at');

        if ($this->search) {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                  ->orWhere('location', 'like', $term)
                  ->orWhere('description', 'like', $term)
                  ->orWhereHas('accommodations', fn ($aq) => $aq->where('name', 'like', $term))
                  ->orWhereHas('employerProfile', fn ($eq) => $eq->where('company_name', 'like', $term));
            });
        }

        if ($this->accommodationFilter) {
            $query->whereHas('accommodations', fn ($aq) =>
                $aq->where('name', 'like', '%' . $this->accommodationFilter . '%')
            );
        }

        $jobs = $query->paginate(10);

        // Popular accommodation names for the pill filters
        // Pull top 5 accommodations used in published jobs via the pivot table
        $popularAccommodations = \App\Models\Accommodation::select('accommodations.name')
            ->join('job_accommodations', 'accommodations.id', '=', 'job_accommodations.accommodation_id')
            ->join('job_postings', 'job_postings.id', '=', 'job_accommodations.job_posting_id')
            ->where('job_postings.status', 'published')
            ->groupBy('accommodations.id', 'accommodations.name')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(5)
            ->pluck('accommodations.name');

        return view('livewire.jobs.public-listing', compact('jobs', 'popularAccommodations'));
    }
}
