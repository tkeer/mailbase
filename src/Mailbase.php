<?php

namespace Tkeer\Mailbase;

use Illuminate\Database\Eloquent\Model;

class Mailbase extends Model
{
    protected $table = 'mailbase_emails';

    protected $guarded = [];

    public $timestamps = false;

    protected $dates = [ 'sent_at' ];

    protected $casts = [
        'from' => 'object',
        'to'   => 'object',
        'cc'   => 'object',
        'bcc'  => 'object',
    ];

    protected $appends = [
        'htmlTo', 'htmlFrom'
    ];

    public function implode($field)
    {
        $str = '';
        foreach ($this->$field as $email => $name) {
            $str .= "$name < $email >";
        }

        return $str;
    }

    public function getHtmlToAttribute()
    {
        return $this->implode('to');
    }

    public function getHtmlFromAttribute()
    {
        return $this->implode('from');
    }
}
