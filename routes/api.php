<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuotationController;

Route::middleware(['jwt'])->post('/quotation', [QuotationController::class, 'calculate'])->name('quotation.calculate');