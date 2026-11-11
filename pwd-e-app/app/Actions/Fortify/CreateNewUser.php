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
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ];

        if ($role === 'candidate') {
            $rules['disability_type'] = ['required', 'string', 'max:100'];
            $rules['pwd_id_number'] = ['required', 'string', 'max:50'];
            $rules['pwd_id_file'] = ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'];
            $rules['consent'] = ['accepted'];
        } elseif ($role === 'employer') {
            $rules['company_name'] = ['required', 'string', 'max:255'];
            $rules['business_permit_number'] = ['required', 'string', 'max:100'];
            $rules['business_permit_file'] = ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'];
        }

        Validator::make($input, $rules)->validate();

        $request = request();

        return DB::transaction(function () use ($input, $role, $request) {
            $pwdIdPath = $request->file('pwd_id_file')?->store('documents/pwd_ids', 'private');
            $permitPath = $request->file('business_permit_file')?->store('documents/permits', 'private');

            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
                'role' => $role,
                'status' => 'pending',
                'verified_at' => null,
                'pwd_id_number' => $role === 'candidate' ? ($input['pwd_id_number'] ?? null) : null,
                'pwd_id_path' => $pwdIdPath,
            ]);

            $user->assignRole($role);

            if ($role === 'candidate') {
                CandidateProfile::create([
                    'user_id' => $user->id,
                    'disability_type' => $input['disability_type'],
                ]);
                VerificationRequest::create([
                    'user_id' => $user->id,
                    'type' => 'pwd_id',
                    'id_number' => $input['pwd_id_number'] ?? null,
                    'document_path' => $pwdIdPath,
                    'status' => 'pending',
                ]);
            } else {
                EmployerProfile::create([
                    'user_id' => $user->id,
                    'company_name' => $input['company_name'],
                    'business_permit_number' => $input['business_permit_number'],
                    'business_permit_path' => $permitPath,
                ]);
                VerificationRequest::create([
                    'user_id' => $user->id,
                    'type' => 'business_permit',
                    'id_number' => $input['business_permit_number'] ?? null,
                    'document_path' => $permitPath,
                    'status' => 'pending',
                ]);
            }

            return $user;
        });
    }
}
