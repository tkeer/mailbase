<?php

namespace Tkeer\Mailbase\Commands;

use Illuminate\Console\Command;
use Illuminate\Mail\MailManager;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TestMailbaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailbase:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an email through mailbase to test if it works.';

    /**
     * @return void
     */
    public function handle()
    {
        /**
         * @var $mailer MailManager
         */
        $mailer = app(MailManager::class);

        if ($mailer->getDefaultDriver() !== 'mailbase') {
            $this->output->error("Mailbase may not be set as your mail driver.\nPlease don't forget to set mailbase as MAIL_MAILER in your env file.");
            return;
        }

        Mail::mailer('mailbase')->raw('Hello from Mailbase', function (Message $msg) {

            $appName = config('app.name');
            $to = "admin@" . Str::slug($appName) . ".local";
            $msg->to($to, 'Admin')
                ->subject('Test Email')
                ->text("Hi, welcome to $appName!");

            $this->output->success("Mail is sent! Please it at /mailbase");
        });
    }
}
