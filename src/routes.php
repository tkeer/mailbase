<?php

declare(strict_types=1);

use Tkeer\Mailbase\MailController;

Route::group(['as' => 'mailbase::', 'prefix' => 'mailbase'], function () {
    Route::get('/', MailController::class . '@index')->name('index');
    Route::get('/{mailbase}', MailController::class . '@show')->name('show');
});
