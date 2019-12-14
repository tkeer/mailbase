<?php

namespace Tkeer\Mailbase;

use Illuminate\Mail\MailServiceProvider;
use Illuminate\Support\ServiceProvider;

class MailbaseServiceProvider extends ServiceProvider
{
    /**
     * Extended register the Swift Transport instance.
     *
     * @return void
     */
    public function register()
    {
    }

    public function boot()
    {
        app('swift.transport')->extend('mailbase', function ($app) {
            return new MailbaseTransport();
        });


        $this->loadMigrationsFrom(__DIR__ . '/migrations/');
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->loadViewsFrom(__DIR__ . '/views', 'mailbase');
    }
}
