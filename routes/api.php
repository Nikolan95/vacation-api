<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, VacationRequestController, TeamController, UserController};



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

Route::group(['middleware' => 'auth:api'], function () {
    // User routes
    Route::put('vacation-requests', [VacationRequestController::class, 'store']);
    Route::patch('vacation-requests/{id}', [VacationRequestController::class, 'update']);
    Route::patch('vacation-requests/{id}/cancel', [VacationRequestController::class, 'cancel']);
    Route::get('vacation-requests', [VacationRequestController::class, 'index']);

    // Manager routes
    Route::patch('vacation-requests/{id}/approve', [VacationRequestController::class, 'approve']);
    Route::patch('vacation-requests/{id}/reject', [VacationRequestController::class, 'reject']);

    // Admin routes
    Route::apiResource('users', UserController::class);
    Route::apiResource('teams', TeamController::class);
});

// Auth routes
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
