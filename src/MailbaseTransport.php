<?php

namespace Tkeer\Mailbase;

use Illuminate\Mail\Transport\Transport;
use Swift_Mime_SimpleMessage;

class MailbaseTransport extends Transport
{

    /**
     * @inheritDoc
     *
     * @param Swift_Mime_SimpleMessage $message
     * @param null $failedRecipients
     * @return int|void
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        Mailbase::create([
            'from'        => $message->getFrom(),
            'to'          => $message->getTo(),
            'cc'          => $message->getCc(),
            'bcc'         => $message->getBcc(),
            'subject'     => $message->getSubject(),
            'body'        => $message->getBody(),
            'headers'     => (string)$message->getHeaders(),
            'attachments' => $message->getChildren() ? implode("\n\n", $message->getChildren()) : null,
            'sent_at'     => now()->toDateTimeString(),
        ]);


    }
}
