<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\BillOfMaterialController;
use App\Http\Controllers\JitNotificationController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect()->route('home');
});

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Admin & Supervisor: Full access
    // Staff: Full POS access
    Route::middleware('role:admin,supervisor,staff')->group(function () {
        Route::get('/stock-adjustments/create', [App\Http\Controllers\StockAdjustmentController::class, 'create'])->name('stock_adjustments.create');
        Route::post('/stock-adjustments', [App\Http\Controllers\StockAdjustmentController::class, 'store'])->name('stock_adjustments.store');
        Route::get('/stock-adjustments', [App\Http\Controllers\StockAdjustmentController::class, 'index'])->name('stock_adjustments.index'); 
    });

    // Admin & Supervisor: Full access
    // Staff: Index & Show only
    Route::middleware('role:admin,supervisor')->group(function () {
        Route::get('/raw-materials/create', [RawMaterialController::class, 'create'])->name('raw_materials.create');
        Route::post('/raw-materials', [RawMaterialController::class, 'store'])->name('raw_materials.store');
        Route::get('/raw-materials/recalculate-analytics', [RawMaterialController::class, 'forceRecalculateAnalytics'])->name('raw_materials.recalculate');
        Route::get('/raw-materials/{slug}/edit', [RawMaterialController::class, 'edit'])->name('raw_materials.edit');
        Route::put('/raw-materials/{slug}', [RawMaterialController::class, 'update'])->name('raw_materials.update');
        Route::put('/raw-materials/{slug}/delete', [RawMaterialController::class, 'destroy'])->name('raw_materials.delete');

        Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::get('/suppliers/{slug}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('/suppliers/{slug}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::put('/suppliers/{slug}/delete', [SupplierController::class, 'destroy'])->name('suppliers.delete');

        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{slug}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{slug}', [ProductController::class, 'update'])->name('products.update');
        Route::put('/products/{slug}/delete', [ProductController::class, 'destroy'])->name('products.delete');
        Route::get('/products/{slug}/raw-materials', [ProductController::class, 'showRawMaterials'])->name('products.raw_materials');

        Route::get('/bill-of-materials/create', [BillOfMaterialController::class, 'create'])->name('bill_of_materials.create');
        Route::post('/bill-of-materials', [BillOfMaterialController::class, 'store'])->name('bill_of_materials.store');
        Route::get('/bill-of-materials/{slug}/edit', [BillOfMaterialController::class, 'edit'])->name('bill_of_materials.edit');
        Route::put('/bill-of-materials/{slug}', [BillOfMaterialController::class, 'update'])->name('bill_of_materials.update');
        Route::put('/bill-of-materials/{slug}/delete', [BillOfMaterialController::class, 'destroy'])->name('bill_of_materials.delete');
    });

    Route::middleware('role:admin,supervisor,staff')->group(function () {
        Route::get('/raw-materials', [RawMaterialController::class, 'index'])->name('raw_materials');
        Route::get('/raw-materials/{slug}', [RawMaterialController::class, 'show'])->name('raw_materials.show');

        Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers');
        Route::get('/suppliers/{slug}', [SupplierController::class, 'show'])->name('suppliers.show');

        Route::get('/products', [ProductController::class, 'index'])->name('products');
        Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

        Route::get('/bill-of-materials', [BillOfMaterialController::class, 'index'])->name('bill_of_materials');
        Route::get('/bill-of-materials/{slug}', [BillOfMaterialController::class, 'show'])->name('bill_of_materials.show');
    });


    // Admin only: User Management
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.delete');
    });

    // Admin, Supervisor, Staff: JIT Notifications
    Route::middleware('role:admin,supervisor,staff')->group(function () {
        Route::get('/jit-notifications/{jitNotification}/mark-as-read', [JitNotificationController::class, 'markAsReadAndRedirect'])
            ->name('jit_notifications.mark_as_read');
    });
});

Route::get('/restricted', function () {
    return view('content.error.restricted');
})->name('restricted.page'); 

Route::get('/tes-cepat', function () {
    return view('welcome');
});