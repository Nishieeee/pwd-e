<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::get('/jobs', \App\Livewire\Jobs\PublicListing::class)->name('jobs');
Route::view('/employers', 'employers')->name('employers');
Route::view('/about', 'about')->name('about');

Route::middleware(['auth'])->group(function () {
    Route::get('verification/pending', fn () => view('auth.verification-pending'))->name('verification.notice');
});

Route::middleware(['auth', 'account.verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::prefix('employer')->name('employer.')->group(function () {
        Route::get('/jobs', \App\Livewire\Employer\Jobs\Index::class)->name('jobs.index');
        Route::get('/jobs/create', \App\Livewire\Employer\Jobs\Create::class)->name('jobs.create');
        Route::get('/jobs/{job}/edit', \App\Livewire\Employer\Jobs\Edit::class)->name('jobs.edit');
    });
});

require __DIR__.'/settings.php';
