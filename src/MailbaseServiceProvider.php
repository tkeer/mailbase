<?php

declare(strict_types=1);

namespace Tkeer\Mailbase;

use Illuminate\Mail\MailManager;
use Tkeer\Mailbase\Commands\ClearMailbaseCommand;
use Tkeer\Mailbase\Commands\TestMailbaseCommand;
use Illuminate\Support\ServiceProvider;

class MailbaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config/mailbase.php', 'mailbase');
    }

    public function boot(): void
    {
        // Add `mailbase` to mailers config
        config([
            'mail.mailers.mailbase' => ['transport' => 'mailbase'],
        ]);

        app(MailManager::class)->extend('mailbase', function ($app) {
            return new MailbaseTransport();
        });

        // Auto-register the mailbase filesystem disk if not already configured
        $diskName = config('mailbase.disk', 'mailbase');
        if (! config("filesystems.disks.{$diskName}")) {
            config([
                "filesystems.disks.{$diskName}" => [
                    'driver' => 'local',
                    'root' => config('mailbase.storage_path', storage_path('app/mailbase')),
                ],
            ]);
        }

        $this->loadMigrationsFrom(__DIR__ . '/migrations/');
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->loadViewsFrom(__DIR__ . '/views', 'mailbase');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ClearMailbaseCommand::class,
                TestMailbaseCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/config/mailbase.php' => config_path('mailbase.php'),
            ], 'mailbase-config');
        }
    }
}
