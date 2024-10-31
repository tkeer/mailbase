<?php

namespace Axn\MailCatcher;

use Axn\MailCatcher\Console\Commands\ClearMailbaseCommand;
use Axn\MailCatcher\Console\Commands\TestMailbaseCommand;
use Illuminate\Mail\MailManager;
use Illuminate\Mail\MailServiceProvider;
use Illuminate\Support\ServiceProvider;

class ServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // add `mailbase` to mailers config
        config([
            'mail.mailers.mailbase' => ['transport' => 'mailbase']
        ]);

        app(MailManager::class)->extend('mailbase', function ($app) {
            return new MailbaseTransport();
        });

        $this->loadMigrationsFrom(__DIR__ . '/migrations/');
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->loadViewsFrom(__DIR__ . '/views', 'mailbase');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ClearMailbaseCommand::class,
                TestMailbaseCommand::class,
            ]);
        }
    }
}
