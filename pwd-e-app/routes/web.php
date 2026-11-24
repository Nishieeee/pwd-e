<?php

use App\Http\Controllers\Admin\VerificationDocumentController;
use App\Http\Controllers\Employer\ApplicantDocumentController;
use App\Livewire\Admin\AuditLog;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Lexicon;
use App\Livewire\Admin\Users;
use App\Livewire\Admin\VerificationQueue;
use App\Livewire\Candidate\ApplicationsTracker;
use App\Livewire\Candidate\Apply;
use App\Livewire\Candidate\EmployerShow;
use App\Livewire\Candidate\Dashboard as CandidateDashboard;
use App\Livewire\Candidate\JobMatches;
use App\Livewire\Candidate\ProfileBuilder;
use App\Livewire\Candidate\ShowProfile;
use App\Livewire\Employer\Applicants;
use App\Livewire\Employer\Dashboard as EmployerDashboard;
use App\Livewire\Employer\Jobs\Create;
use App\Livewire\Employer\Jobs\Edit;
use App\Livewire\Employer\Jobs\Index;
use App\Livewire\Employer\Talents;
use App\Livewire\Employer\Workforce;
use App\Livewire\Jobs\PublicListing;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::get('/jobs', PublicListing::class)->name('jobs');
Route::view('/employers', 'employers')->name('employers');
Route::view('/about', 'about')->name('about');

Route::middleware(['auth'])->group(function () {
    Route::get('verification/pending', fn () => view('auth.verification-pending'))->name('verification.notice');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboard::class)->name('dashboard');
    Route::get('users', Users::class)->name('users');
    Route::get('lexicon', Lexicon::class)->name('lexicon');
    Route::get('audit-log', AuditLog::class)->name('audit-log');
    Route::get('verifications', VerificationQueue::class)->name('verifications.index');
    Route::get('verifications/{verificationRequest}/document', [VerificationDocumentController::class, 'show'])->name('verifications.document');
});

Route::middleware(['auth', 'account.verified'])->group(function () {
    Route::get('dashboard', function () {
        $user = Auth::user();
        if ($user instanceof User && $user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if ($user instanceof User && $user->isCandidate()) {
            return app(CandidateDashboard::class)();
        }
        if ($user instanceof User && $user->isEmployer()) {
            return app(EmployerDashboard::class)();
        }

        return view('dashboard');
    })->name('dashboard');
});

Route::middleware(['auth', 'account.verified', 'role:candidate'])->group(function () {
    Route::get('profile', ShowProfile::class)->name('candidate.profile.show');
    Route::get('profile/builder/{step?}', ProfileBuilder::class)->name('candidate.profile.builder');
    Route::get('jobs/matches/{job?}', JobMatches::class)->name('candidate.jobs');
    Route::get('jobs/{job}/apply', Apply::class)->name('candidate.apply');
    Route::get('applications', ApplicationsTracker::class)->name('candidate.applications');
    Route::get('applications/{application}/document/{type}', [ApplicantDocumentController::class, 'showCandidateDocument'])->name('candidate.applications.document');
    Route::get('employers/{employer}', EmployerShow::class)->name('candidate.employers.show');
});

Route::middleware(['auth', 'account.verified', 'role:employer'])->prefix('employer')->name('employer.')->group(function () {
    Route::get('/jobs', Index::class)->name('jobs.index');
    Route::get('/jobs/create', Create::class)->middleware('employer.permit')->name('jobs.create');
    Route::get('/jobs/{job}/edit', Edit::class)->middleware('employer.permit')->name('jobs.edit');
    Route::get('/jobs/{job}/applicants', Applicants::class)->name('jobs.applicants');
    Route::get('/talents', Talents::class)->name('talents');
    Route::get('/applicants/{application}/document/{type}', [ApplicantDocumentController::class, 'show'])->name('applicants.document');
    Route::get('/candidates/{candidateProfile}', ShowProfile::class)->name('candidates.show');
    Route::get('/applicants', Applicants::class)->name('applicants');
    Route::get('/workforce', Workforce::class)->name('workforce');
    Route::get('/profile', \App\Livewire\Employer\Profile::class)->name('profile');
});

require __DIR__.'/settings.php';
