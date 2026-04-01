<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Dashboard\PermissionController;
use App\Http\Controllers\Dashboard\ReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

// Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    /* Route::get('/reports/transactions', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/product', [ReportController::class, 'productReport'])->name('reports.product');
    Route::get('/reports/summary', [ReportController::class, 'summary'])->name('reports.summary');
    Route::get('/reports/outlet', [ReportController::class, 'outlet'])->name('reports.outlet');
    Route::get('/reports/employee', [ReportController::class, 'employee'])->name('reports.employee'); */

    Route::post('users/import', [UserController::class, 'import'])->name('users.import');
    Route::get('users/export', [UserController::class, 'export'])->name('users.export');
    Route::get('users/downloadImportTemplate', [UserController::class, 'downloadImportTemplate'])->name('users.downloadImportTemplate');

    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);

    Route::resource('products', ProductController::class);
    Route::get('/get-subcategories/{parentId}', [ProductController::class, 'getSubCategories'])->name('products.getSubCategories');
    Route::resource('categories', CategoryController::class);
    Route::resource('suppliers', SupplierController::class);

    // POS & Orders Routes
    Route::get('/orders/pos', [OrderController::class, 'pos'])->name('orders.pos');
    Route::post('/orders/add-to-cart', [OrderController::class, 'addToCart'])->name('orders.addToCart');
    Route::post('/orders/remove-from-cart', [OrderController::class, 'removeFromCart'])->name('orders.removeFromCart');
    Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');
    Route::put('/orders/{order}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('orders.confirmPayment');

    Route::get('/dashboard/report/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export');

    Route::resource('outlets', OutletController::class);
});

// Route sementara untuk cek desain error
Route::get('/preview-error-419', function () {
    return view('errors.419');
});
Route::get('/preview-error-429', function () {
    return view('errors.429');
});
Route::get('/preview-error-503', function () {
    return view('errors.503');
});

