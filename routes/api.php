<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/health', [HealthController::class, 'index']);

Route::post('/register', [AuthController::class, 'register']);
