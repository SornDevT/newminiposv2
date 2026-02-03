<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\UnitController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\StockImportController;
use App\Http\Controllers\API\SaleController;
use App\Http\Controllers\API\SupplierController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\ExpenseController;


Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::get('me', 'me');
});

Route::middleware('auth:api')->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::post('products/import', [ProductController::class, 'import']);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('units', UnitController::class);
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('stock-imports', StockImportController::class);
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('expenses', ExpenseController::class);
    Route::post('sales', [SaleController::class, 'store']);
    Route::get('reports', [ReportController::class, 'index']);
    Route::get('reports/export', [ReportController::class, 'export']);
    Route::get('reports/export/pdf', [ReportController::class, 'exportPdf']);
});
