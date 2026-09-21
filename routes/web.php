<?php

use App\Http\Controllers\Web\Auth\ActivationController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\RegisterController;
use App\Http\Controllers\Web\Company\HrAttendanceController;
use App\Http\Controllers\Web\Company\HrDashboardController;
use App\Http\Controllers\Web\Company\HrEmployeeController;
use App\Http\Controllers\Web\Employee\EmployeeAttendanceWebController;
use App\Http\Controllers\Web\Employee\EmployeeDashboardWebController;
use App\Http\Controllers\Web\LandingController;
use App\Http\Controllers\Web\PortalController;
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
| SAMA (HrDashboardController, HrAttendanceController) karena logikanya
| identik, cuma istilah & role-nya beda -- dibedakan lewat middleware
| 'tenant.type' dan 'role', bukan controller terpisah.
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
            ->group(function () {
                Route::get('/dashboard', [HrDashboardController::class, 'index']);
                Route::get('/attendances', [HrAttendanceController::class, 'index']);
                Route::get('/attendances/{id}', [HrAttendanceController::class, 'show']);

                // Kelola anggota (karyawan / santri / siswa -- otomatis
                // menyesuaikan tipe tenant, lihat HrEmployeeController::memberRole())
                Route::get('/employees', [HrEmployeeController::class, 'index']);
                Route::get('/employees/create', [HrEmployeeController::class, 'create']);
                Route::post('/employees', [HrEmployeeController::class, 'store']);
                Route::get('/employees/{id}/edit', [HrEmployeeController::class, 'edit']);
                Route::put('/employees/{id}', [HrEmployeeController::class, 'update']);
                Route::delete('/employees/{id}', [HrEmployeeController::class, 'destroy']);
            });
    }

    // ===== ANGGOTA (employee / santri / student) =====
    foreach (['company' => 'employee', 'pesantren' => 'santri', 'school' => 'student'] as $type => $memberRole) {
        Route::middleware(['auth', 'tenant.type:' . $type, 'role:' . $memberRole])
            ->prefix($memberRole)
            ->group(function () {
                Route::get('/dashboard', [EmployeeDashboardWebController::class, 'index']);
                Route::get('/attendance', [EmployeeAttendanceWebController::class, 'index']);
                Route::get('/attendance/{id}', [EmployeeAttendanceWebController::class, 'show']);
                Route::post('/attendance/checkin', [EmployeeAttendanceWebController::class, 'checkIn']);
                Route::post('/attendance/checkout', [EmployeeAttendanceWebController::class, 'checkOut']);
            });
    }
});