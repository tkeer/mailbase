<?php

declare(strict_types=1);

namespace Axn\MailCatcher\Http\Controllers;

use Axn\MailCatcher\Models\Mailcatcher;
use Illuminate\Routing\Controller;

class MailController extends Controller
{
    public function index()
    {
        $mails = Mailcatcher::query()->latest('sent_at')->paginate(20);

        return view('mailbase::index', ['mails' => $mails]);
    }

    public function show(Mailcatcher $mailcatcher)
    {
        $mailcatcher->update(['is_read' => 1]);

        return response()->json($mailcatcher);
    }

}
