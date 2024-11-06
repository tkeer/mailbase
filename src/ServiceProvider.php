<?php

namespace Axn\MailCatcher;

use Axn\MailCatcher\Console\Commands\ClearMailbaseCommand;
use Axn\MailCatcher\Console\Commands\TestMailbaseCommand;
use Axn\MailCatcher\Models\Mailcatcher;
use Illuminate\Mail\MailManager;
use Illuminate\Mail\MailServiceProvider;
use Illuminate\Support\ServiceProvider;

class ServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // add `mailbase` to mailers config
        config([
            'mail.mailers.mailcatcher' => ['transport' => 'mailcatcher']
        ]);

        app(MailManager::class)->extend('mailcatcher', function ($app) {
            return new MailcatcherTransport();
        });

        $this->loadMigrationsFrom(__DIR__ . '/migrations/');
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->loadViewsFrom(__DIR__ . '/views', 'mailcatcher');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ClearMailbaseCommand::class,
                TestMailbaseCommand::class,
            ]);
        }
    }
}
