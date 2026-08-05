<?php

use App\Http\Controllers\Backend\MessageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'Devotion Technology is working!';
});

    Route::prefix('admin/messages')->name('admin.messages.')->middleware(['auth:admin'])->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::post('/{conversation}/send', [MessageController::class, 'send'])->name('send');
    
        // New:
        Route::post('/{conversation}/poll', [MessageController::class, 'poll'])->name('poll');
        Route::post('/poll/{message}/vote', [MessageController::class, 'pollVote'])->name('poll.vote');
        Route::post('/{conversation}/contact', [MessageController::class, 'contact'])->name('contact');
        Route::post('/{conversation}/event', [MessageController::class, 'event'])->name('event');
    });