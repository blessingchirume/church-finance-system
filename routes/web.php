<?php

use App\Http\Controllers\ChartAccountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FinanceReportController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PartnershipController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

//Route::get('/dashboard', function () {
//    return view('dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/chart-accounts', [ChartAccountController::class, 'index'])->name('chart-accounts.index');
    Route::middleware('role:admin,treasurer')->group(function () {
        Route::get('/chart-accounts/create', [ChartAccountController::class, 'create'])->name('chart-accounts.create');
        Route::post('/chart-accounts', [ChartAccountController::class, 'store'])->name('chart-accounts.store');
        Route::get('/chart-accounts/{chartAccount}/edit', [ChartAccountController::class, 'edit'])->name('chart-accounts.edit');
        Route::put('/chart-accounts/{chartAccount}', [ChartAccountController::class, 'update'])->name('chart-accounts.update');
        Route::delete('/chart-accounts/{chartAccount}', [ChartAccountController::class, 'destroy'])->name('chart-accounts.destroy');
    });
    Route::get('/finance-reports', [FinanceReportController::class, 'index'])->name('finance-reports.index');
    Route::get('/finance-reports/general-ledger', [FinanceReportController::class, 'generalLedger'])->name('finance-reports.general-ledger');
    Route::get('/finance-reports/funeral-reconciliation', [FinanceReportController::class, 'funeral'])->name('finance-reports.funeral');
    Route::get('/users', [UserRoleController::class, 'index'])->name('users.index')->middleware('role:admin');
    Route::patch('/users/{user}/role', [UserRoleController::class, 'update'])->name('users.role.update')->middleware('role:admin');
    Route::prefix('partnerships')->group(function () {
        Route::get('/test', function () {
            return 'This is a test route for partnerships.';
        })->name('partnerships.test');
        Route::get('/reports', [PartnershipController::class, 'reports'])->name('partnerships.reports');

        Route::get('/', [PartnershipController::class, 'index'])->name('partnerships.index');
        Route::get('/create', [PartnershipController::class, 'create'])->name('partnerships.create');
        Route::post('/', [PartnershipController::class, 'store'])->name('partnerships.store');
        Route::get('/{id}', [PartnershipController::class, 'show'])->name('partnerships.show');
        Route::get('/{id}/record-payment', [PartnershipController::class, 'recordPaymentForm'])->name('partnerships.record-payment');
        Route::post('/{id}/record-payment', [PartnershipController::class, 'recordPayment'])->name('partnerships.record-payment.store');

    });
    Route::resource('members', MemberController::class);
    Route::resource('services', ServiceController::class);
    Route::resource('incomes', IncomeController::class);
    Route::resource('expenses', ExpenseController::class);
    Route::resource('projects', ProjectController::class);
    Route::get('/test', [MemberController::class, 'search'])->name('test');
    Route::get('/member/search', [MemberController::class, 'search'])->name('members.search');
    Route::get('/reports/service/{id}', [ReportController::class, 'generateServiceReport']);
});

require __DIR__ . '/auth.php';
