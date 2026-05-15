<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\OrderController;

Route::get('/', function () {
    return view('welcome');
});

// Print Order Invoice
Route::get('/admin/orders/{id}/print', [OrderController::class, 'print'])->name('orders.print');
