<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationRequest extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'id_number',
        'document_path',
        'secondary_document_path',
        'status',
        'rejection_reason',
        'verification_checklist',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'id_number' => 'encrypted',
        'verification_checklist' => 'array',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Structured verification criteria grouped by credential type (REV-10).
     *
     * @return array<string, array{title: string, badge: string, items: array<string, string>}>
     */
    public function requiredChecklistSections(): array
    {
        $sections = [];

        if (in_array($this->type, ['pwd_id', 'both'], true)) {
            $sections['pwd_id'] = [
                'title' => 'Government PWD ID Card Checklist',
                'badge' => 'PWD ID',
                'items' => [
                    'pwd_id_format' => 'Valid LGU / NCDA PWD ID number format and issuing office verified',
                    'pwd_id_identity' => 'Applicant full name and ID photo are clear and match account details',
                    'pwd_id_disability' => 'Disability category on card aligns with declared disability type(s)',
                    'pwd_id_validity' => 'Authorized signature / seal present and card is within validity period',
                ],
            ];
        }

        if (in_array($this->type, ['medical_certificate', 'both'], true)) {
            $sections['medical_certificate'] = [
                'title' => 'Clinical Medical Certificate Checklist',
                'badge' => 'Med Cert',
                'items' => [
                    'med_cert_physician' => 'Attending physician name and valid PRC license number are legible',
                    'med_cert_clinical' => 'Clinical diagnosis / Fit-to-Work assessment confirms PWD eligibility',
                    'med_cert_identity' => 'Patient name matches applicant profile and issue date is current',
                    'med_cert_signature' => 'Signed on official clinic or hospital letterhead',
                ],
            ];
        }

        if ($this->type === 'business_permit' || empty($sections)) {
            $sections['business_permit'] = [
                'title' => 'Employer Business Permit Checklist',
                'badge' => 'Business Permit',
                'items' => [
                    'permit_number' => 'Business permit / SEC / DTI registration number is legible and valid',
                    'permit_company' => 'Registered business name and address match employer profile',
                    'permit_validity' => 'Permit is current and issued by the appropriate LGU or agency',
                ],
            ];
        }

        return $sections;
    }

    /**
     * @return list<string>
     */
    public function requiredChecklistKeys(): array
    {
        $keys = [];
        foreach ($this->requiredChecklistSections() as $section) {
            foreach (array_keys($section['items']) as $key) {
                $keys[] = $key;
            }
        }

        return $keys;
    }

    /**
     * @param  array<string, mixed>|list<string>  $checked
     */
    public function hasCompleteChecklist(array $checked): bool
    {
        $required = $this->requiredChecklistKeys();
        if (empty($required)) {
            return true;
        }

        $truthyKeys = [];
        foreach ($checked as $k => $v) {
            if (is_int($k) && is_string($v)) {
                $truthyKeys[] = $v;
            } elseif ($v === true || $v === 'true' || $v === 1 || $v === '1') {
                $truthyKeys[] = (string) $k;
            }
        }

        return count(array_diff($required, $truthyKeys)) === 0;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
