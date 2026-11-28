<?php

namespace App\Livewire\Candidate\Concerns;

use App\Models\Application;
use App\Models\ApplicationMessage;
use App\Models\WorkforceRecord;
use App\Support\EmploymentScheduleHelper;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

trait HandlesCandidateOfferDecisions
{
    public ?int $confirmingMultiJobAppId = null;

    public bool $dismissedMultiOfferPopup = false;

    /**
     * Attempt to accept an offer. If the candidate already holds an active job,
     * prompt for explicit multi-job confirmation first (REV-14).
     */
    public function acceptOffer(int $appId, bool $forceConfirm = false): void
    {
        $user = Auth::user();
        $candidateProfile = $user?->candidateProfile;
        if (! $candidateProfile) {
            return;
        }

        $app = Application::where('id', $appId)
            ->where('candidate_profile_id', $candidateProfile->id)
            ->with(['jobPosting.employerProfile'])
            ->first();

        if (! $app || ! $app->canTransitionTo(Application::STATUS_HIRED)) {
            if (property_exists($this, 'actionNotification')) {
                $this->actionNotification = 'Unable to accept offer in current state.';
            }

            return;
        }

        $activeJobs = EmploymentScheduleHelper::activeEmployments($candidateProfile->id);
        if ($activeJobs->isNotEmpty() && ! $forceConfirm) {
            $this->confirmingMultiJobAppId = $app->id;

            return;
        }

        $this->confirmingMultiJobAppId = null;
        $this->finalizeOfferAcceptance($app);
    }

    public function confirmAcceptAdditionalJob(): void
    {
        if (! $this->confirmingMultiJobAppId) {
            return;
        }

        $appId = $this->confirmingMultiJobAppId;
        $this->acceptOffer($appId, true);
    }

    public function cancelMultiJobConfirmation(): void
    {
        $this->confirmingMultiJobAppId = null;
    }

    public function ignoreOfferInPopup(int $appId): void
    {
        $candidateProfile = Auth::user()?->candidateProfile;
        if (! $candidateProfile) {
            return;
        }

        $app = Application::where('id', $appId)
            ->where('candidate_profile_id', $candidateProfile->id)
            ->where('status', Application::STATUS_OFFERED)
            ->first();

        if ($app) {
            $app->update(['multi_offer_ignored_at' => now()]);
        }
    }

    public function dismissMultiOfferModal(): void
    {
        $this->dismissedMultiOfferPopup = true;
    }

    protected function finalizeOfferAcceptance(Application $app): void
    {
        $user = Auth::user();
        $candidateProfile = $user?->candidateProfile;

        $app->update([
            'status' => Application::STATUS_HIRED,
            'offer_accepted_at' => now(),
            'status_subtext' => Application::computeStatusSubtext(Application::STATUS_HIRED, now()),
        ]);

        $salary = (float) ($app->offer_details['salary'] ?? ($app->jobPosting?->salary_max ?? $app->jobPosting?->salary_min ?? 35000));
        $payPeriod = $app->offer_details['pay_period'] ?? 'monthly';
        $annualWage = match ($payPeriod) {
            'monthly' => $salary * 12,
            'annual' => $salary,
            default => $salary,
        };

        $startDate = ! empty($app->offer_details['start_date']) ? Carbon::parse($app->offer_details['start_date']) : now();
        $isPwd = ! empty($candidateProfile?->user?->pwd_id_number);

        WorkforceRecord::firstOrCreate(
            ['application_id' => $app->id],
            [
                'employer_profile_id' => $app->jobPosting->employer_profile_id,
                'candidate_profile_id' => $app->candidate_profile_id,
                'hired_at' => $startDate,
                'pwd_verified' => $isPwd,
                'annual_wage' => $annualWage,
            ]
        );

        ApplicationMessage::create([
            'application_id' => $app->id,
            'sender_id' => $user->id,
            'type' => 'system',
            'body' => 'Offer Accepted: Candidate has formally accepted the job offer!',
            'metadata' => [
                'event' => 'offer_accepted',
                'accepted_at' => now()->toIso8601String(),
            ],
        ]);

        if (method_exists($this, 'closeOfferModal')) {
            $this->closeOfferModal();
        }

        $company = $app->jobPosting?->employerProfile?->company_name ?? 'the employer';
        $msg = "Congratulations! You have accepted the job offer from {$company}.";
        if (property_exists($this, 'actionNotification')) {
            $this->actionNotification = $msg;
        }
        if (property_exists($this, 'calendarDownloadSuccess')) {
            $this->calendarDownloadSuccess = $msg;
        }

        $employerUser = $app->jobPosting?->employerProfile?->user;
        if ($employerUser) {
            $employerUser->notify(
                new \App\Notifications\ApplicationStatusChanged(
                    $app->jobPosting?->title ?? 'Position',
                    'offer_accepted',
                    "{$user->name} has accepted the job offer!"
                )
            );
        }
    }

    /**
     * Build view data for the Multi-Offer Notification Popup & Already-Employed Confirmation Modal.
     *
     * @return array{
     *     concurrentOffers: Collection<int, Application>,
     *     showMultiOfferPopup: bool,
     *     multiOfferHasScheduleOverlap: bool,
     *     confirmingMultiJobApp: ?Application,
     *     activeEmploymentsForConfirm: Collection<int, WorkforceRecord>,
     *     multiJobHasScheduleOverlap: bool
     * }
     */
    protected function buildOfferModalsViewData(?int $candidateProfileId): array
    {
        if (! $candidateProfileId) {
            return [
                'concurrentOffers' => collect(),
                'showMultiOfferPopup' => false,
                'multiOfferHasScheduleOverlap' => false,
                'confirmingMultiJobApp' => null,
                'activeEmploymentsForConfirm' => collect(),
                'multiJobHasScheduleOverlap' => false,
            ];
        }

        $unignoredOffers = Application::with(['jobPosting.employerProfile', 'jobPosting.branch'])
            ->where('candidate_profile_id', $candidateProfileId)
            ->where('status', Application::STATUS_OFFERED)
            ->whereNull('multi_offer_ignored_at')
            ->orderByDesc('offer_extended_at')
            ->get();

        $multiOverlap = false;
        if ($unignoredOffers->count() >= 2) {
            $items = $unignoredOffers->values();
            for ($i = 0; $i < $items->count(); $i++) {
                for ($j = $i + 1; $j < $items->count(); $j++) {
                    if (EmploymentScheduleHelper::schedulesOverlap($items[$i]->offer_details ?? [], $items[$j]->offer_details ?? [])) {
                        $multiOverlap = true;
                        break 2;
                    }
                }
            }
        }

        $confirmingApp = $this->confirmingMultiJobAppId
            ? Application::with(['jobPosting.employerProfile'])->where('candidate_profile_id', $candidateProfileId)->find($this->confirmingMultiJobAppId)
            : null;

        $activeEmployments = $confirmingApp
            ? EmploymentScheduleHelper::activeEmployments($candidateProfileId)
            : collect();

        $multiJobOverlap = false;
        if ($confirmingApp && $activeEmployments->isNotEmpty()) {
            foreach ($activeEmployments as $rec) {
                if (EmploymentScheduleHelper::schedulesOverlap($confirmingApp->offer_details ?? [], $rec->application?->offer_details ?? [])) {
                    $multiJobOverlap = true;
                    break;
                }
            }
        }

        return [
            'concurrentOffers' => $unignoredOffers,
            'showMultiOfferPopup' => ! $this->dismissedMultiOfferPopup && $unignoredOffers->count() >= 2 && ! $confirmingApp,
            'multiOfferHasScheduleOverlap' => $multiOverlap,
            'confirmingMultiJobApp' => $confirmingApp,
            'activeEmploymentsForConfirm' => $activeEmployments,
            'multiJobHasScheduleOverlap' => $multiJobOverlap,
        ];
    }
}
