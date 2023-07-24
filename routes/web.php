<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Admin Route

Route::prefix('admin')->middleware(['auth','isAdmin'])->group(function() {

    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index']);
    Route::get('employees', [App\Http\Controllers\Admin\EmployeeController::class, 'index']);
    Route::post('employees', [App\Http\Controllers\Admin\EmployeeController::class, 'store']);
    Route::get('click_delete/{user_id}', [App\Http\Controllers\Admin\EmployeeController::class, 'delete_function']);
    Route::get('attendance', [\App\Http\Controllers\Admin\AttendanceReportController::class, 'empreport'])->name('admin.empreport');
    Route::get('employees/viewemp/{user_id}', [App\Http\Controllers\Admin\EmployeeController::class, 'view']);
    Route::get('employees/edit/{user_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'edit']);
    Route::put('employees/update/{user_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'update']);
});

// Employee Route

Route::prefix('emp')->middleware(['auth','isEmployee'])->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\Employee\DashboardController::class, 'index']);
    Route::get('attendance', [App\Http\Controllers\Employee\AttendanceController::class, 'index'])->name('attendance');
    Route::post('attendance', [App\Http\Controllers\Employee\AttendanceController::class, 'store']);
    Route::put('attendance', [App\Http\Controllers\Employee\AttendanceController::class, 'update']);
    Route::put('attendance/breakin', [App\Http\Controllers\Employee\AttendanceController::class, 'breakIn']);
    Route::put('attendance/breakout', [App\Http\Controllers\Employee\AttendanceController::class, 'breakOut']);
    // Route::put('attendance/{id}', [App\Http\Controllers\Employee\AttendanceController::class, 'total']);
    Route::get('attendance/report', [App\Http\Controllers\Employee\AttendanceController::class, 'report'])->name('report.index');
    Route::get('empprofile', [\App\Http\Controllers\Employee\ProfileController::class, 'index']);
    Route::get('changepassword', [\App\Http\Controllers\Employee\ChangePasswordController::class, 'index']);
    Route::post('changepassword', [\App\Http\Controllers\Employee\ChangePasswordController::class, 'changePassword']);
    Route::get('payslip', [\App\Http\Controllers\Employee\PayslipController::class, 'index']);
    Route::get('payslip/view/{payslip_id}', [\App\Http\Controllers\Employee\PayslipController::class, 'view']);
});

//HR Route

Route::prefix('hr')->middleware(['auth','isHr'])->group(function () {
    Route::get('dashboard', [App\Http\Controllers\Hr\DashboardController::class, 'index']);
    Route::get('attendance', [App\Http\Controllers\Hr\HRAttendanceController::class, 'index'])->name('att.test');
    Route::post('attendance', [App\Http\Controllers\Hr\HRAttendanceController::class, 'store']);
    Route::put('attendance', [App\Http\Controllers\Hr\HRAttendanceController::class, 'update']);
    Route::put('attendance/breakin', [App\Http\Controllers\Hr\HRAttendanceController::class, 'breakIn']);
    Route::put('attendance/breakout', [App\Http\Controllers\Hr\HRAttendanceController::class, 'breakOut']);
    Route::get('attendance/empreport', [App\Http\Controllers\Hr\HRAttendanceController::class, 'empreport'])->name('report.empindex');
    Route::get('employee', [App\Http\Controllers\Hr\EmployeeController::class, 'index']);
    Route::post('employee', [\App\Http\Controllers\Hr\EmployeeController::class, 'store']);
    Route::get('click_delete/{user_id}', [\App\Http\Controllers\Hr\EmployeeController::class, 'delete_function']);
    Route::get('employee/viewemp/{user_id}', [App\Http\Controllers\Hr\EmployeeController::class, 'view']);
    Route::get('employee/edit/{user_id}', [\App\Http\Controllers\Hr\EmployeeController::class, 'edit']);
    Route::put('employee/update/{user_id}', [\App\Http\Controllers\Hr\EmployeeController::class, 'update']);
    Route::get('payroll', [App\Http\Controllers\Hr\PayrollController::class, 'index']);
    Route::get('payroll', [\App\Http\Controllers\Hr\PayrollController::class, 'emppayroll'])->name('emp.payroll');
    Route::post('payroll', [\App\Http\Controllers\Hr\PayrollController::class, 'store'])->name('employee.salaries.store');
    Route::post('payroll/check', [\App\Http\Controllers\Hr\PayrollController::class, 'check'])->name('employee.salaries.check');
    Route::get('payslip', [App\Http\Controllers\Hr\PayslipController::class, 'index']);
    Route::get('payroll/edit/{salary_id}', [App\Http\Controllers\Hr\PayrollController::class, 'edit']);
    Route::get('payroll/view/{salary_id}', [App\Http\Controllers\Hr\PayrollController::class, 'view']);
    Route::get('payroll/click_delete/{salary_id}', [App\Http\Controllers\Hr\PayrollController::class, 'function_delete']);
});