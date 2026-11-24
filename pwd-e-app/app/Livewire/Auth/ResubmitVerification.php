<?php

namespace App\Livewire\Auth;

use App\Models\VerificationRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class ResubmitVerification extends Component
{
    use WithFileUploads;

    public $documentFile = null;

    public string $idNumber = '';

    public bool $resubmitted = false;

    public function mount(): void
    {
        $user = Auth::user();
        if ($user) {
            $this->idNumber = $user->isCandidate()
                ? ($user->pwd_id_number ?? '')
                : ($user->employerProfile?->business_permit_number ?? '');
        }
    }

    public function submit(): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(401);
        }

        $this->validate([
            'documentFile' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'idNumber' => ['nullable', 'string', 'max:100'],
        ]);

        $isCandidate = $user->isCandidate();
        $type = $isCandidate ? 'pwd_id' : 'business_permit';
        $folder = $isCandidate ? 'verifications/pwd_ids' : 'verifications/business_permits';

        $path = $this->documentFile->store($folder, 'private');

        if ($isCandidate) {
            $user->update([
                'pwd_id_number' => $this->idNumber ?: $user->pwd_id_number,
                'pwd_id_path' => $path,
                'status' => 'pending',
            ]);
        } else {
            $user->update(['status' => 'pending']);
            if ($user->employerProfile) {
                $user->employerProfile->update([
                    'business_permit_number' => $this->idNumber ?: $user->employerProfile->business_permit_number,
                    'business_permit_path' => $path,
                ]);
            }
        }

        $vr = VerificationRequest::create([
            'user_id' => $user->id,
            'type' => $type,
            'id_number' => $this->idNumber ?: null,
            'document_path' => $path,
            'status' => 'pending',
        ]);

        activity()
            ->causedBy($user)
            ->performedOn($vr)
            ->event('resubmitted')
            ->log('Resubmitted verification request #' . $vr->id . ' after prior review');

        $this->resubmitted = true;
        session()->flash('status', 'Your verification documents have been re-submitted for administrator review.');
    }

    public function render(): View
    {
        return view('livewire.auth.resubmit-verification', [
            'user' => Auth::user(),
        ]);
    }
}
