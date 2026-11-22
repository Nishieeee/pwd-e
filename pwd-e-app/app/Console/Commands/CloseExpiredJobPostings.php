<?php

namespace App\Console\Commands;

use App\Models\JobPosting;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CloseExpiredJobPostings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:close-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically close published job postings past their deadline';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = Carbon::today();

        $expiredJobs = JobPosting::where('status', 'published')
            ->whereNotNull('closes_at')
            ->whereDate('closes_at', '<', $today)
            ->get();

        $count = 0;

        foreach ($expiredJobs as $job) {
            $job->update([
                'status' => 'closed',
            ]);

            if (function_exists('activity')) {
                activity('job_posting')
                    ->performedOn($job)
                    ->log("Job posting #{$job->id} auto-closed due to passing deadline ({$job->closes_at->format('Y-m-d')})");
            }

            $count++;
        }

        $this->info("Auto-closed {$count} expired job posting(s).");

        return self::SUCCESS;
    }
}
