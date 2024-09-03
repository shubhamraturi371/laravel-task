<?php

use App\Http\Controllers\Campaign\CampaignController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware('auth')->group(function () {
    Route::post('upload-campaign',[CampaignController::class,'store'])->name('upload-campaign');
    Route::get('batch',[CampaignController::class,'index'])->name('list-campaign');


});

require __DIR__.'/auth.php';
