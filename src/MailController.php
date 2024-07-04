<?php

declare(strict_types=1);

namespace Tkeer\Mailbase;

use Illuminate\Routing\Controller;

class MailController extends Controller
{
    public function index()
    {
        $mails = Mailbase::query()->latest('sent_at')->paginate(20);

        return view('mailbase::index', ['mails' => $mails]);
    }

    public function show(Mailbase $mailbase)
    {
        $mailbase->update(['is_read' => 1]);

        return response()->json($mailbase);
    }

}
