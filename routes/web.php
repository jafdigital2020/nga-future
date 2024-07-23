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

    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.index');
    Route::get('employee', [App\Http\Controllers\Admin\EmployeeController::class, 'index']);
    Route::post('employee', [App\Http\Controllers\Admin\EmployeeController::class, 'store']);
    Route::get('employee/search', [App\Http\Controllers\Admin\EmployeeController::class, 'search'])->name('admin.search');
    Route::post('employee/delete', [App\Http\Controllers\Admin\EmployeeController::class, 'delete_function']);
    Route::get('employee/edit/{user_id}', [App\Http\Controllers\Admin\EmployeeController::class, 'edit'])->name('admin.edit');
    Route::put('employee/update/{user_id}', [App\Http\Controllers\Admin\EmployeeController::class, 'update'])->name('admin.update');
    Route::put('employee/update/mandates/{user_id}', [App\Http\Controllers\Admin\EmployeeController::class, 'government'])->name('admin.mandates');
    Route::post('employee/contact/{user_id}', [App\Http\Controllers\Admin\EmployeeController::class, 'contactStore'])->name('admin.contact');
    Route::post('employee/bank/{user_id}', [App\Http\Controllers\Admin\EmployeeController::class, 'bankInfo'])->name('admin.bank');
    Route::post('employee/personal-info/{user_id}', [App\Http\Controllers\Admin\EmployeeController::class, 'personalInfo'])->name('admin.personal');
    Route::post('employee/changepassword/{user_id}', [App\Http\Controllers\Admin\EmployeeController::class, 'changePassword'])->name('admin.changepassword');
    Route::post('employee/update-record/{user_id}', [\App\Http\Controllers\Admin\EmployeeController::class , 'employmentStore'])->name('employment.storeadmin');
    Route::post('employee/update-salary/{user_id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'employmentSalaryStore'])->name('employment.salaryadmin');
    Route::get('/leave', [\App\Http\Controllers\Admin\AdminLeaveController::class, 'index'])->name('leave.searchadmin');
    Route::post('/leave/{id}/approve', [\App\Http\Controllers\Admin\AdminLeaveController::class, 'approve'])->name('leave.approveadmin');
    Route::post('/leave/{id}/decline', [\App\Http\Controllers\Admin\AdminLeaveController::class, 'decline'])->name('leave.declineadmin');
    Route::post('/leave-request', [\App\Http\Controllers\Admin\AdminLeaveController::class, 'storeLeave'])->name('mstore.leaveadmin');
    Route::post('/leave/{id}', [\App\Http\Controllers\Admin\AdminLeaveController::class, 'update'])->name('leavem.updateadmin');
    Route::delete('/leave/{id}', [\App\Http\Controllers\Admin\AdminLeaveController::class, 'destroy'])->name('leavem.destroyadmin');
    Route::get('/attendance', [App\Http\Controllers\Admin\AttendanceReportController::class, 'index']);
    Route::get('/attendance/search', [App\Http\Controllers\Admin\AttendanceReportController::class, 'index'])->name('attendance.admin');
    Route::get('/attendance/tableview', [App\Http\Controllers\Admin\AttendanceReportController::class, 'empreport'])->name('report.admin');
    Route::post('attendance/tableview/update', [App\Http\Controllers\Admin\AttendanceReportController::class, 'updateTable'])->name('admin.table');

});

// Employee Route

Route::prefix('emp')->middleware(['auth','isEmployee', 'sessionTimeout'])->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\Employee\DashboardController::class, 'index'])->name('main.emp');
    Route::get('/dashboard/get-data', [\App\Http\Controllers\Employee\DashboardController::class, 'getUserAttendance'])->name('attendance.get');
    Route::post('dashboard', [App\Http\Controllers\Employee\DashboardController::class, 'store']);
    Route::put('dashboard', [App\Http\Controllers\Employee\DashboardController::class, 'update']);
    Route::put('dashboard/breakin', [App\Http\Controllers\Employee\DashboardController::class, 'breakIn']);
    Route::put('dashboard/breakout', [App\Http\Controllers\Employee\DashboardController::class, 'breakOut']);
    Route::get('attendance/report', [App\Http\Controllers\Employee\DashboardController::class, 'report'])->name('report.index');
    Route::get('profile', [\App\Http\Controllers\Employee\ProfileController::class, 'index'])->name('emp.profile');
    Route::post('profile', [\App\Http\Controllers\Employee\ProfileController::class, 'update'])->name('emp.update');
    Route::post('profile/contact', [\App\Http\Controllers\Employee\ProfileController::class, 'contactStore'])->name('profile.econtact');
    Route::post('profile/personal-info', [\App\Http\Controllers\Employee\ProfileController::class, 'personalInfo']);
    Route::get('changepassword', [\App\Http\Controllers\Employee\ChangePasswordController::class, 'index'])->name('change.pass');
    Route::post('changepassword', [\App\Http\Controllers\Employee\ChangePasswordController::class, 'changePassword']);
    Route::get('payslip', [\App\Http\Controllers\Employee\PayslipController::class, 'index']);
    Route::get('payslip/view/{payslip_id}', [\App\Http\Controllers\Employee\PayslipController::class, 'view']);
    Route::get('leave', [\App\Http\Controllers\Employee\LeaveController::class, 'index']);
    Route::post('leave-request', [\App\Http\Controllers\Employee\LeaveController::class, 'storeLeave'])->name('store.leave');
    Route::post('/leave/{id}', [\App\Http\Controllers\Employee\LeaveController::class, 'update'])->name('leave.update');
    Route::delete('/leave/{id}', [\App\Http\Controllers\Employee\LeaveController::class, 'destroy'])->name('leave.destroy');
    Route::post('dashboard/attendance/send', [\App\Http\Controllers\Employee\DashboardController::class, 'saveAttendance'])->name('attendance.save');
    Route::post('dashboard/attendance/check', [\App\Http\Controllers\Employee\DashboardController::class, 'check'])->name('attendance.check');
    Route::get('dashboard/attendance/status', [\App\Http\Controllers\Employee\DashboardController::class, 'getStatus'])->name('attendance.status');
    
});


//HR Route

Route::prefix('hr')->middleware(['auth','isHr', 'sessionTimeout'])->group(function () {
    Route::get('dashboard', [App\Http\Controllers\Hr\DashboardController::class, 'index'])->name('main.hr');
    // Route::get('attendance', [App\Http\Controllers\Hr\HRAttendanceController::class, 'index'])->name('att.test');
    Route::post('dashboard', [App\Http\Controllers\Hr\DashboardController::class, 'store']);
    Route::put('dashboard', [App\Http\Controllers\Hr\DashboardController::class, 'update']);
    Route::put('dashboard/breakin', [App\Http\Controllers\Hr\DashboardController::class, 'breakIn']);
    Route::put('dashboard/breakout', [App\Http\Controllers\Hr\DashboardController::class, 'breakOut']);
    Route::get('/dashboard/get-data', [App\Http\Controllers\Hr\DashboardController::class, 'getUserAttendance'])->name('attendance.getr');
    Route::get('employee', [App\Http\Controllers\Hr\EmployeeController::class, 'index'])->name('employee.index');
    Route::post('employee', [\App\Http\Controllers\Hr\EmployeeController::class, 'store']);
    Route::get('employee/search', [\App\Http\Controllers\Hr\EmployeeController::class, 'search'])->name('employee.search');
    Route::post('employee/delete', [\App\Http\Controllers\Hr\EmployeeController::class, 'delete_function']);
    Route::get('employee/edit/{user_id}', [\App\Http\Controllers\Hr\EmployeeController::class, 'edit'])->name('employee.edit');
    Route::put('employee/update/{user_id}', [\App\Http\Controllers\Hr\EmployeeController::class, 'update'])->name('employee.update');
    Route::put('employee/update/mandates/{user_id}', [\App\Http\Controllers\Hr\EmployeeController::class, 'government'])->name('employee.mandates');
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
    Route::post('employee/personal-info/{user_id}', [App\Http\Controllers\Hr\EmployeeController::class, 'personalInfo'])->name('personal.store');
    Route::post('employee/changepassword/{user_id}', [\App\Http\Controllers\Hr\EmployeeController::class, 'changePassword'])->name('hr.changepassword');
    Route::get('/leave', [\App\Http\Controllers\Hr\LeaveAdminController::class, 'index'])->name('leave.searchr');
    Route::post('/leave/{id}/approve', [\App\Http\Controllers\Hr\LeaveAdminController::class, 'approve'])->name('leave.approver');
    Route::post('/leave/{id}/decline', [\App\Http\Controllers\Hr\LeaveAdminController::class, 'decline'])->name('leave.decliner');
    Route::post('/leave-request', [App\Http\Controllers\Hr\LeaveAdminController::class, 'storeLeave'])->name('mstore.leaver');
    Route::post('/leave/{id}', [\App\Http\Controllers\Hr\LeaveAdminController::class, 'update'])->name('leavem.updater');
    Route::delete('/leave/{id}', [\App\Http\Controllers\Hr\LeaveAdminController::class, 'destroy'])->name('leavem.destroyr');
    Route::post('dashboard/attendance/send', [\App\Http\Controllers\Hr\DashboardController::class, 'saveAttendance'])->name('attendance.saver');
    Route::post('dashboard/attendance/check', [\App\Http\Controllers\Hr\DashboardController::class, 'check'])->name('attendance.checkr');
    Route::get('dashboard/attendance/status', [\App\Http\Controllers\Hr\DashboardController::class, 'getStatus'])->name('attendance.statusr');
    Route::get('/attendance', [App\Http\Controllers\Hr\HRAttendanceController::class, 'index']);
    Route::get('/attendance/search', [App\Http\Controllers\Hr\HRAttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/tableview', [App\Http\Controllers\Hr\HRAttendanceController::class, 'empreport'])->name('report.empindex');
    Route::post('attendance/tableview/update',[\App\Http\Controllers\Hr\HRAttendanceController::class, 'updateTable'])->name('update.table');
    Route::get('/profile', [App\Http\Controllers\Hr\ProfileController::class, 'index']);
    Route::post('/profile/personal-info', [App\Http\Controllers\Hr\ProfileController::class, 'personalInfo'])->name('personal.info');
    Route::post('profile/contact', [App\Http\Controllers\Hr\ProfileController::class, 'contactStore'])->name('profile.econtactr');
    Route::get('profile/changepassword', [\App\Http\Controllers\Hr\ProfileController::class, 'changePasswordHr'])->name('hrchange.pass');
    Route::post('profile/changepassword', [\App\Http\Controllers\Hr\ProfileController::class, 'changePassword']);
    Route::post('employee/personal-info/{user_id}', [App\Http\Controllers\Admin\EmployeeController::class, 'personalInfo'])->name('hr.personal');
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
    Route::post('/department-record/update-record/{user_id}', [\App\Http\Controllers\Manager\DepartmentController::class , 'employmentStore'])->name('employment.store');
    Route::post('/department-record/update-salary/{user_id}', [\App\Http\Controllers\Manager\DepartmentController::class, 'employmentSalaryStore'])->name('employment.salary');
    Route::get('/leave', [\App\Http\Controllers\Manager\LeaveController::class, 'index'])->name('leave.search');
    Route::post('/leave/{id}/approve', [\App\Http\Controllers\Manager\LeaveController::class, 'approve'])->name('leave.approve');
    Route::post('/leave/{id}/decline', [\App\Http\Controllers\Manager\LeaveController::class, 'decline'])->name('leave.decline');
    Route::post('/leave-request', [App\Http\Controllers\Manager\LeaveController::class, 'storeLeave'])->name('mstore.leave');
    Route::post('/leave/{id}', [\App\Http\Controllers\Manager\LeaveController::class, 'update'])->name('leavem.update');
    Route::delete('/leave/{id}', [\App\Http\Controllers\Manager\LeaveController::class, 'destroy'])->name('leavem.destroy');
    Route::post('dashboard/attendance/send', [\App\Http\Controllers\Manager\DashboardController::class, 'saveAttendance'])->name('attendance.savem');
    Route::post('dashboard/attendance/check', [\App\Http\Controllers\Manager\DashboardController::class, 'check'])->name('attendance.checkm');
    Route::get('dashboard/attendance/status', [\App\Http\Controllers\Manager\DashboardController::class, 'getStatus'])->name('attendance.statusm');
    Route::get('/attendance', [App\Http\Controllers\Manager\AttendanceApproveController::class, 'index'])->name('attendance.search');
    Route::post('/attendance/{id}/approve', [\App\Http\Controllers\Manager\AttendanceApproveController::class, 'approve'])->name('att.approve');
    Route::post('/attendance/{id}/decline', [\App\Http\Controllers\Manager\AttendanceApproveController::class, 'decline'])->name('att.decline');
    Route::get('/profile', [App\Http\Controllers\Manager\ProfileController::class, 'index']);
    Route::post('/profile/personal-info', [App\Http\Controllers\Manager\ProfileController::class, 'personalInfo'])->name('personal.infom');
    Route::post('profile/contact', [App\Http\Controllers\Manager\ProfileController::class,'contactStore'])->name('profile.econtactm');
    Route::get('profile/changepassword', [App\Http\Controllers\Manager\ProfileController::class, 'changePasswordHr'])->name('manager.pass');
    Route::post('profile/changepassword', [App\Http\Controllers\Manager\ProfileController::class, 'changePassword']);
});
