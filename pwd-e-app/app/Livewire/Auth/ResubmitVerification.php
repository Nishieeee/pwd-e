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

    public string $documentType = 'pwd_id';

    public $documentFile = null;

    public $secondaryDocumentFile = null;

    public string $idNumber = '';

    public string $medicalCertificateNumber = '';

    public bool $resubmitted = false;

    public bool $showProactiveUpload = false;

    public function mount(): void
    {
        $user = Auth::user();
        if ($user) {
            if ($user->isCandidate()) {
                $this->documentType = in_array($user->pwd_document_type, ['pwd_id', 'medical_certificate', 'both'], true)
                    ? $user->pwd_document_type
                    : 'pwd_id';
                $this->idNumber = $user->pwd_id_number ?? '';
                $this->medicalCertificateNumber = $user->medical_certificate_number ?? '';
            } else {
                $this->documentType = 'business_permit';
                $this->idNumber = $user->employerProfile?->business_permit_number ?? '';
            }
        }
    }

    public function checkStatus(): void
    {
        $user = Auth::user()?->fresh();
        if (! $user) {
            return;
        }

        if ($user->isVerified()) {
            $this->redirect(route('dashboard'), navigate: false);

            return;
        }

        $latest = $user->verificationRequests()->latest('id')->first();
        if ($latest && in_array($latest->status, ['needs_more_docs', 'rejected'], true)) {
            $this->resubmitted = false;
        }
    }

    public function toggleProactiveUpload(): void
    {
        $this->showProactiveUpload = ! $this->showProactiveUpload;
    }

    public function submit(): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(401);
        }

        $isCandidate = $user->isCandidate();

        $rules = [
            'idNumber' => ['nullable', 'string', 'max:100'],
        ];

        if ($isCandidate) {
            $rules['documentType'] = ['required', 'string', 'in:pwd_id,medical_certificate,both'];
            $rules['medicalCertificateNumber'] = ['nullable', 'string', 'max:100'];

            if ($this->documentType === 'both') {
                $rules['documentFile'] = ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120', 'required_without:secondaryDocumentFile'];
                $rules['secondaryDocumentFile'] = ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120', 'required_without:documentFile'];
            } else {
                $rules['documentFile'] = ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'];
            }
        } else {
            $rules['documentFile'] = ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'];
        }

        $this->validate($rules);

        if ($isCandidate) {
            $type = $this->documentType;
            $pwdIdPath = $user->pwd_id_path;
            $medCertPath = $user->medical_certificate_path;

            if ($type === 'medical_certificate') {
                $medCertPath = $this->documentFile->store('documents/medical_certificates', 'private');
            } elseif ($type === 'both') {
                if ($this->documentFile) {
                    $pwdIdPath = $this->documentFile->store('documents/pwd_ids', 'private');
                }
                if ($this->secondaryDocumentFile) {
                    $medCertPath = $this->secondaryDocumentFile->store('documents/medical_certificates', 'private');
                }
            } else {
                $pwdIdPath = $this->documentFile->store('documents/pwd_ids', 'private');
            }

            $resolvedIdNumber = $this->idNumber ?: $user->pwd_id_number;
            $resolvedMedNumber = $this->medicalCertificateNumber
                ?: ($type === 'medical_certificate' ? ($this->idNumber ?: $user->medical_certificate_number) : $user->medical_certificate_number);

            $user->update([
                'pwd_document_type' => $type,
                'pwd_id_number' => $resolvedIdNumber,
                'pwd_id_path' => $pwdIdPath,
                'medical_certificate_number' => $resolvedMedNumber,
                'medical_certificate_path' => $medCertPath,
                'status' => 'pending',
            ]);

            $primaryDocPath = $type === 'medical_certificate' ? $medCertPath : $pwdIdPath;
            $secondaryDocPath = $type === 'both' ? $medCertPath : null;
        } else {
            $type = 'business_permit';
            $primaryDocPath = $this->documentFile->store('verifications/business_permits', 'private');
            $secondaryDocPath = null;
            $resolvedIdNumber = $this->idNumber ?: $user->employerProfile?->business_permit_number;

            $user->update(['status' => 'pending']);
            if ($user->employerProfile) {
                $user->employerProfile->update([
                    'business_permit_number' => $resolvedIdNumber,
                    'business_permit_path' => $primaryDocPath,
                ]);
            }
        }

        $existingActiveVr = VerificationRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'needs_more_docs'])
            ->latest('id')
            ->first();

        if ($existingActiveVr) {
            $existingActiveVr->update([
                'type' => $type,
                'id_number' => $resolvedIdNumber ?: null,
                'document_path' => $primaryDocPath,
                'secondary_document_path' => $secondaryDocPath,
                'status' => 'pending',
                'rejection_reason' => null,
            ]);
            $vr = $existingActiveVr;
        } else {
            $vr = VerificationRequest::create([
                'user_id' => $user->id,
                'type' => $type,
                'id_number' => $resolvedIdNumber ?: null,
                'document_path' => $primaryDocPath,
                'secondary_document_path' => $secondaryDocPath,
                'status' => 'pending',
            ]);
        }

        activity()
            ->causedBy($user)
            ->performedOn($vr)
            ->event('resubmitted')
            ->log('Resubmitted verification request #' . $vr->id . ' after prior review');

        $this->documentFile = null;
        $this->secondaryDocumentFile = null;
        $this->resubmitted = true;
        $this->showProactiveUpload = false;
        session()->flash('status', 'Your verification documents have been submitted for administrator review.');
    }

    public function render(): View
    {
        $user = Auth::user()?->fresh();
        $latestRequest = $user?->verificationRequests()->latest('id')->first();
        $status = $latestRequest?->status ?? $user?->status ?? 'pending';
        $reason = $latestRequest?->rejection_reason;

        return view('livewire.auth.resubmit-verification', [
            'user' => $user,
            'latestRequest' => $latestRequest,
            'status' => $status,
            'reason' => $reason,
        ]);
    }
}
