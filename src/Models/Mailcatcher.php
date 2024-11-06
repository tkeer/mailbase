<?php

namespace Axn\MailCatcher\Models;

use Illuminate\Database\Eloquent\Model;

class Mailcatcher extends Model
{
    protected $table = 'catcher_emails';

    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'sent_at'  => 'datetime',
    ];
}
