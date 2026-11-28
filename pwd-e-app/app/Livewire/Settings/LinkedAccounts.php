<?php

namespace App\Livewire\Settings;

use App\Models\AccountLink;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Linked role accounts')]
class LinkedAccounts extends Component
{
    public string $email = '';

    public string $password = '';

    public ?string $statusMessage = null;

    public function mount(): void
    {
        $user = Auth::user();
        if (! $user || $user->isAdmin()) {
            abort(403, 'Account role linking is only available for Candidate and Employer accounts.');
        }
    }

    public function linkAccount(): void
    {
        $this->statusMessage = null;
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $actor = Auth::user();
        if (! $actor || ! in_array($actor->role, ['candidate', 'employer'], true)) {
            abort(403);
        }

        $throttleKey = 'link-account:'.$actor->id.':'.request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('password', __('Too many attempts. Please try again in :seconds seconds.', ['seconds' => $seconds]));

            return;
        }

        $expectedTargetRole = $actor->role === 'employer' ? 'candidate' : 'employer';
        $normalizedEmail = strtolower(trim($this->email));

        $target = User::whereRaw('LOWER(email) = ?', [$normalizedEmail])->first();

        if (! $target || $target->id === $actor->id || ! Hash::check($this->password, $target->password)) {
            RateLimiter::hit($throttleKey, 60);
            $this->addError('email', __('The provided credentials do not match an existing account.'));

            return;
        }

        if ($target->role !== $expectedTargetRole) {
            RateLimiter::hit($throttleKey, 60);
            $this->addError('email', $expectedTargetRole === 'candidate'
                ? __('Only a PWD Candidate account can be linked from an Employer account.')
                : __('Only a Company Employer account can be linked from a Candidate account.'));

            return;
        }

        if (! $target->isVerified()) {
            $this->addError('email', __('That account has not been verified by an administrator yet. Only verified accounts can be linked.'));

            return;
        }

        RateLimiter::clear($throttleKey);

        $employerId = $actor->role === 'employer' ? $actor->id : $target->id;
        $candidateId = $actor->role === 'candidate' ? $actor->id : $target->id;

        $link = AccountLink::firstOrCreate(
            [
                'employer_user_id' => $employerId,
                'candidate_user_id' => $candidateId,
            ],
            [
                'linked_by_user_id' => $actor->id,
            ]
        );

        activity()
            ->causedBy($actor)
            ->performedOn($target)
            ->event('account_linked')
            ->log('Linked '.$actor->role.' account #'.$actor->id.' with '.$target->role.' account #'.$target->id);

        $this->reset(['email', 'password']);
        $this->statusMessage = $link->wasRecentlyCreated
            ? __('Account linked successfully. You can now switch roles from the portal header using that account\'s password.')
            : __('This account is already linked.');

        Flux::toast(variant: 'success', text: $this->statusMessage);
    }

    public function unlinkAccount(int $linkId): void
    {
        $this->statusMessage = null;
        $actor = Auth::user();
        if (! $actor) {
            abort(403);
        }

        $link = AccountLink::with(['employerUser', 'candidateUser'])->findOrFail($linkId);

        if ($link->employer_user_id !== $actor->id && $link->candidate_user_id !== $actor->id) {
            abort(403, 'Unauthorized to modify this account link.');
        }

        $counterpart = $link->employer_user_id === $actor->id ? $link->candidateUser : $link->employerUser;
        $link->delete();

        if ($counterpart) {
            activity()
                ->causedBy($actor)
                ->performedOn($counterpart)
                ->event('account_unlinked')
                ->log('Unlinked '.$actor->role.' account #'.$actor->id.' from account #'.$counterpart->id);
        }

        $this->statusMessage = __('Account unlinked. Role switching to that account has been revoked immediately.');
        Flux::toast(variant: 'success', text: $this->statusMessage);
    }

    public function render(): View
    {
        $user = Auth::user();
        $links = $user->role === 'employer'
            ? $user->employerAccountLinks()->with('candidateUser.candidateProfile')->latest()->get()
            : $user->candidateAccountLinks()->with('employerUser.employerProfile')->latest()->get();

        return view('livewire.settings.linked-accounts', [
            'user' => $user,
            'links' => $links,
            'targetRoleLabel' => $user->role === 'employer' ? __('PWD Candidate') : __('Employer Company'),
        ]);
    }
}
