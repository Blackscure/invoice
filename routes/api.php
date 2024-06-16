<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MPesaController;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('invoices', [InvoiceController::class, 'store']);

Route::post('/pay/{invoice_number}', [MPesaController::class, 'pay']);
Route::post('/mpesa/callback', [MPesaController::class, 'callback'])->name('mpesa.callback');

