<?php

declare(strict_types=1);

namespace Tkeer\Mailbase;

use Illuminate\Routing\Controller;

class MailController extends Controller
{
    public function index()
    {
        $mails = Mailbase::latest('sent_at')->paginate(20);

        return view('mailbase::index', compact('mails'));
    }

    public function show(Mailbase $mailbase)
    {
        $mailbase->update(['is_read' => 1]);

        return response()->json($mailbase);
    }

}
