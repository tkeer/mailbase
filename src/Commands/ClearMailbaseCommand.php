<?php

declare(strict_types=1);

namespace Tkeer\Mailbase\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
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
    protected $description = 'Delete all emails and attachment files stored by Mailbase.';

    public function handle(): void
    {
        $this->line('Clearing stored Mailbase emails.');

        Mailbase::truncate();

        $disk = Storage::disk(config('mailbase.disk', 'mailbase'));
        foreach ($disk->allDirectories() as $dir) {
            $disk->deleteDirectory($dir);
        }

        $this->info('Cleared stored Mailbase emails and attachments.');
    }
}
