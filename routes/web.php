<?php

declare(strict_types=1);

use Axn\MailCatcher\MailController;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;

Route::group(['as' => 'mailbase::', 'prefix' => 'mailbase', 'middleware' => SubstituteBindings::class], function () {
    Route::get('/', MailController::class . '@index')->name('index');
    Route::get('/{mailbase}', MailController::class . '@show')->name('show');
});
