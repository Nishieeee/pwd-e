<?php

namespace App\Livewire;

use App\Models\AccountLink;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class RoleSwitcher extends Component
{
    public bool $showModal = false;

    public ?int $selectedTargetId = null;

    public string $password = '';

    public function openModal(): void
    {
        $user = Auth::user();
        if (! $user || ! in_array($user->role, ['candidate', 'employer'], true)) {
            return;
        }

        $targets = $user->linkedSwitchTargets();
        $this->selectedTargetId = $targets->first()?->id;
        $this->password = '';
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->password = '';
        $this->resetErrorBag();
    }

    public function confirmSwitch()
    {
        $actor = Auth::user();
        if (! $actor || ! in_array($actor->role, ['candidate', 'employer'], true)) {
            abort(403);
        }

        $this->validate([
            'selectedTargetId' => ['required', 'integer'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = 'role-switch:'.$actor->id.':'.request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('password', __('Too many failed attempts. Try again in :seconds seconds.', ['seconds' => $seconds]));

            return;
        }

        $employerId = $actor->role === 'employer' ? $actor->id : (int) $this->selectedTargetId;
        $candidateId = $actor->role === 'candidate' ? $actor->id : (int) $this->selectedTargetId;

        $linkExists = AccountLink::where('employer_user_id', $employerId)
            ->where('candidate_user_id', $candidateId)
            ->exists();

        $target = User::find($this->selectedTargetId);

        if (! $linkExists || ! $target || ! $target->isVerified()) {
            $this->addError('password', __('This account link is no longer active or verified.'));

            return;
        }

        if (! Hash::check($this->password, $target->password)) {
            RateLimiter::hit($throttleKey, 60);
            $this->addError('password', __('Incorrect password for :email.', ['email' => $target->email]));

            return;
        }

        RateLimiter::clear($throttleKey);

        activity()
            ->causedBy($actor)
            ->performedOn($target)
            ->event('role_switched')
            ->log('Switched portal session from '.$actor->role.' (#'.$actor->id.') to '.$target->role.' (#'.$target->id.')');

        Auth::login($target);
        if (request()->hasSession()) {
            request()->session()->regenerate();
        }

        return $this->redirect(route('dashboard'));
    }

    public function render(): View
    {
        $user = Auth::user();
        $targets = $user ? $user->linkedSwitchTargets() : collect();
        $targetRoleLabel = $user?->role === 'employer' ? __('Candidate') : __('Employer');

        return view('livewire.role-switcher', [
            'user' => $user,
            'targets' => $targets,
            'hasLinkedTarget' => $targets->isNotEmpty(),
            'targetRoleLabel' => $targetRoleLabel,
        ]);
    }
}
