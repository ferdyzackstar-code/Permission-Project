<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Dashboard\PermissionController;
use App\Http\Controllers\Dashboard\ReportController;
use App\Http\Controllers\DashboardController;
// use App\Http\Controllers\HomeController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

// Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('permissions', PermissionController::class);

    Route::get('/reports/summary', [ReportController::class, 'summary'])->name('reports.summary');
    Route::get('/reports/outlet', [ReportController::class, 'outlet'])->name('reports.outlet');
    Route::get('/reports/employee', [ReportController::class, 'employee'])->name('reports.employee');

    // Cukup satu rute ini untuk melayani semua jenis export PDF
    Route::get('/dashboard/report/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export');
});

Route::resource('dashboard/outlets', OutletController::class)->names([
    'index' => 'dashboard.outlets.index',
    'store' => 'dashboard.outlets.store',
    'update' => 'dashboard.outlets.update',
    'destroy' => 'dashboard.outlets.destroy',
]);

