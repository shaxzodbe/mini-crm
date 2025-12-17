<?php

use App\Http\Controllers\Api\TicketController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::get('/ticket-statistics', [TicketController::class, 'statistics']);
});
