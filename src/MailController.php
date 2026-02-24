<?php

declare(strict_types=1);

namespace Tkeer\Mailbase;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MailController extends Controller
{
    public function index()
    {
        $mails = Mailbase::query()->latest('sent_at')->paginate(20);

        return view('mailbase::index', ['mails' => $mails]);
    }

    public function show(Mailbase $mailbase): JsonResponse
    {
        $mailbase->update(['is_read' => 1]);

        return response()->json($mailbase);
    }

    public function attachment(Mailbase $mailbase, int $index): StreamedResponse
    {
        $attachment = $this->resolveAttachment($mailbase, $index);
        $disk = Storage::disk(config('mailbase.disk', 'mailbase'));

        return $disk->response($attachment['storage_path'], $attachment['filename'], [
            'Content-Type' => $attachment['mime_type'],
        ]);
    }

    public function downloadAttachment(Mailbase $mailbase, int $index): StreamedResponse
    {
        $attachment = $this->resolveAttachment($mailbase, $index);
        $disk = Storage::disk(config('mailbase.disk', 'mailbase'));

        return $disk->download($attachment['storage_path'], $attachment['filename']);
    }

    /**
     * Clear all emails from the database and delete stored attachments.
     */
    public function clear(): JsonResponse
    {
        try {
            Mailbase::truncate();

            $disk = Storage::disk(config('mailbase.disk', 'mailbase'));
            foreach ($disk->allDirectories() as $dir) {
                $disk->deleteDirectory($dir);
            }

            return response()->json([
                'success' => true,
                'message' => 'All emails have been cleared successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear emails: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Resolve an attachment from the mail's JSON metadata by index.
     *
     * @return array{filename: string, mime_type: string, size: int, storage_path: string}
     */
    protected function resolveAttachment(Mailbase $mailbase, int $index): array
    {
        $attachments = json_decode($mailbase->attachments ?? '[]', true);

        if (! is_array($attachments) || ! isset($attachments[$index])) {
            throw new NotFoundHttpException('Attachment not found.');
        }

        $attachment = $attachments[$index];
        $disk = Storage::disk(config('mailbase.disk', 'mailbase'));

        if (! $disk->exists($attachment['storage_path'])) {
            throw new NotFoundHttpException('Attachment file not found on disk.');
        }

        return $attachment;
    }
}
