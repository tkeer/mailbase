<?php

declare(strict_types=1);

namespace Tkeer\Mailbase;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;

class MailbaseTransport extends AbstractTransport
{
    protected function doSend(SentMessage $message): void
    {
        /** @var \Symfony\Component\Mime\Email $email */
        $email = $message->getOriginalMessage();

        $subject = $email->getSubject();
        $from = collect($email->getFrom())->map->toString()->implode("\n");
        $to = collect($email->getTo())->map->toString()->implode("\n");
        $cc = collect($email->getCc())->map->toString()->implode("\n");
        $bcc = collect($email->getBcc())->map->toString()->implode("\n");
        $body = $email->getHtmlBody() ?: $email->getTextBody();
        $headers = $email->getHeaders()->toString();

        $attachments = $this->storeAttachments($email->getAttachments());

        Mailbase::create([
            'from'        => $from,
            'to'          => $to,
            'cc'          => $cc,
            'bcc'         => $bcc,
            'subject'     => $subject,
            'body'        => $body,
            'headers'     => $headers,
            'attachments' => json_encode($attachments),
            'sent_at'     => now()->toDateTimeString(),
        ]);
    }

    /**
     * Store attachment files to disk and return metadata array.
     *
     * @param  iterable<\Symfony\Component\Mime\Part\DataPart>  $parts
     * @return array<int, array{filename: string, mime_type: string, size: int, storage_path: string}>
     */
    protected function storeAttachments(iterable $parts): array
    {
        $disk = Storage::disk(config('mailbase.disk', 'mailbase'));
        $date = now()->format('Y-m-d');
        $attachments = [];

        foreach ($parts as $part) {
            $filename = $part->getFilename() ?? 'attachment';
            $content = $part->getBody();
            $mimeType = $part->getContentType();

            $storagePath = $date . '/' . Str::uuid() . '_' . $filename;
            $disk->put($storagePath, $content);

            $attachments[] = [
                'filename'     => $filename,
                'mime_type'    => $mimeType,
                'size'         => strlen($content),
                'storage_path' => $storagePath,
            ];
        }

        return $attachments;
    }

    public function __toString(): string
    {
        return 'mailbase';
    }
}
