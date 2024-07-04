<?php

namespace Tkeer\Mailbase;

use Illuminate\Database\Eloquent\Model;

class Mailbase extends Model
{
    protected $table = 'mailbase_emails';

    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'sent_at'  => 'datetime',
    ];
}
