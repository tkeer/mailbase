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

    public function show($id)
    {
        /**
         * not using route model binding for older versions
         */
        $mailbase = Mailbase::findOrFail($id);
        $mailbase->is_read = 1;
        $mailbase->save();

        return response()->json($mailbase);
    }

}
