<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect('/dashboard'));

// Панели
Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index']);
Route::get('/iss',       [\App\Http\Controllers\IssController::class,       'index']);
Route::get('/jwst',      [\App\Http\Controllers\JwstController::class,      'index']);
Route::get('/astro',     [\App\Http\Controllers\AstroController::class,     'index']);
Route::get('/osdr',      [\App\Http\Controllers\OsdrController::class,      'index']);

// Прокси к rust_iss
Route::get('/api/iss/last',  [\App\Http\Controllers\ProxyController::class, 'last']);
Route::get('/api/iss/trend', [\App\Http\Controllers\ProxyController::class, 'trend']);

// API
Route::get('/api/jwst/feed',    [\App\Http\Controllers\JwstController::class,   'feed']);
Route::get("/api/astro/events", [\App\Http\Controllers\AstroController::class,  "events"]);

