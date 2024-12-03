<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Employee\api\AttendanceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// PUBLIC ROUTES

Route::post('/login', [LoginController::class, 'apiLogin']);

// EMP PROTECTED ROUTES
Route::middleware(['auth:sanctum'])->prefix('clock')->group(function () {
    // Clock In
    Route::post('timein', [AttendanceController::class, 'store']);
    // Clock Out
    Route::post('timein', [AttendanceController::class, 'store']);

});

