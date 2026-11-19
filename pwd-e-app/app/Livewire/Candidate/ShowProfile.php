<?php

namespace App\Livewire\Candidate;

use App\Models\CandidateProfile;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('View my Profile — PWD-E')]
class ShowProfile extends Component
{
    public CandidateProfile $profile;

    public ?User $candidateUser = null;

    public bool $isOwner = false;

    public bool $isEmployer = false;

    public function mount(?CandidateProfile $candidateProfile = null): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(401);
        }

        if ($candidateProfile && $candidateProfile->exists) {
            // Employer or Admin viewing a specific candidate's profile
            if (! $user->isEmployer() && ! $user->isAdmin() && $user->id !== $candidateProfile->user_id) {
                abort(403, 'Unauthorized access to candidate profile.');
            }

            $this->profile = $candidateProfile->load([
                'user',
                'education',
                'workExperiences',
                'certifications',
                'accommodations',
                'accessibilityNeeds.accommodation',
            ]);
        } else {
            // Candidate viewing their own profile
            if (! $user->isCandidate()) {
                abort(403, 'Candidate profile required.');
            }

            $this->profile = $user->candidateProfile ?? CandidateProfile::firstOrCreate(['user_id' => $user->id]);
            $this->profile->load([
                'user',
                'education',
                'workExperiences',
                'certifications',
                'accommodations',
                'accessibilityNeeds.accommodation',
            ]);
        }

        $this->candidateUser = $this->profile->user;
        $this->isOwner = ($user->id === $this->profile->user_id);
        $this->isEmployer = $user->isEmployer();
    }

    public function render(): View
    {
        $layout = $this->isEmployer ? 'layouts.employer' : 'layouts.candidate';

        return view('livewire.candidate.show-profile', [
            'profile' => $this->profile,
            'candidateUser' => $this->candidateUser,
            'isOwner' => $this->isOwner,
            'isEmployer' => $this->isEmployer,
        ])->layout($layout);
    }
}
