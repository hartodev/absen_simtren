<?php

use App\Http\Controllers\Web\Auth\ActivationController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\RegisterController;
use App\Http\Controllers\Web\Company\HrAttendanceController;
use App\Http\Controllers\Web\Company\HrDashboardController;
use App\Http\Controllers\Web\Company\HrEmployeeController;
use App\Http\Controllers\Web\Employee\EmployeeAttendanceController;
use App\Http\Controllers\Web\Employee\EmployeeDailyReportController;
use App\Http\Controllers\Web\Employee\EmployeeDashboardController;
use App\Http\Controllers\Web\Employee\EmployeeHolidayController;
use App\Http\Controllers\Web\Employee\EmployeeLeaveController;
use App\Http\Controllers\Web\Employee\EmployeeMonthlyReportController;
use App\Http\Controllers\Web\Employee\EmployeeNotesController;
use App\Http\Controllers\Web\Employee\EmployeeOvertimeRequestController;
use App\Http\Controllers\Web\Employee\EmployeePayrollController;
use App\Http\Controllers\Web\Employee\EmployeePerformanceScoreController;
use App\Http\Controllers\Web\Employee\EmployeePermissionController;
use App\Http\Controllers\Web\Employee\EmployeeShiftController;
use App\Http\Controllers\Web\Landing\LandingController;
use App\Http\Controllers\Web\Tenant\AttendanceDeviceController;
use App\Http\Middleware\ForgetTenantRouteParameter;
use App\Http\Controllers\Web\Tenant\BoardingPermissionController;
use App\Http\Controllers\Web\Tenant\ClassRoomController;
use App\Http\Controllers\Web\Tenant\ModuleController;
use App\Http\Controllers\Web\Tenant\MutabaahController;
use App\Http\Controllers\Web\Tenant\ProfileController;
use App\Http\Controllers\Web\Tenant\StudentAttendanceController;
use App\Http\Controllers\Web\Tenant\StudentController;
use App\Http\Controllers\Web\Tenant\StudentPermissionController;
use App\Http\Controllers\Web\Auth\PortalController;
use App\Http\Controllers\Web\SuperAdmin\AnalyticsController;
use App\Http\Controllers\Web\SuperAdmin\AppPolicyController;
use App\Http\Controllers\Web\SuperAdmin\AuditLogController;
use App\Http\Controllers\Web\SuperAdmin\CompanySubscriptionController;
use App\Http\Controllers\Web\SuperAdmin\DiscountController;
use App\Http\Controllers\Web\SuperAdmin\GlobalSearchController;
use App\Http\Controllers\Web\SuperAdmin\HelpArticleController;
use App\Http\Controllers\Web\SuperAdmin\InvoiceController;
use App\Http\Controllers\Web\SuperAdmin\PlanController;
use App\Http\Controllers\Web\SuperAdmin\SettingController;
use App\Http\Controllers\Web\SuperAdmin\StaffController;
use App\Http\Controllers\Web\SuperAdmin\SuperAdminDashboardController;
use App\Http\Controllers\Web\SuperAdmin\SuperAdminTenantController;
use App\Http\Controllers\Web\SuperAdmin\SupportTicketController;
use App\Http\Controllers\Web\SuperAdmin\VaPaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| routes/web.php
|
| PENTING: route domain utama SEKARANG dibungkus eksplisit dengan
| Route::domain(config('app.main_domain')). Sebelumnya route ini TIDAK
| dibatasi domain apapun, sehingga ikut "menangkap" request dari
| subdomain tenant juga (karena didaftarkan lebih dulu daripada grup
| subdomain tenant di bawah). Akibatnya middleware 'tenant' tidak pernah
| jalan walau user sedang mengakses dari subdomain, dan semua pengecekan
| isolasi tenant di LoginController jadi tidak efektif.
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| DOMAIN UTAMA -- landing, registrasi mandiri, aktivasi, login+dashboard
| superadmin
|--------------------------------------------------------------------------
*/
Route::domain(config('app.main_domain'))->group(function () {
    Route::get('/', [LandingController::class, 'index'])->name('landing');

    Route::get('/daftar', [RegisterController::class, 'create'])->name('register.form');
    Route::post('/daftar', [RegisterController::class, 'store'])->name('register.store');
    Route::get('/daftar/cek-email', [ActivationController::class, 'cekEmail'])->name('register.cek-email');
    Route::post('/daftar/kirim-ulang', [ActivationController::class, 'resend'])->name('register.resend');
    Route::get('/daftar/aktivasi/{company}/{user}', [ActivationController::class, 'activate'])
        ->middleware('signed')
        ->name('register.activate');

    // Gerbang "cari lembaga": user isi subdomain -> diarahkan ke login subdomain.
    // BUKAN form email/password (itu khusus superadmin, di bawah).
    Route::get('/login', [PortalController::class, 'show'])->name('login.form');
    Route::post('/login', [PortalController::class, 'redirectToTenant'])->name('login.attempt');

    // Login superadmin (email + password) dipisah ke URL sendiri.
    Route::get('/superadmin/login', [LoginController::class, 'showLoginForm'])->name('superadmin.login.form');
    Route::post('/superadmin/login', [LoginController::class, 'login'])->name('superadmin.login.post');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'role:superadmin'])
        ->prefix('superadmin')
        ->name('superadmin.')
        ->group(function () {
            Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
            Route::get('/search', [GlobalSearchController::class, 'index'])->name('search');

            Route::get('/tenants', [SuperAdminTenantController::class, 'index'])->name('tenants.index');
            Route::get('/tenants/create', [SuperAdminTenantController::class, 'create'])->name('tenants.create');
            Route::post('/tenants', [SuperAdminTenantController::class, 'store'])->name('tenants.store');
            Route::get('/tenants/{id}', [SuperAdminTenantController::class, 'show'])->name('tenants.show');
            Route::post('/tenants/{id}/style', [SuperAdminTenantController::class, 'updateStyle'])->name('tenants.style');
            Route::post('/tenants/{id}/suspend', [SuperAdminTenantController::class, 'suspend'])->name('tenants.suspend');
            Route::post('/tenants/{id}/activate', [SuperAdminTenantController::class, 'activate'])->name('tenants.activate');
            Route::delete('/tenants/{id}', [SuperAdminTenantController::class, 'destroy'])->name('tenants.destroy');

            // Modul billing/subscription -- STUB, ditunda ke tahap berikutnya
            Route::resource('plans', PlanController::class);
            Route::resource('discounts', DiscountController::class);
            Route::get('/subscriptions', [CompanySubscriptionController::class, 'index'])->name('subscriptions.index');
            Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
            Route::get('/va-payments', [VaPaymentController::class, 'index'])->name('va-payments.index');
            Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
            Route::resource('staff', StaffController::class);
            Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
            Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
            Route::resource('help-articles', HelpArticleController::class);
            Route::get('/support-tickets', [SupportTicketController::class, 'index'])->name('support-tickets.index');
            Route::resource('app-policies', AppPolicyController::class);
        });
});

/*
|--------------------------------------------------------------------------
| SUBDOMAIN TENANT -- login & dashboard untuk company/pesantren/school.
| Tiga prefix admin (company/pesantren/school) berbagi controller yang
| SAMA (HrDashboardController, HrAttendanceController, HrEmployeeController)
| karena logikanya identik, cuma istilah & role-nya beda -- dibedakan lewat
| middleware 'tenant.type' dan 'role', bukan controller terpisah.
|
| PENTING soal nama route: karena controllernya sama tapi dipakai 3x (satu
| per tipe tenant), setiap grup dibungkus ->name($type.'.') supaya nama
| route-nya tidak tabrakan -- hasilnya: company.dashboard, pesantren.dashboard,
| school.dashboard, company.employees.index, pesantren.employees.index, dst.
| Controller sendiri menentukan tipe tenant yang aktif dari $company->type
| (lihat HrDashboardController/HrEmployeeController), BUKAN dari nama route,
| jadi route() yang dipanggil dari dalam controller/view harus selalu pakai
| "{$company->type}.nama-route" -- lihat tips di bawah file ini.
|--------------------------------------------------------------------------
*/
Route::domain('{tenant}.' . config('app.tenant_domain'))->middleware('tenant')->group(function () {

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('tenant.login.form');
    Route::post('/login', [LoginController::class, 'login'])->name('tenant.login.post');
    Route::post('/logout', [LoginController::class, 'logout'])->name('tenant.logout');

    // ===== ADMIN ORGANISASI (hr / ustadz / teacher) =====
    foreach (['company' => 'hr', 'pesantren' => 'ustadz', 'school' => 'teacher'] as $type => $adminRole) {
        Route::middleware(['auth', 'tenant.type:' . $type, 'role:' . $adminRole])
            ->prefix($type)
            ->name($type . '.')
            ->group(function () {
                Route::get('/dashboard', [HrDashboardController::class, 'index'])
                    ->name('dashboard');

                Route::get('/dashboard/refresh', [HrDashboardController::class, 'refresh'])
                    ->name('dashboard.refresh');

                Route::get('/attendances', [HrAttendanceController::class, 'index'])->name('attendances.index');

                // Settings HARUS di atas route {id} di bawahnya.
                Route::get('/attendances/settings', [HrAttendanceController::class, 'settings'])->name('attendances.settings');
                Route::put('/attendances/settings', [HrAttendanceController::class, 'updateSettings'])->name('attendances.settings.update');

                Route::get('/attendances/{id}', [HrAttendanceController::class, 'show'])->name('attendances.show');

                // Kelola anggota (karyawan / santri / siswa -- otomatis
                // menyesuaikan tipe tenant, lihat HrEmployeeController::memberRole())
                Route::get('/employees', [HrEmployeeController::class, 'index'])->name('employees.index');
                Route::get('/employees/create', [HrEmployeeController::class, 'create'])->name('employees.create');
                Route::post('/employees', [HrEmployeeController::class, 'store'])->name('employees.store');
                Route::get('/employees/export', [HrEmployeeController::class, 'exportPdf'])->name('employees.export');
                Route::get('/employees/{id}/edit', [HrEmployeeController::class, 'edit'])->name('employees.edit');
                Route::put('/employees/{id}', [HrEmployeeController::class, 'update'])->name('employees.update');
                Route::delete('/employees/{id}', [HrEmployeeController::class, 'destroy'])->name('employees.destroy');
            });
    }

    // ===== MODUL ADMIN SEKOLAH & PESANTREN (halaman web, tema hijau/biru) =====
    // Dashboard-nya tetap lewat route 'dashboard' di atas (HrDashboardController
    // yang mendelegasikan ke AdminDashboardController sesuai gaya dashboard).
    //
    // ForgetTenantRouteParameter (paling akhir): membuang parameter domain
    // {tenant} dari argumen controller, supaya method seperti edit(int $id)
    // tidak menerima subdomain sebagai argumen pertama.

    foreach (['pesantren' => 'ustadz', 'school' => 'teacher'] as $type => $adminRole) {
        Route::middleware(['auth', 'tenant.type:' . $type, 'role:' . $adminRole, ForgetTenantRouteParameter::class])
            ->prefix($type)
            ->name($type . '.')
            ->group(function () use ($type) {
                Route::get('/profile', [ProfileController::class, 'show'])->name('profile');

                Route::get('/students', [StudentController::class, 'index'])->name('students.index');
                Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
                Route::post('/students', [StudentController::class, 'store'])->name('students.store');
                Route::get('/students/{id}/edit', [StudentController::class, 'edit'])->name('students.edit');
                Route::put('/students/{id}', [StudentController::class, 'update'])->name('students.update');
                Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('students.destroy');

                Route::get('/classes', [ClassRoomController::class, 'index'])->name('classes.index');
                Route::get('/classes/create', [ClassRoomController::class, 'create'])->name('classes.create');
                Route::post('/classes', [ClassRoomController::class, 'store'])->name('classes.store');
                Route::get('/classes/{id}/edit', [ClassRoomController::class, 'edit'])->name('classes.edit');
                Route::put('/classes/{id}', [ClassRoomController::class, 'update'])->name('classes.update');
                Route::delete('/classes/{id}', [ClassRoomController::class, 'destroy'])->name('classes.destroy');

                Route::get('/devices', [AttendanceDeviceController::class, 'index'])->name('devices.index');
                Route::get('/devices/create', [AttendanceDeviceController::class, 'create'])->name('devices.create');
                Route::post('/devices', [AttendanceDeviceController::class, 'store'])->name('devices.store');
                Route::get('/devices/{id}/edit', [AttendanceDeviceController::class, 'edit'])->name('devices.edit');
                Route::put('/devices/{id}', [AttendanceDeviceController::class, 'update'])->name('devices.update');
                Route::post('/devices/{id}/regenerate', [AttendanceDeviceController::class, 'regenerate'])->name('devices.regenerate');
                Route::delete('/devices/{id}', [AttendanceDeviceController::class, 'destroy'])->name('devices.destroy');

                Route::get('/student-attendances', [StudentAttendanceController::class, 'index'])->name('student-attendances.index');
                Route::post('/student-attendances', [StudentAttendanceController::class, 'store'])->name('student-attendances.store');

                Route::get('/student-permissions', [StudentPermissionController::class, 'index'])->name('student-permissions.index');
                Route::post('/student-permissions/{id}/review', [StudentPermissionController::class, 'review'])->name('student-permissions.review');

                Route::get('/boarding-permissions', [BoardingPermissionController::class, 'index'])->name('boarding.index');
                Route::post('/boarding-permissions/{id}/review', [BoardingPermissionController::class, 'review'])->name('boarding.review');


                // Mutaba'ah hanya untuk pesantren/TPQ (view: pages/pesantren/mutabaah).
                if ($type === 'pesantren') {
                    Route::get('/mutabaah', [MutabaahController::class, 'index'])->name('mutabaah.index');
                    Route::post('/mutabaah', [MutabaahController::class, 'store'])->name('mutabaah.store');
                    Route::post('/mutabaah/{id}/sign', [MutabaahController::class, 'sign'])->name('mutabaah.sign');
                }

                // Placeholder modul yang belum punya halaman -- HARUS paling bawah.
                Route::get('/modules/{slug}', [ModuleController::class, 'show'])->name('modules.show');
            });
    }

    // ===== ANGGOTA (employee / santri / student) =====
    foreach (['company' => 'employee', 'pesantren' => 'santri', 'school' => 'student'] as $type => $memberRole) {
        Route::middleware(['auth', 'tenant.type:' . $type, 'role:' . $memberRole])
            ->prefix($memberRole)
            ->name($type . '.member.')
            ->group(function () {
                Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');

                Route::get('/attendance', [EmployeeAttendanceController::class, 'index'])->name('attendance.index');
                Route::get('/attendance/{id}', [EmployeeAttendanceController::class, 'show'])->name('attendance.show');
                Route::post('/attendance/checkin', [EmployeeAttendanceController::class, 'checkIn'])->name('attendance.checkin');
                Route::post('/attendance/checkout', [EmployeeAttendanceController::class, 'checkOut'])->name('attendance.checkout');

                // --- Izin ---
                Route::get('/permissions', [EmployeePermissionController::class, 'index'])->name('permissions.index');
                Route::get('/permissions/create', [EmployeePermissionController::class, 'create'])->name('permissions.create');
                Route::post('/permissions', [EmployeePermissionController::class, 'store'])->name('permissions.store');
                Route::get('/permissions/{id}', [EmployeePermissionController::class, 'show'])->name('permissions.show');
                Route::post('/permissions/{id}/cancel', [EmployeePermissionController::class, 'cancel'])->name('permissions.cancel');

                // --- Cuti ---
          
                Route::get('/leaves', [EmployeeLeaveController::class, 'index'])->name('leaves.index');
                Route::get('/leaves/create', [EmployeeLeaveController::class, 'create'])->name('leaves.create');
                Route::post('/leaves', [EmployeeLeaveController::class, 'store'])->name('leaves.store');
                Route::get('/leaves/{id}', [EmployeeLeaveController::class, 'show'])->name('leaves.show');
                Route::post('/leaves/{id}/cancel', [EmployeeLeaveController::class, 'cancel'])->name('leaves.cancel');

                // --- Laporan Harian ---
                Route::get('/daily-reports', [EmployeeDailyReportController::class, 'index'])->name('daily-reports.index');
                Route::post('/daily-reports', [EmployeeDailyReportController::class, 'store'])->name('daily-reports.store');
                Route::get('/daily-reports/{id}', [EmployeeDailyReportController::class, 'show'])->name('daily-reports.show');
                Route::put('/daily-reports', [EmployeeDailyReportController::class, 'update'])->name('daily-reports.update');
                // --- Laporan Bulanan ---
                Route::get('/monthly-reports', [EmployeeMonthlyReportController::class, 'index'])->name('monthly-reports.index');
                Route::get('/monthly-reports/create', [EmployeeMonthlyReportController::class, 'create'])->name('monthly-reports.create');
                Route::post('/monthly-reports', [EmployeeMonthlyReportController::class, 'store'])->name('monthly-reports.store');
                Route::get('/monthly-reports/{id}', [EmployeeMonthlyReportController::class, 'show'])->name('monthly-reports.show');
                Route::get('/monthly-reports/{id}/edit', [EmployeeMonthlyReportController::class, 'edit'])->name('monthly-reports.edit');
                Route::post('/monthly-reports/{id}', [EmployeeMonthlyReportController::class, 'update'])->name('monthly-reports.update');
                Route::post('/monthly-reports/{id}/submit', [EmployeeMonthlyReportController::class, 'submit'])->name('monthly-reports.submit');
                Route::delete('/monthly-reports/{id}', [EmployeeMonthlyReportController::class, 'destroy'])->name('monthly-reports.destroy');

                // --- Notes ---
                Route::get('/notes', [EmployeeNotesController::class, 'index'])->name('notes.index');
                Route::get('/notes/{id}', [EmployeeNotesController::class, 'show'])->name('notes.show');

                // --- Lembur ---
                Route::get('/overtimes', [EmployeeOvertimeRequestController::class, 'index'])->name('overtimes.index');
                Route::get('/overtimes/create', [EmployeeOvertimeRequestController::class, 'create'])->name('overtimes.create');
                Route::post('/overtimes', [EmployeeOvertimeRequestController::class, 'store'])->name('overtimes.store');
                Route::get('/overtimes/{id}', [EmployeeOvertimeRequestController::class, 'show'])->name('overtimes.show');
                Route::post('/overtimes/{id}/cancel', [EmployeeOvertimeRequestController::class, 'cancel'])->name('overtimes.cancel');

                // --- Jadwal Shift ---
                Route::get('/shifts/schedule', [EmployeeShiftController::class, 'schedule'])->name('shifts.schedule');

                // --- Slip Gaji ---
                Route::get('/payrolls', [EmployeePayrollController::class, 'index'])->name('payrolls.index');
                Route::get('/payrolls/{id}', [EmployeePayrollController::class, 'show'])->name('payrolls.show');

                // --- Performa ---
                Route::get('/performance-scores', [EmployeePerformanceScoreController::class, 'index'])->name('performance-scores.index');
                Route::get('/performance-scores/leaderboard', [EmployeePerformanceScoreController::class, 'leaderboard'])->name('performance-scores.leaderboard');
                Route::get('/performance-scores/{id}', [EmployeePerformanceScoreController::class, 'show'])->name('performance-scores.show');

                // --- Hari Libur ---
                Route::get('/holidays', [EmployeeHolidayController::class, 'index'])->name('holidays.index');
                Route::get('/holidays/{id}', [EmployeeHolidayController::class, 'show'])->name('holidays.show');
            });
    }
});