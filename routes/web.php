<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('auth.login');
});


Auth::routes(['register' => false]);

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Admin Route

Route::prefix('admin')->middleware(['auth','isAdmin','sessionTimeout'])->group(function() {

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

Route::prefix('emp')->middleware(['auth','isEmployee', 'sessionTimeout'])->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\Employee\DashboardController::class, 'index'])->name('main.emp');
    Route::get('/dashboard/get-data', [\App\Http\Controllers\Employee\DashboardController::class, 'getUserAttendance'])->name('attendance.get');
    // Route::get('attendance', [App\Http\Controllers\Employee\AttendanceController::class, 'index'])->name('attendance');
    Route::post('dashboard', [App\Http\Controllers\Employee\DashboardController::class, 'store']);
    Route::put('dashboard', [App\Http\Controllers\Employee\DashboardController::class, 'update']);
    Route::put('dashboard/breakin', [App\Http\Controllers\Employee\DashboardController::class, 'breakIn']);
    Route::put('dashboard/breakout', [App\Http\Controllers\Employee\DashboardController::class, 'breakOut']);
    Route::get('attendance/report', [App\Http\Controllers\Employee\DashboardController::class, 'report'])->name('report.index');
    Route::get('profile', [\App\Http\Controllers\Employee\ProfileController::class, 'index'])->name('emp.profile');
    Route::post('profile', [\App\Http\Controllers\Employee\ProfileController::class, 'update'])->name('emp.update');
    Route::post('profile/contact', [\App\Http\Controllers\Employee\ProfileController::class, 'contactStore'])->name('profile.econtact');
    Route::get('changepassword', [\App\Http\Controllers\Employee\ChangePasswordController::class, 'index'])->name('change.pass');
    Route::post('changepassword', [\App\Http\Controllers\Employee\ChangePasswordController::class, 'changePassword']);
    Route::get('payslip', [\App\Http\Controllers\Employee\PayslipController::class, 'index']);
    Route::get('payslip/view/{payslip_id}', [\App\Http\Controllers\Employee\PayslipController::class, 'view']);
});


//HR Route

Route::prefix('hr')->middleware(['auth','isHr', 'sessionTimeout'])->group(function () {
    Route::get('dashboard', [App\Http\Controllers\Hr\DashboardController::class, 'index'])->name('main.hr');
    Route::get('attendance', [App\Http\Controllers\Hr\HRAttendanceController::class, 'index'])->name('att.test');
    Route::post('attendance', [App\Http\Controllers\Hr\HRAttendanceController::class, 'store']);
    Route::put('attendance', [App\Http\Controllers\Hr\HRAttendanceController::class, 'update']);
    Route::put('attendance/breakin', [App\Http\Controllers\Hr\HRAttendanceController::class, 'breakIn']);
    Route::put('attendance/breakout', [App\Http\Controllers\Hr\HRAttendanceController::class, 'breakOut']);
    Route::post('attendance/empreport/update',[\App\Http\Controllers\Hr\HRAttendanceController::class, 'updateTable'])->name('update.table');
    Route::post('attendance/empreport', [App\Http\Controllers\Hr\HRAttendanceController::class, 'updateTotals'])->name('updateTotals');
    Route::get('attendance/empreport', [App\Http\Controllers\Hr\HRAttendanceController::class, 'empreport'])->name('report.empindex');
    Route::get('employee', [App\Http\Controllers\Hr\EmployeeController::class, 'index'])->name('employee.index');
    Route::post('employee', [\App\Http\Controllers\Hr\EmployeeController::class, 'store']);
    Route::get('employee/search', [\App\Http\Controllers\Hr\EmployeeController::class, 'search'])->name('employee.search');
    Route::post('employee/delete', [\App\Http\Controllers\Hr\EmployeeController::class, 'delete_function']);
    Route::get('employee/edit/{user_id}', [\App\Http\Controllers\Hr\EmployeeController::class, 'edit'])->name('employee.edit');
    Route::put('employee/update/{user_id}', [\App\Http\Controllers\Hr\EmployeeController::class, 'update'])->name('employee.update');
    Route::put('employee/update/mandates/{user_id}', [\App\Http\Controllers\HR\EmployeeController::class, 'government'])->name('employee.mandates');
    Route::get('payroll', [App\Http\Controllers\Hr\PayrollController::class, 'index']);
    Route::get('payroll', [\App\Http\Controllers\Hr\PayrollController::class, 'emppayroll'])->name('emppayroll');
    Route::post('payroll', [\App\Http\Controllers\Hr\PayrollController::class, 'store'])->name('employee.salaries.store');
    Route::post('payroll/check', [\App\Http\Controllers\Hr\PayrollController::class, 'check'])->name('employee.salaries.check');
    Route::get('payslip', [App\Http\Controllers\Hr\PayslipController::class, 'index']);
    Route::get('payroll/edit/{salary_id}', [App\Http\Controllers\Hr\PayrollController::class, 'edit']);
    Route::get('payroll/view/{salary_id}', [App\Http\Controllers\Hr\PayrollController::class, 'view']);
    Route::get('payroll/click_delete/{salary_id}', [App\Http\Controllers\Hr\PayrollController::class, 'function_delete']);
    Route::get('leave', [App\Http\Controllers\Hr\LeaveAdminController::class, 'index']);
    Route::post('employee/contact/{user_id}', [\App\Http\Controllers\Hr\EmployeeController::class, 'contactStore'])->name('contact.store');
    Route::post('employee/bank/{user_id}', [\App\Http\Controllers\Hr\EmployeeController::class, 'bankInfo'])->name('bank.store');
    Route::post('employee/changepassword/{user_id}', [\App\Http\Controllers\Hr\EmployeeController::class, 'changePassword'])->name('hr.changepassword');
});

// Manager Route


Route::prefix('manager')->middleware(['auth', 'isManager', 'sessionTimeout'])->group(function() {
    Route::get('/dashboard', [App\Http\Controllers\Manager\DashboardController::class, 'index']);
    Route::get('/dashboard/get-data', [App\Http\Controllers\Manager\DashboardController::class, 'getUserAttendance'])->name('attendance.getm');
    Route::post('dashboard', [App\Http\Controllers\Manager\DashboardController::class, 'store']);
    Route::put('dashboard', [App\Http\Controllers\Manager\DashboardController::class, 'update']);
    Route::put('dashboard/breakin', [App\Http\Controllers\Manager\DashboardController::class, 'breakIn']);
    Route::put('dashboard/breakout', [App\Http\Controllers\Manager\DashboardController::class, 'breakOut']);
    Route::get('/department-employee', [App\Http\Controllers\Manager\DepartmentController::class, 'index']);
    Route::get('/department-record/{user_id}', [\App\Http\Controllers\Manager\DepartmentController::class, 'edit']);
});