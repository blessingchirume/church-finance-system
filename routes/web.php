<?php

use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\MemberController;
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

Route::resource('members', MemberController::class);
Route::resource('services', ServiceController::class);
Route::resource('incomes', IncomeController::class);
Route::resource('expenses', ExpenseController::class);
Route::resource('projects', ProjectController::class);
Route::get('/test', [MemberController::class, 'search'])->name('test');
Route::get('/member/search', [MemberController::class, 'search'])->name('members.search');
Route::get('/reports/service/{id}', [ReportController::class, 'generateServiceReport']);

