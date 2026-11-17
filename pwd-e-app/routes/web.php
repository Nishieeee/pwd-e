<?php

use App\Livewire\Candidate\ApplicationsTracker;
use App\Livewire\Candidate\Dashboard;
use App\Livewire\Candidate\JobMatches;
use App\Livewire\Candidate\ProfileBuilder;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::view('/jobs', 'jobs')->name('jobs');
Route::view('/employers', 'employers')->name('employers');
Route::view('/about', 'about')->name('about');

Route::middleware(['auth'])->group(function () {
    Route::get('verification/pending', fn () => view('auth.verification-pending'))->name('verification.notice');
});

Route::middleware(['auth', 'account.verified'])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('profile/builder/{step?}', ProfileBuilder::class)->name('candidate.profile.builder');
    Route::get('jobs/matches/{job?}', JobMatches::class)->name('candidate.jobs');
    Route::get('applications', ApplicationsTracker::class)->name('candidate.applications');
});

require __DIR__.'/settings.php';
