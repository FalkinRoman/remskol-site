<?php

use App\Http\Controllers\LandingController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TelegramWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/uslugi/{slug}', [PageController::class, 'service'])
    ->whereIn('slug', array_keys(config('catalog.services')))
    ->name('service');
Route::get('/primery-rabot', [PageController::class, 'works'])->name('works');
Route::get('/ceny', [PageController::class, 'prices'])->name('prices');
Route::get('/kontakty', [PageController::class, 'contacts'])->name('contacts');
Route::get('/privacy', [LandingController::class, 'privacy'])->name('privacy');
Route::get('/consent', [LandingController::class, 'consent'])->name('consent');
Route::get('/sitemap.xml', [LandingController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [LandingController::class, 'robots'])->name('robots');

Route::post('/lead', [LeadController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('lead.store');

Route::post('/telegram/webhook', TelegramWebhookController::class)
    ->middleware('throttle:60,1')
    ->name('telegram.webhook');
