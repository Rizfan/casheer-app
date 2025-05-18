<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('menu.index');
})->name('home');


Route::resource('menu', MenuController::class);
Route::resource('transaction', TransactionController::class);

Route::get('dashboard', function () {
    return redirect()->route('menu.index');
})->name('dashboard');

Route::get('/print-preview', function () {
    return Inertia::render('transaction/PrintPreview');
})->name('print-preview');

// require __DIR__ . '/settings.php';
// require __DIR__ . '/auth.php';
