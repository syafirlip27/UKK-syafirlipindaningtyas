<?php

use App\Http\Controllers\DetailSalesController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\UsersController;
use Database\Seeders\Sales;
use Illuminate\Support\Facades\Route;


Route::middleware(['guest'])->group(function () {
    Route::get('/', [UsersController::class, 'loginpage'])->name('login.page');
    Route::post('/', [UsersController::class, 'login'])->name('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [UsersController::class, 'logout'])->name('logout');
    Route::get('/dashboard',[DetailSalesController::class, 'index'])->name('dashboard');
    Route::get('/product',[ProductsController::class, 'index'])->name('product');
    Route::get('/sales',[SalesController::class, 'index'])->name('sales');
    Route::get('/download/{id}', [DetailSalesController::class, 'downloadPDF'])->name('download');
    Route::get('/logout', [UsersController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::prefix('/product')->name('product.')->group(function () {
        Route::put('/edit-stock/{id}', [ProductsController::class, 'updateStock'])->name('stock');
        Route::get('/create', [ProductsController::class, 'create'])->name('create');
        Route::post('/store', [ProductsController::class, 'store'])->name('store');
        Route::delete('/{id}', [ProductsController::class, 'destroy'])->name('delete');
        Route::get('/edit/{id}', [ProductsController::class, 'edit'])->name('edit');
        Route::put('/edit/{id}', [ProductsController::class, 'update'])->name('update');
    });

    Route::prefix('/user')->name('user.')->group(function () {
        Route::get('/', [UsersController::class, 'index'])->name('list');
        Route::get('/create', [UsersController::class, 'create'])->name('create');
        Route::post('/store', [UsersController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [UsersController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [UsersController::class, 'update'])->name('update');
        Route::delete('/{id}', [UsersController::class, 'destroy'])->name('delete');
    });
});

Route::middleware(['auth', 'is_employee'])->group(function () {

    Route::prefix('/sales')->name('sales.')->group(function () {
        Route::get('/create',[SalesController::class, 'create'])->name('create');
        Route::post('/create/post',[SalesController::class, 'store'])->name('store');
        Route::post('/create/post/createsales',[SalesController::class, 'createsales'])->name('createsales');
        Route::get('/create/post',[SalesController::class, 'post'])->name('post');
        Route::get('/print/{id}',[DetailSalesController::class, 'show'])->name('print.show');
        Route::get('/create/member/{id}', [SalesController::class, 'createmember'])->name('create.member');
        Route::get('/exportexcel', [DetailSalesController::class, 'exportexcel'])->name('exportexcel');
    });
});




// Route::get('/sales/create',[SalessController::class, 'create'])->name('sales.create');
// Route::post('/sales/create/post',[SalessController::class, 'store'])->name('sales.store');
// Route::post('/sales/create/post/createsales',[SalessController::class, 'createsales'])->name('sales.createsales');
// Route::get('/sales/create/post',[SalessController::class, 'post'])->name('sales.post');
// Route::get('/sales/print/{id}',[DetailSalesController::class, 'show'])->name('sales.print.show');
// Route::get('/sale/create/member/{id}', [SalessController::class, 'createmember'])->name('sales.create.member');




// Route::fallback(function () {
//     return redirect()->route('dashboard');
// });
