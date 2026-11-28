<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string|null $first_name
 * @property string|null $middle_name
 * @property string|null $last_name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'first_name', 'middle_name', 'last_name', 'email', 'phone', 'password', 'role', 'status', 'verified_at', 'pwd_id_number', 'pwd_id_path', 'medical_certificate_number', 'medical_certificate_path', 'pwd_document_type', 'display_settings'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'verified_at' => 'datetime',
            'pwd_id_number' => 'encrypted',
            'medical_certificate_number' => 'encrypted',
            'display_settings' => 'array',
        ];
    }

    public function isMedCertVerified(): bool
    {
        return $this->isCandidate() && $this->isVerified() && in_array($this->pwd_document_type, ['medical_certificate', 'both'], true);
    }

    public function isPwdIdVerified(): bool
    {
        return $this->isCandidate() && $this->isVerified() && (in_array($this->pwd_document_type, ['pwd_id', 'both'], true) || empty($this->pwd_document_type));
    }

    public function candidateProfile()
    {
        return $this->hasOne(CandidateProfile::class);
    }

    public function employerProfile()
    {
        return $this->hasOne(EmployerProfile::class);
    }

    public function verificationRequests()
    {
        return $this->hasMany(VerificationRequest::class);
    }

    public function employerAccountLinks()
    {
        return $this->hasMany(AccountLink::class, 'employer_user_id');
    }

    public function candidateAccountLinks()
    {
        return $this->hasMany(AccountLink::class, 'candidate_user_id');
    }

    /**
     * Active, verified counterpart accounts available for password-gated role switching.
     *
     * @return \Illuminate\Support\Collection<int, User>
     */
    public function linkedSwitchTargets()
    {
        if ($this->role === 'employer') {
            return $this->employerAccountLinks()
                ->with('candidateUser.candidateProfile')
                ->get()
                ->pluck('candidateUser')
                ->filter(fn (?User $u) => $u !== null && $u->role === 'candidate' && $u->isVerified())
                ->values();
        }

        if ($this->role === 'candidate') {
            return $this->candidateAccountLinks()
                ->with('employerUser.employerProfile')
                ->get()
                ->pluck('employerUser')
                ->filter(fn (?User $u) => $u !== null && $u->role === 'employer' && $u->isVerified())
                ->values();
        }

        return collect();
    }

    public function notificationPreferences()
    {
        return $this->hasMany(NotificationPreference::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null && $this->status === 'active';
    }

    public function isEmployer(): bool
    {
        return $this->role === 'employer' || $this->hasRole('employer');
    }

    public function isCandidate(): bool
    {
        return $this->role === 'candidate' || $this->hasRole('candidate');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->hasRole('admin');
    }

    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }
}
