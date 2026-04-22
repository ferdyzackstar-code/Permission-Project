<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Dashboard\PermissionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

// Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    Route::get('users/downloadImportTemplate', [UserController::class, 'downloadImportTemplate'])->name('users.downloadImportTemplate');
    Route::post('users/import', [UserController::class, 'import'])->name('users.import');
    Route::get('users/export', [UserController::class, 'export'])->name('users.export');

    Route::get('products/downloadImportTemplate', [ProductController::class, 'downloadImportTemplate'])->name('products.downloadImportTemplate');
    Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
    Route::get('products/export', [ProductController::class, 'export'])->name('products.export');

    Route::get('suppliers/downloadImportTemplate', [SupplierController::class, 'downloadImportTemplate'])->name('suppliers.downloadImportTemplate');
    Route::post('suppliers/import', [SupplierController::class, 'import'])->name('suppliers.import');
    Route::get('suppliers/export', [SupplierController::class, 'export'])->name('suppliers.export');

    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);

    Route::resource('products', ProductController::class);
    Route::get('/get-subcategories/{parentId}', [ProductController::class, 'getSubCategories'])->name('products.getSubCategories');
    Route::resource('categories', CategoryController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('purchases', PurchaseController::class);

    Route::get('/orders/confirmation', [OrderController::class, 'confirmation'])->name('orders.confirmation');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/pos', [OrderController::class, 'pos'])->name('orders.pos');
    Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');

    Route::get('/orders/{id}', [OrderController::class, 'show'])
        ->name('orders.show')
        ->where('id', '[0-9]+');
    Route::get('/orders/{id}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');
    Route::post('/orders/{order}/approve', [OrderController::class, 'approve'])->name('orders.approve');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    Route::resource('outlets', OutletController::class);

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
    Route::get('/reports/income', [ReportController::class, 'incomeReport'])->name('reports.income');
    Route::get('//reports/daily', [ReportController::class, 'dailyReport'])->name('reports.daily');
    Route::get('/reports/daily/export', [ReportController::class, 'exportDailyPdf'])->name('reports.daily.export');
    Route::get('/reports/monthly', [ReportController::class, 'monthlyReport'])->name('reports.monthly');
    Route::get('/reports/monthly/export', [ReportController::class, 'exportMonthlyPdf'])->name('reports.monthly.export');
    Route::get('/reports/hourly', [ReportController::class, 'hourlyReport'])->name('reports.hourly');
    Route::get('/reports/hourly/export', [ReportController::class, 'exportHourlyPdf'])->name('reports.hourly.export');

});

Route::group(['middleware' => ['auth']], function () {
    Route::get('dashboard/profile', [UserController::class, 'profile'])->name('profile.index');
    Route::put('dashboard/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');
});

Route::get('/preview-error-419', function () {
    return view('errors.419');
});
Route::get('/preview-error-429', function () {
    return view('errors.429');
});
Route::get('/preview-error-503', function () {
    return view('errors.503');
});
