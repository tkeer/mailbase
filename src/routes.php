<?php

declare(strict_types=1);

use Tkeer\Mailbase\MailController;
use Illuminate\Routing\Middleware\SubstituteBindings;

Route::group(['as' => 'mailbase::', 'prefix' => 'mailbase', 'middleware' => SubstituteBindings::class], function () {
    Route::get('/', MailController::class . '@index')->name('index');
    Route::get('/{mailbase}', MailController::class . '@show')->name('show');
});
