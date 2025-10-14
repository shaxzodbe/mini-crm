<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Manager\TicketController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('widget', function () {
    return view('widget.feedback');
})->name('feedback.widget');

Route::middleware(['auth', 'role:manager'])->prefix('manager')->group(function () {
    Route::get('/', function() {
        return redirect()->route('manager.tickets.index');
    })->name('manager.dashboard');
    Route::get('tickets', [TicketController::class, 'index'])->name('manager.tickets.index');
    Route::get('tickets/{ticket}', [TicketController::class, 'show'])->name('manager.tickets.show');
    Route::patch('tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('manager.tickets.update_status');
});

Auth::routes();

Route::get('home', [HomeController::class, 'index'])->name('home');
