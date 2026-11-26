<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\CandidateProfile;
use App\Models\EmployerProfile;
use App\Models\User;
use App\Models\VerificationRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    public function create(array $input): User
    {
        $role = $input['role'] ?? null;

        $rules = [
            'role' => ['required', 'string', 'in:candidate,employer'],
            'email' => $this->emailRules(),
            'password' => $this->passwordRules(),
            'terms' => ['accepted'],
        ];

        if ($role === 'candidate') {
            $rules['first_name'] = ['required', 'string', 'max:100'];
            $rules['last_name'] = ['required', 'string', 'max:100'];
            $rules['middle_name'] = ['nullable', 'string', 'max:100'];
            $rules['disability_type'] = ['required', 'string', 'max:100'];
            $rules['pwd_document_type'] = ['nullable', 'string', 'in:pwd_id,medical_certificate,both'];
            $rules['pwd_id_number'] = ['required', 'string', 'max:50'];
            $rules['medical_certificate_number'] = ['nullable', 'string', 'max:50'];
            $rules['pwd_id_file'] = ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'];
            $rules['medical_certificate_file'] = ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'];
            $rules['consent'] = ['accepted'];
            $rules['date_of_birth'] = ['required', 'date', 'before_or_equal:'.now()->subYears(18)->toDateString()];
        } elseif ($role === 'employer') {
            $rules['company_name'] = ['required', 'string', 'max:255'];
            $rules['first_name'] = ['nullable', 'string', 'max:100'];
            $rules['last_name'] = ['nullable', 'string', 'max:100'];
            $rules['middle_name'] = ['nullable', 'string', 'max:100'];
            $rules['business_permit_number'] = ['nullable', 'string', 'max:100'];
            $rules['business_permit_file'] = ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'];
        }

        Validator::make($input, $rules)->validate();

        $request = request();

        return DB::transaction(function () use ($input, $role, $request) {
            $isCandidate = $role === 'candidate';
            $pwdDocType = $isCandidate ? ($input['pwd_document_type'] ?? 'pwd_id') : 'pwd_id';

            $pwdIdUpload = $request->file('pwd_id_file')
                ?? (($input['pwd_id_file'] ?? null) instanceof \Illuminate\Http\UploadedFile ? $input['pwd_id_file'] : null);
            $medCertUpload = $request->file('medical_certificate_file')
                ?? (($input['medical_certificate_file'] ?? null) instanceof \Illuminate\Http\UploadedFile ? $input['medical_certificate_file'] : null);
            $permitUpload = $request->file('business_permit_file')
                ?? (($input['business_permit_file'] ?? null) instanceof \Illuminate\Http\UploadedFile ? $input['business_permit_file'] : null);

            $pwdIdPath = null;
            $medCertPath = null;
            if ($isCandidate) {
                if ($pwdDocType === 'medical_certificate') {
                    $medCertPath = ($medCertUpload ?? $pwdIdUpload)?->store('documents/medical_certificates', 'private');
                } elseif ($pwdDocType === 'both') {
                    $pwdIdPath = $pwdIdUpload?->store('documents/pwd_ids', 'private');
                    $medCertPath = $medCertUpload?->store('documents/medical_certificates', 'private');
                } else {
                    $pwdIdPath = $pwdIdUpload?->store('documents/pwd_ids', 'private');
                }
            }
            $permitPath = $permitUpload?->store('documents/permits', 'private');

            $firstName = !empty($input['first_name'])
                ? $input['first_name']
                : ($role === 'employer' ? $input['company_name'] : '');
            $lastName = !empty($input['last_name'])
                ? $input['last_name']
                : ($role === 'employer' ? 'Representative' : '');

            $fullName = trim(implode(' ', array_filter([
                $firstName,
                $input['middle_name'] ?? '',
                $lastName,
            ])));

            $medCertNumber = $isCandidate
                ? ($input['medical_certificate_number'] ?? ($pwdDocType === 'medical_certificate' ? ($input['pwd_id_number'] ?? null) : null))
                : null;

            $user = User::create([
                'name' => $fullName,
                'first_name' => $firstName,
                'middle_name' => $input['middle_name'] ?? null,
                'last_name' => $lastName,
                'email' => $input['email'],
                'password' => $input['password'],
                'role' => $role,
                'status' => $isCandidate ? 'pending' : 'active',
                'verified_at' => $isCandidate ? null : now(),
                'pwd_id_number' => $isCandidate ? ($input['pwd_id_number'] ?? null) : null,
                'pwd_id_path' => $pwdIdPath,
                'medical_certificate_number' => $medCertNumber,
                'medical_certificate_path' => $medCertPath,
                'pwd_document_type' => $pwdDocType,
            ]);

            $user->assignRole($role);

            if ($isCandidate) {
                CandidateProfile::create([
                    'user_id' => $user->id,
                    'disability_type' => $input['disability_type'],
                    'date_of_birth' => $input['date_of_birth'] ?? null,
                ]);
                VerificationRequest::create([
                    'user_id' => $user->id,
                    'type' => $pwdDocType,
                    'id_number' => $input['pwd_id_number'] ?? null,
                    'document_path' => $pwdDocType === 'medical_certificate' ? $medCertPath : $pwdIdPath,
                    'secondary_document_path' => $pwdDocType === 'both' ? $medCertPath : null,
                    'status' => 'pending',
                ]);
            } else {
                EmployerProfile::create([
                    'user_id' => $user->id,
                    'company_name' => $input['company_name'],
                    'business_permit_number' => $input['business_permit_number'] ?? null,
                    'business_permit_path' => $permitPath,
                ]);
            }

            return $user;
        });
    }
}
