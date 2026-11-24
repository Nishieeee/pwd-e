<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Recruitment & Job Application System Configurations
    |--------------------------------------------------------------------------
    |
    | Defines system-wide policy limits for job postings, talent invitations,
    | and candidate re-application rules.
    |
    */

    'max_active_job_postings' => env('RECRUITMENT_MAX_ACTIVE_JOB_POSTINGS', 5),

    'max_daily_invitations' => env('RECRUITMENT_MAX_DAILY_INVITATIONS', 25),

    'allow_reapplication_after_days' => env('RECRUITMENT_REAPPLICATION_COOLDOWN_DAYS', 0),
];
