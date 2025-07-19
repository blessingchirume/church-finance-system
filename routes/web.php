<?php

use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PartnershipController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    //    return view('welcome');
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
// Route::middleware(['auth'])->group(function () {
Route::prefix('partnerships')->group(function () {
    Route::get('/', [PartnershipController::class, 'index'])->name('partnerships.index');
    Route::get('/create', [PartnershipController::class, 'create'])->name('partnerships.create');
    Route::post('/', [PartnershipController::class, 'store'])->name('partnerships.store');
    Route::get('/{id}', [PartnershipController::class, 'show'])->name('partnerships.show');
    Route::get('/{id}/record-payment', [PartnershipController::class, 'recordPaymentForm'])->name('partnerships.record-payment');
    Route::post('/{id}/record-payment', [PartnershipController::class, 'recordPayment'])->name('partnerships.record-payment.store');
    Route::get('/reports', [PartnershipController::class, 'reports'])->name('partnerships.reports');
    Route::get('/test', function(){
return 'This is a test route for partnerships.';
    })->name('partnerships.test');
});
// });
Route::resource('members', MemberController::class);
Route::resource('services', ServiceController::class);
Route::resource('incomes', IncomeController::class);
Route::resource('expenses', ExpenseController::class);
Route::resource('projects', ProjectController::class);
Route::get('/test', [MemberController::class, 'search'])->name('test');
Route::get('/member/search', [MemberController::class, 'search'])->name('members.search');
Route::get('/reports/service/{id}', [ReportController::class, 'generateServiceReport']);
