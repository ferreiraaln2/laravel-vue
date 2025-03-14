<?php

use App\Http\Controllers\Api\PetController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\AppointmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Pet routes
Route::middleware('auth:sanctum')->apiResource('pets', PetController::class);

// Service routes
Route::apiResource('services', ServiceController::class);

// Appointment routes
Route::middleware('auth:sanctum')->apiResource('appointments', AppointmentController::class); 