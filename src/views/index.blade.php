<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Mail Viewer</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        /* Custom animations and transitions */
        .mail-item {
            transition: all 0.2s ease-in-out;
        }

        .mail-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Smooth scrollbar */
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }

        .scrollbar-thin::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Mobile header visibility */
        @media (max-width: 767px) {
            /* Hide reset button when viewing mail details on mobile */
            .mobile-mail-viewer #reset-btn {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">

<!-- Header -->
<header id="main-header" class="bg-white shadow-sm border-b border-gray-200">
    <div class="px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <i class="fas fa-envelope text-blue-600 text-xl"></i>
                <h1 class="text-xl font-semibold text-gray-900">{{ config('app.name') }} - Outgoing Mails</h1>
            </div>
            <div class="flex items-center space-x-3">
                <!-- Reset Button -->
                <button id="reset-btn" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center space-x-2">
                    <i class="fas fa-trash-alt text-sm"></i>
                    <span class="hidden sm:inline">Clear All</span>
                </button>
                <!-- Mobile back button -->
                <button id="mobile-back-btn" class="md:hidden bg-gray-100 hover:bg-gray-200 p-2 rounded-lg transition-colors duration-200 hidden">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </button>
            </div>
        </div>
    </div>
</header>

<div class="flex h-screen bg-gray-50" style="height: calc(100vh - 73px);">

    <!-- Mail List Sidebar -->
    <div id="mail-list-panel" class="w-full md:w-80 lg:w-96 bg-white border-r border-gray-200 flex flex-col">

        <!-- Mail List Header -->
        <div class="p-4 border-b border-gray-100 bg-gray-50">
            <div class="flex items-center justify-between">
                <h2 class="font-medium text-gray-900 flex items-center">
                    <i class="fas fa-inbox text-gray-500 mr-2"></i>
                    Inbox
                </h2>
                <span class="text-sm text-gray-500 bg-gray-200 px-2 py-1 rounded-full">
                        {{ $mails->total() ?? 0 }}
                    </span>
            </div>
        </div>

        <!-- Mail List -->
        <div class="flex-1 overflow-y-auto scrollbar-thin">
            @forelse($mails as $mail)
                <div class="mail-item border-b border-gray-100 p-4 cursor-pointer hover:bg-gray-50 {{ $mail->is_read ? '' : 'bg-blue-50 border-l-4 border-l-blue-500' }}"
                     data-id="{{ $mail->id }}">

                    <div class="flex items-start justify-between mb-2">
                        <div class="flex items-center space-x-2 min-w-0 flex-1">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                {{ strtoupper(substr($mail->to, 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $mail->to }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 ml-2">
                            @if(!$mail->is_read)
                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                            @endif
                            <button class="external-link-btn text-gray-400 hover:text-blue-600 transition-colors duration-200"
                                    data-id="{{ $mail->id }}" title="Open in new tab">
                                <i class="fas fa-external-link-alt text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <h3 class="text-sm font-medium text-gray-900 mb-1 line-clamp-1">
                        {{ $mail->subject ?: 'No Subject' }}
                    </h3>

                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-clock"></i>
                            <span>{{ $mail->sent_at->diffForHumans() }}</span>
                        </div>
                        @php $attachmentList = json_decode($mail->attachments, true) ?? []; @endphp
                        @if(count($attachmentList) > 0)
                            <span class="flex items-center space-x-1 text-gray-400">
                                <i class="fas fa-paperclip"></i>
                                <span>{{ count($attachmentList) }}</span>
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">No mails found</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($mails->hasPages())
            <div class="border-t border-gray-200 p-4 bg-gray-50">
                <div class="flex items-center justify-between text-sm text-gray-700">
                    <div class="flex items-center space-x-2">
                        @if($mails->onFirstPage())
                            <button disabled class="p-2 text-gray-400 cursor-not-allowed">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                        @else
                            <a href="{{ $mails->previousPageUrl() }}" class="p-2 text-gray-600 hover:text-blue-600 transition-colors">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        @endif

                        <span class="px-3 py-1 bg-white rounded border">
                                {{ $mails->currentPage() }} of {{ $mails->lastPage() }}
                            </span>

                        @if($mails->hasMorePages())
                            <a href="{{ $mails->nextPageUrl() }}" class="p-2 text-gray-600 hover:text-blue-600 transition-colors">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        @else
                            <button disabled class="p-2 text-gray-400 cursor-not-allowed">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        @endif
                    </div>

                    <div class="text-xs text-gray-500">
                        Showing {{ $mails->firstItem() ?? 0 }}-{{ $mails->lastItem() ?? 0 }} of {{ $mails->total() ?? 0 }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Mail Viewer -->
    <div id="mail-viewer-panel" class="hidden md:flex flex-1 flex-col bg-white">

        <!-- Mail Viewer Header -->
        <div id="mail-header" class="border-b border-gray-200 p-6 bg-white">
            <div id="empty-state" class="text-center py-12">
                <i class="fas fa-envelope-open text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">Select a mail to view its content</p>
            </div>

            <div id="mail-details" class="hidden">
                <div class="flex items-start justify-between mb-4">
                    <h2 id="mail-subject" class="text-xl font-semibold text-gray-900 flex-1 mr-4"></h2>
                    <div class="flex items-center space-x-2">
                        <button id="open-external" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center space-x-2">
                            <i class="fas fa-external-link-alt"></i>
                        </button>
                    </div>
                </div>

                <div class="space-y-2 text-sm">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-user text-gray-400 w-4"></i>
                        <span class="font-medium text-gray-700">From:</span>
                        <span id="mail-from" class="text-gray-900"></span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-paper-plane text-gray-400 w-4"></i>
                        <span class="font-medium text-gray-700">To:</span>
                        <span id="mail-to" class="text-gray-900"></span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-clock text-gray-400 w-4"></i>
                        <span class="font-medium text-gray-700">Sent:</span>
                        <span id="mail-date" class="text-gray-900"></span>
                    </div>
                </div>

                <div id="mail-attachments" class="hidden mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center space-x-2 mb-2">
                        <i class="fas fa-paperclip text-gray-400"></i>
                        <span class="text-sm font-medium text-gray-700">Attachments</span>
                        <span id="attachment-count" class="text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full"></span>
                    </div>
                    <div id="attachment-list" class="flex flex-wrap gap-2"></div>
                </div>
            </div>
        </div>

        <!-- Mail Content -->
        <div class="flex-1 overflow-hidden">
            <iframe id="mail-iframe" class="w-full h-full border-0 bg-white"></iframe>
        </div>
    </div>
</div>

<!-- Reset Confirmation Modal -->
<div id="reset-modal" class="fixed inset-0 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity"></div>
        <div class="relative bg-white rounded-lg max-w-md w-full p-6 shadow-xl">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 text-center mb-2">Clear All Emails</h3>
            <p class="text-sm text-gray-500 text-center mb-6">
                Are you sure you want to delete all emails? This action cannot be undone.
            </p>
            <div class="flex space-x-3">
                <button id="cancel-reset" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                    Cancel
                </button>
                <button id="confirm-reset" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                    Delete All
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(function () {
        let currentMailId = null;

        // Handle mail item clicks
        $(".mail-item").click(function (e) {
            // Prevent event if clicking on external link button
            if ($(e.target).closest('.external-link-btn').length) {
                return;
            }

            const mailId = $(this).data('id');
            selectMail(mailId, $(this));
        });

        // Handle external link button clicks
        $('.external-link-btn').click(function(e) {
            e.stopPropagation(); // Prevent mail selection
            const mailId = $(this).data('id');
            openMailInNewTab(mailId);
        });

        // Handle mobile back button
        $('#mobile-back-btn').click(function() {
            showMailList();
        });

        // Handle open external button in viewer
        $('#open-external').click(function() {
            if (currentMailId) {
                openMailInNewTab(currentMailId);
            }
        });

        // Handle reset button click
        $('#reset-btn').click(function() {
            $('#reset-modal').removeClass('hidden');
        });

        // Handle reset modal buttons
        $('#cancel-reset').click(function() {
            $('#reset-modal').addClass('hidden');
        });

        $('#confirm-reset').click(function() {
            resetAllMails();
        });

        // Close modal when clicking outside
        $('#reset-modal').click(function(e) {
            if (e.target === this) {
                $(this).addClass('hidden');
            }
        });

        // Function to reset all mails
        function resetAllMails() {
            // Show loading state on button
            const originalText = $('#confirm-reset').html();
            $('#confirm-reset').html('<i class="fas fa-spinner fa-spin mr-2"></i>Deleting...');
            $('#confirm-reset').prop('disabled', true);

            // Make AJAX request to clear all mails
            $.ajax({
                url: '/mailbase/clear',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Hide modal
                    $('#reset-modal').addClass('hidden');

                    // Show success message (you could use a toast notification here)
                    alert('All emails have been cleared successfully!');

                    // Reload the page to refresh the mail list
                    window.location.reload();
                },
                error: function(xhr, status, error) {
                    // Handle error
                    alert('Failed to clear emails. Please try again.');
                    console.error('Reset error:', error);
                },
                complete: function() {
                    // Reset button state
                    $('#confirm-reset').html(originalText);
                    $('#confirm-reset').prop('disabled', false);
                }
            });
        }

        // Function to select and display mail
        function selectMail(mailId, mailElement) {
            currentMailId = mailId;

            // Update UI - remove previous selection and add new
            $('.mail-item').removeClass('bg-blue-100 ring-2 ring-blue-500 ring-opacity-50');
            mailElement.addClass('bg-blue-100 ring-2 ring-blue-500 ring-opacity-50')
                .removeClass('bg-blue-50 border-l-4 border-l-blue-500'); // Remove unread styling

            // Show loading state
            showLoadingState();

            // Fetch mail content
            $.get('/mailbase/' + mailId)
                .done(function (response) {
                    displayMail(response);
                    showMailViewer();
                })
                .fail(function() {
                    showErrorState();
                });
        }

        // Function to display mail content
        function displayMail(mailData) {
            $("#mail-iframe").attr('srcdoc', '<base target="_blank" /> ' + mailData.body);
            $("#mail-subject").text(mailData.subject || 'No Subject');
            $("#mail-to").text(mailData.to);
            $("#mail-from").text(mailData.from);
            $("#mail-date").text(mailData.sent_at || 'Unknown');

            // Render attachments
            renderAttachments(mailData);

            // Show mail details, hide empty state
            $("#empty-state").hide();
            $("#mail-details").removeClass('hidden').addClass('fade-in');
        }

        function renderAttachments(mailData) {
            let attachments = [];
            try {
                attachments = typeof mailData.attachments === 'string'
                    ? JSON.parse(mailData.attachments)
                    : (mailData.attachments || []);
            } catch (e) {
                attachments = [];
            }

            if (!Array.isArray(attachments) || attachments.length === 0) {
                $('#mail-attachments').addClass('hidden');
                return;
            }

            $('#attachment-count').text(attachments.length);
            const $list = $('#attachment-list').empty();

            attachments.forEach(function (att, index) {
                const previewUrl = '/mailbase/' + mailData.id + '/attachments/' + index;
                const downloadUrl = previewUrl + '/download';

                const $chip = $('<div>')
                    .addClass('flex items-center space-x-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm');

                $chip.append($('<i>').addClass(getFileIcon(att.mime_type) + ' text-gray-500'));
                $chip.append(
                    $('<a>').attr('href', previewUrl).attr('target', '_blank')
                        .addClass('text-blue-600 hover:text-blue-800 font-medium truncate max-w-[200px]')
                        .text(att.filename)
                );
                $chip.append($('<span>').addClass('text-gray-400 text-xs').text(formatFileSize(att.size)));
                $chip.append(
                    $('<a>').attr('href', downloadUrl).attr('title', 'Download')
                        .addClass('text-gray-400 hover:text-gray-600')
                        .append($('<i>').addClass('fas fa-download text-xs'))
                );

                $list.append($chip);
            });

            $('#mail-attachments').removeClass('hidden');
        }

        function formatFileSize(bytes) {
            if (!bytes || bytes === 0) return '0 B';
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / 1048576).toFixed(1) + ' MB';
        }

        function getFileIcon(mimeType) {
            if (!mimeType) return 'fas fa-file';
            if (mimeType.startsWith('image/')) return 'fas fa-file-image';
            if (mimeType === 'application/pdf') return 'fas fa-file-pdf';
            if (mimeType.includes('spreadsheet') || mimeType.includes('excel')) return 'fas fa-file-excel';
            if (mimeType.includes('word') || mimeType.includes('document')) return 'fas fa-file-word';
            if (mimeType.includes('zip') || mimeType.includes('archive')) return 'fas fa-file-archive';
            if (mimeType.startsWith('text/')) return 'fas fa-file-alt';
            return 'fas fa-file';
        }

        // Function to open mail in new tab
        function openMailInNewTab(mailId) {
            $.get('/mailbase/' + mailId)
                .done(function (response) {
                    let attachments = [];
                    try {
                        attachments = typeof response.attachments === 'string'
                            ? JSON.parse(response.attachments)
                            : (response.attachments || []);
                    } catch (e) {
                        attachments = [];
                    }

                    let attachmentHtml = '';
                    if (Array.isArray(attachments) && attachments.length > 0) {
                        const items = attachments.map(function (att, index) {
                            const previewUrl = '/mailbase/' + response.id + '/attachments/' + index;
                            const downloadUrl = previewUrl + '/download';
                            return '<div style="display:inline-flex;align-items:center;gap:8px;background:#f7fafc;border:1px solid #e2e8f0;border-radius:6px;padding:6px 12px;font-size:13px;">'
                                + '<a href="' + previewUrl + '" target="_blank" style="color:#3182ce;text-decoration:none;font-weight:500;">' + att.filename + '</a>'
                                + '<span style="color:#a0aec0;font-size:11px;">' + formatFileSize(att.size) + '</span>'
                                + '<a href="' + downloadUrl + '" style="color:#718096;text-decoration:none;" title="Download">&#x2B07;</a>'
                                + '</div>';
                        }).join(' ');

                        attachmentHtml = '<div style="padding:12px 20px;border-bottom:1px solid #e2e8f0;background:#f8fafc;">'
                            + '<div style="font-size:13px;font-weight:600;color:#4a5568;margin-bottom:8px;">Attachments (' + attachments.length + ')</div>'
                            + '<div style="display:flex;flex-wrap:wrap;gap:8px;">' + items + '</div>'
                            + '</div>';
                    }

                    const newWindow = window.open('', '_blank');
                    newWindow.document.write(`
                            <!DOCTYPE html>
                            <html>
                            <head>
                                <title>${response.subject || 'Mail'}</title>
                                <base target="_blank" />
                                <style>
                                    body { margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
                                    .mail-header { background: #f8fafc; padding: 20px; border-bottom: 1px solid #e2e8f0; }
                                    .mail-header h1 { margin: 0 0 10px 0; color: #1a202c; }
                                    .mail-meta { color: #4a5568; font-size: 14px; }
                                    .mail-content { padding: 20px; }
                                </style>
                            </head>
                            <body>
                                <div class="mail-header">
                                    <h1>${response.subject || 'No Subject'}</h1>
                                    <div class="mail-meta">
                                        <div><strong>From:</strong> ${response.from}</div>
                                        <div><strong>To:</strong> ${response.to}</div>
                                        <div><strong>Date:</strong> ${response.sent_at || 'Unknown'}</div>
                                    </div>
                                </div>
                                ${attachmentHtml}
                                <div class="mail-content">
                                    ${response.body}
                                </div>
                            </body>
                            </html>
                        `);
                    newWindow.document.close();
                })
                .fail(function() {
                    alert('Failed to load mail content');
                });
        }

        // Responsive functions
        function showMailViewer() {
            if (window.innerWidth < 768) { // Mobile
                $('#mail-list-panel').hide();
                $('#mail-viewer-panel').removeClass('hidden').addClass('flex');
                $('#mobile-back-btn').removeClass('hidden');
                $('body').addClass('mobile-mail-viewer');
            } else { // Desktop
                $('#mail-viewer-panel').removeClass('hidden').addClass('flex');
            }
        }

        function showMailList() {
            if (window.innerWidth < 768) { // Mobile
                $('#mail-viewer-panel').removeClass('flex').addClass('hidden');
                $('#mail-list-panel').show();
                $('#mobile-back-btn').addClass('hidden');
                // Show header when back to mail list
                $('#main-header').removeClass('header-mobile-hidden');

                $('body').removeClass('mobile-mail-viewer');
            }
        }

        function showLoadingState() {
            $("#mail-iframe").attr('srcdoc', '<div style="display: flex; align-items: center; justify-content: center; height: 100vh; font-family: sans-serif; color: #666;"><i class="fas fa-spinner fa-spin" style="margin-right: 10px;"></i> Loading...</div>');
        }

        function showErrorState() {
            $("#mail-iframe").attr('srcdoc', '<div style="display: flex; align-items: center; justify-content: center; height: 100vh; font-family: sans-serif; color: #e53e3e;"><i class="fas fa-exclamation-triangle" style="margin-right: 10px;"></i> Failed to load mail content</div>');
        }

        // Handle window resize
        $(window).resize(function() {
            if (window.innerWidth >= 768 && $('#mail-viewer-panel').hasClass('hidden')) {
                $('#mail-list-panel').show();
                $('#mail-viewer-panel').removeClass('hidden').addClass('flex');
                $('#mobile-back-btn').addClass('hidden');
                // Show header on desktop
                $('#main-header').removeClass('header-mobile-hidden');
            }
        });

        // Auto-select first mail on desktop
        if (window.innerWidth >= 768) {
            $(".mail-item").first().click();
        }
    });
</script>

</body>
</html>