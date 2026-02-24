<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Storage Disk
    |--------------------------------------------------------------------------
    |
    | The filesystem disk used to store email attachments. If this disk is not
    | defined in your filesystems.php config, Mailbase will automatically
    | register a local disk pointing to the storage_path below.
    |
    */

    'disk' => env('MAILBASE_DISK', 'mailbase'),

    /*
    |--------------------------------------------------------------------------
    | Storage Path
    |--------------------------------------------------------------------------
    |
    | The local path used when Mailbase auto-registers its filesystem disk.
    | This is only used if the disk above is not already configured in
    | your application's filesystems.php config file.
    |
    */

    'storage_path' => storage_path('app/mailbase'),

];