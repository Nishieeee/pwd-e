<?php

use App\Http\Controllers\Admin\VerificationDocumentController;
use App\Livewire\Admin\AuditLog;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Lexicon;
use App\Livewire\Admin\Users;
use App\Livewire\Admin\VerificationQueue;
use App\Livewire\Employer\Jobs\Create;
use App\Livewire\Employer\Jobs\Edit;
use App\Livewire\Employer\Jobs\Index;
use App\Livewire\Jobs\PublicListing;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::get('/jobs', PublicListing::class)->name('jobs');
Route::view('/employers', 'employers')->name('employers');
Route::view('/about', 'about')->name('about');

Route::middleware(['auth'])->group(function () {
    Route::get('verification/pending', fn () => view('auth.verification-pending'))->name('verification.notice');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('users', Users::class)->name('users');
    Route::get('lexicon', Lexicon::class)->name('lexicon');
    Route::get('audit-log', \App\Livewire\Admin\AuditLog::class)->name('audit-log');
    Route::get('verifications', VerificationQueue::class)->name('verifications.index');
    Route::get('verifications/{verificationRequest}/document', [VerificationDocumentController::class, 'show'])->name('verifications.document');
});

Route::middleware(['auth', 'account.verified'])->group(function () {
    Route::get('dashboard', function () {
        if (auth()->user()?->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('dashboard');
    })->name('dashboard');

    Route::prefix('employer')->name('employer.')->group(function () {
        Route::get('/jobs', Index::class)->name('jobs.index');
        Route::get('/jobs/create', Create::class)->name('jobs.create');
        Route::get('/jobs/{job}/edit', Edit::class)->name('jobs.edit');
    });
});

require __DIR__.'/settings.php';
