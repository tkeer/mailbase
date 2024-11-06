<?php

declare(strict_types=1);

use Axn\MailCatcher\MailController;
use Illuminate\Support\Facades\Route;

Route::prefix('mail-sandbox')
    ->name('mail-sandbox.')
    ->group(function () {
        Route::get('/', [MailController::class, 'index'])
            ->name('index');
        Route::get('/', [MailController::class, 'show'])
            ->name('show');
    });

// Route::group(['as' => 'mail-sandbox::', 'prefix' => 'mailbase', 'middleware' => SubstituteBindings::class], function () {
//     Route::get('/', MailController::class . '@index')->name('index');
//     Route::get('/{mailcatcher}', MailController::class . '@show')->name('show');
// });
