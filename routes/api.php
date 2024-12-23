<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Employee\api\AttendanceController;
use App\Http\Controllers\Employee\api\InformationController;
use App\Http\Controllers\Employee\api\DashboardController;
use App\Http\Controllers\Employee\api\BankController;
use App\Http\Controllers\Employee\api\LeaveController;
use App\Http\Controllers\Employee\api\EmegencyContactController;


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
    Route::post('in', [DashboardController::class, 'store']);
    // Clock Out
    Route::post('out', [DashboardController::class, 'update']);
    //breaks
    Route::post('breakout15m', [AttendanceController::class, 'startBreak15m']);
    Route::post('breakin15m', [AttendanceController::class, 'endBreak15m']);
    Route::post('breakout1h', [AttendanceController::class, 'startBreak1h']);
    Route::post('breakin1h', [AttendanceController::class, 'endBreak1h']);

});


//attendance
Route::middleware(['auth:sanctum'])->prefix('attendance')->group(function () {
    //today
    Route::get('/today', [AttendanceController::class, 'getTodayAttendance']);

    //Day
    Route::get('/day', [AttendanceController::class, 'getDayAttendance']);

    //monthly
    Route::get('/monthly', [AttendanceController::class, 'getMonthlyAttendance']);

});

Route::middleware(['auth:sanctum'])->prefix('leave')->group(function () {
    //leave credit
    Route::get('/credit', [LeaveController::class, 'getLeaveCredits']);

    //leave request
    Route::get('/request', [LeaveController::class, 'getLeaveRequest']);
    Route::post('/request', [LeaveController::class, 'createLeaveRequest']);
    Route::put('/request/{id}', [LeaveController::class, 'updateLeaveRequest']);
    Route::delete('/request/{id}', [LeaveController::class, 'deleteLeaveRequest']);

});

//leave request
// Route::get('/leaverequest', [LeaveController::class, 'getLeaveRequest']);
// Route::post('/leaverequest', [LeaveController::class, 'createLeaveRequest']);
// Route::put('/leaverequest/{id}', [LeaveController::class, 'updateLeaveRequest']);
// Route::delete('/leaverequest/{id}', [LeaveController::class, 'deleteLeaveRequest']);


Route::middleware(['auth:sanctum'])->prefix('info')->group(function () {

    //profileInformation
    Route::get('/profile', [InformationController::class, 'getProfileInformation']);

    // Get and Add Additional Info
    Route::get('/additional', [InformationController::class, 'getAdditionalInformation']);
    Route::post('/additional', [InformationController::class, 'createupdateAdditionalInformation']);


    //bankinformation
    Route::get('/bank', [BankController::class, 'getBankInformation']);
    Route::post('/bank', [BankController::class, 'createupdateBankInformation']);


    //emergency Contact
    Route::get('/emergency', [EmegencyContactController::class, 'getEmegencyContact']);
    Route::post('/emergency', [EmegencyContactController::class, 'createupdateEmegencyContact']);

});





//profileInformation
//Route::get('/profileinfomation/{id}', [InformationController::class, 'getProfileInformation']);




//Addtional Info
//Route::get('/additionalinformation/{id}', [InformationController::class, 'getAdditionalInformation']);
//Route::post('/additionalinformation/{id}', [InformationController::class, 'createupdateAdditionalInformation']);

// //bankinformation
// Route::get('/bankinformation/{id}', [BankController::class, 'getBankInformation']);
// Route::post('/bankinformation/{id}', [BankController::class, 'createupdateBankInformation']);


// //emergency Contact
// Route::get('/emergencycontact/{id}', [EmegencyContactController::class, 'getEmegencyContact']);
// Route::post('/emergencycontact/{id}', [EmegencyContactController::class, 'createupdateEmegencyContact']);








