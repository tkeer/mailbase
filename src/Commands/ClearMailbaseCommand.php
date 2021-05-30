<?php

namespace Tkeer\Mailbase\Commands;

use Illuminate\Console\Command;
use Tkeer\Mailbase\Mailbase;

class ClearMailbaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailbase:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all emails stored by Mailbase in the database.';

    /**
     * Execute the console command. Attempt to delete
     * all of the Mailbase items stored in the DB.
     * If an exception is thrown, catch it and
     * display it in the console.
     *
     * @return void
     */
    public function handle()
    {
        $this->line('Clearing stored Mailbase emails.');

        Mailbase::truncate();

        $this->info('Cleared stored Mailbase emails.');
    }
}
