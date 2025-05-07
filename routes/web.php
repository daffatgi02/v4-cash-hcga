<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FundController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\RkbController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\StaffDebtController;
use App\Http\Controllers\TemporaryFundController;
use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Auth;


// Authentication routes (already defined by laravel/ui)
Auth::routes(['register' => false]);

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Protected routes
Route::middleware('auth')->group(function () {
    // Funds
    Route::resource('funds', FundController::class);

    // Categories
    Route::resource('categories', CategoryController::class);

    // Staff
    Route::resource('staff', StaffController::class);

    // RKB
    Route::resource('rkbs', RkbController::class);
    Route::post('rkbs/{rkb}/receive-funds', [RkbController::class, 'receiveFunds'])->name('rkbs.receive-funds');

    // Transactions
    Route::resource('transactions', TransactionController::class);

    // Staff Debts
    Route::resource('staff-debts', StaffDebtController::class)->except(['create', 'store', 'update', 'destroy']);
    Route::post('staff-debts/{staffDebt}/record-purchase', [StaffDebtController::class, 'recordPurchase'])->name('staff-debts.record-purchase');
    Route::post('staff-debts/{staffDebt}/record-return', [StaffDebtController::class, 'recordReturn'])->name('staff-debts.record-return');

    // Temporary Funds
    Route::resource('temporary-funds', TemporaryFundController::class)->except(['edit', 'update']);
    Route::post('temporary-funds/{temporaryFund}/settle', [TemporaryFundController::class, 'settle'])->name('temporary-funds.settle');

    // Files
    Route::get('files/{file}/download', [FileController::class, 'download'])->name('files.download');
    Route::delete('files/{file}', [FileController::class, 'destroy'])->name('files.destroy');
    Route::post('files', [FileController::class, 'store'])->name('files.store');
});
