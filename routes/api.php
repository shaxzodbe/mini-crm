<?php

use App\Http\Controllers\Api\TicketApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('tickets')->group(function () {
    Route::post('/', [TicketApiController::class, 'store'])->name('api.tickets.store');
    Route::get('statistics', [TicketApiController::class, 'statistics'])
        ->name('api.tickets.statistics');
});
