@extends('layouts.admin')

@section('title', $room->name)
@section('admin_page_title', 'Chat: ' . $room->name)
@section('admin_nav_chat', 'active')

@push('admin-styles')
<style>
    .chat-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - var(--sb-topbar-h) - 100px);
        background: var(--sb-card);
        border: 1px solid var(--sb-border);
        border-radius: 12px;
        overflow: hidden;
    }
    .chat-header {
        padding: 16px 24px;
        border-bottom: 1px solid var(--sb-border);
        background: #F8FAFC;
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .chat-header-icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
    }
    .chat-header-icon.class-chat { background: #EFF6FF; color: var(--sb-accent); }
    .chat-header-icon.category-chat { background: #F5F3FF; color: var(--sb-purple); }
    
    .chat-messages {
        flex: 1;
        padding: 24px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 16px;
        background: #F1F5F9;
    }
    
    .chat-bubble-row {
        display: flex;
        width: 100%;
    }
    .chat-bubble-row.me { justify-content: flex-end; }
    
    .chat-bubble-wrapper {
        max-width: 70%;
        display: flex;
        flex-direction: column;
    }
    .chat-bubble-row.me .chat-bubble-wrapper { align-items: flex-end; }
    
    .chat-sender-name {
        font-size: 11px;
        color: var(--sb-muted);
        margin-bottom: 4px;
        padding: 0 4px;
        font-weight: 600;
    }
    
    .chat-bubble {
        padding: 12px 16px;
        border-radius: 16px;
        font-size: 14px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        position: relative;
    }
    
    /* Others Bubble */
    .chat-bubble-row:not(.me) .chat-bubble {
        background: #FFFFFF;
        color: var(--sb-text);
        border: 1px solid var(--sb-border);
        border-bottom-left-radius: 4px;
    }
    
    /* My Bubble */
    .chat-bubble-row.me .chat-bubble {
        background: var(--sb-accent);
        color: #FFFFFF;
        border-bottom-right-radius: 4px;
    }
    
    .chat-timestamp {
        font-size: 10px;
        opacity: 0.7;
        margin-top: 4px;
        text-align: right;
    }
    .chat-bubble-row.me .chat-timestamp { color: rgba(255,255,255,0.8); }
    
    .chat-input-area {
        padding: 16px 24px;
        background: #FFFFFF;
        border-top: 1px solid var(--sb-border);
    }
    
    .chat-input-wrapper {
        display: flex;
        gap: 12px;
        background: #F8FAFC;
        border: 1px solid var(--sb-border);
        border-radius: 24px;
        padding: 8px 16px;
        align-items: center;
    }
    
    .chat-input-wrapper input {
        border: none;
        background: transparent;
        flex: 1;
        outline: none;
        font-size: 14.5px;
    }
    
    .btn-send {
        background: var(--sb-accent);
        color: white;
        border: none;
        border-radius: 50%;
        width: 36px; height: 36px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: transform 0.1s;
    }
    .btn-send:hover { transform: scale(1.05); background: var(--sb-accent-hover); }
</style>
@endpush

@section('admin_content')

<div class="chat-container shadow-sm">
    <div class="chat-header">
        <a href="{{ route('admin.chat.index') }}" class="btn btn-sm btn-light border me-2 shadow-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="chat-header-icon {{ $room->type === 'class' ? 'class-chat' : 'category-chat' }}">
            @if($room->type === 'class')
                <i class="bi bi-book"></i>
            @else
                <i class="bi bi-briefcase"></i>
            @endif
        </div>
        <div>
            <h5 class="m-0 fw-bold">{{ $room->name }}</h5>
            <div class="text-muted small">
                @if($room->type === 'class')
                    Class: {{ $room->schoolClass->standard ?? '' }} {{ $room->schoolClass->section ?? '' }}
                @else
                    Staff Category: {{ $room->category->name ?? '' }}
                @endif
            </div>
        </div>
        
        <div class="ms-auto d-flex align-items-center">
            <span id="chatHeaderStatus" class="badge bg-light text-secondary border me-3">
                <i class="bi bi-circle-fill text-success" style="font-size: 8px; margin-right: 4px; vertical-align: middle;"></i>
                <span id="onlineCountText">Checking...</span>
            </span>
        </div>
    </div>

    <div class="chat-messages" id="chatMessagesBox">
        @forelse($messages as $msg)
            @include('admin.chat.partials.message', ['msg' => $msg])
        @empty
            <div class="text-center text-muted my-auto" id="noMessagesMsg">
                <i class="bi bi-chat-dots" style="font-size: 40px; opacity: 0.5;"></i>
                <p class="mt-2">No messages yet. Be the first to say hi!</p>
            </div>
        @endforelse
    </div>

    <div id="typingIndicator" class="text-muted small px-4 py-2 bg-light border-top" style="display: none; font-style: italic;">
        Someone is typing...
    </div>

    <div class="chat-input-area">
        <form id="chatForm" class="m-0" method="POST" action="{{ route('admin.chat.send', $room->id) }}">
            @csrf
            <div class="chat-input-wrapper shadow-sm">
                <input type="text" name="message" id="chatInput" placeholder="Type a message..." required autocomplete="off" autofocus>
                <button type="submit" class="btn-send shadow-sm" id="btnSend">
                    <i class="bi bi-send-fill" style="margin-left:-2px;"></i>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('admin-scripts')
<script>
    const roomId = {{ $room->id }};
    const syncUrl = `{{ route('admin.chat.sync', $room->id) }}`;
    const typingUrl = `{{ route('admin.chat.typing', $room->id) }}`;
    const sendUrl = `{{ route('admin.chat.send', $room->id) }}`;
    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : document.querySelector('input[name="_token"]').value;

    let syncInterval;
    let typingTimeout;

    function scrollToBottom() {
        const box = document.getElementById('chatMessagesBox');
        box.scrollTop = box.scrollHeight;
    }

    function initPopovers() {
        // Initialize Bootstrap Popovers for read receipts
        $('[data-bs-toggle="popover"]').popover({
            html: true,
            sanitize: false,
            placement: 'left',
            container: 'body',
            content: function() {
                return `<div class="text-center py-2"><div class="spinner-border spinner-border-sm text-muted" role="status"></div></div>`;
            }
        }).on('inserted.bs.popover', function() {
            var $trigger = $(this);
            var id = $trigger.data('msg-id');
            var popoverId = $trigger.attr('aria-describedby');
            var $popoverBody = $('#' + popoverId).find('.popover-body');
            
            // Fetch seen by info
            $.ajax({
                url: `/admin/chat/message/${id}/info`,
                method: 'GET',
                success: function(res) {
                    let html = '<div class="small">';
                    if (res.seen_by.length === 0) {
                        html += '<span class="text-muted">Not seen yet</span>';
                    } else {
                        html += '<strong>Seen by:</strong><ul class="list-unstyled mb-0 mt-1">';
                        res.seen_by.forEach(p => {
                            html += `<li><i class="bi bi-check2-all text-info me-1"></i> ${p.name} <span class="text-muted" style="font-size:10px;">${p.time}</span></li>`;
                        });
                        html += '</ul>';
                    }
                    html += '</div>';
                    $popoverBody.html(html);
                },
                error: function() {
                    $popoverBody.html('<div class="small text-danger">Failed to load</div>');
                }
            });
        });
    }

    function syncChat() {
        const messageRows = document.querySelectorAll('.chat-bubble-row');
        let currentMaxId = 0;
        if (messageRows.length > 0) {
            currentMaxId = parseInt(messageRows[messageRows.length - 1].getAttribute('data-id'));
        }

        $.ajax({
            url: syncUrl,
            method: 'GET',
            data: {
                last_fetched_id: currentMaxId,
                current_max_id: currentMaxId
            },
            success: function(response) {
                // 1. Append New Messages
                if (response.html.trim() !== '') {
                    $('#noMessagesMsg').remove();
                    
                    // Parse the HTML and only append rows that don't exist
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = response.html;
                    let appended = false;
                    
                    tempDiv.querySelectorAll('.chat-bubble-row').forEach(row => {
                        const id = row.getAttribute('data-id');
                        if (document.querySelectorAll('.chat-bubble-row[data-id="' + id + '"]').length === 0) {
                            document.getElementById('chatMessagesBox').appendChild(row);
                            appended = true;
                        }
                    });

                    if (appended) {
                        scrollToBottom();
                        initPopovers();
                    }
                }

                // 2. Update Typing Indicator
                if (response.typists && response.typists.length > 0) {
                    let text = response.typists.length === 1 
                        ? `${response.typists[0]} is typing...`
                        : `${response.typists.join(', ')} are typing...`;
                    $('#typingIndicator').text(text).slideDown(150);
                } else {
                    $('#typingIndicator').slideUp(150);
                }

                // 3. Update Online Status Header
                if (response.online_count > 0) {
                    $('#onlineCountText').text(`${response.online_count} online now`);
                } else {
                    $('#onlineCountText').text('Offline');
                }

                // 4. Update Read Receipts
                if (response.seen_messages && response.seen_messages.length > 0) {
                    response.seen_messages.forEach(id => {
                        let icon = $(`.read-receipt[data-msg-id="${id}"] i`);
                        icon.removeClass('bi-check2 text-light')
                            .addClass('bi-check2-all text-info') // double blue tick
                            .css('opacity', '1');
                    });
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        try { scrollToBottom(); } catch (e) { console.error('Error scrolling:', e); }
        try { initPopovers(); } catch (e) { console.error('Error initializing popovers:', e); }

        // Start polling
        syncInterval = setInterval(syncChat, 3000);

        // Typing event
        $('#chatInput').on('keyup', function() {
            clearTimeout(typingTimeout);
            typingTimeout = setTimeout(() => {
                $.ajax({
                    url: typingUrl,
                    method: 'POST',
                    data: { _token: csrfToken }
                });
            }, 500); // Debounce typing ping to 500ms
        });

        let isSending = false;
        // AJAX Message Send
        $('#chatForm').on('submit', function(e) {
            e.preventDefault();
            if (isSending) return;

            const btn = $('#btnSend');
            const input = $('#chatInput');
            const message = input.val().trim();
            if (!message) return;

            isSending = true;
            btn.prop('disabled', true);
            input.val(''); // Clear immediately to prevent visual double-submit
            
            $.ajax({
                url: sendUrl,
                method: 'POST',
                data: {
                    _token: csrfToken,
                    message: message
                },
                success: function(res) {
                    if (res.success) {
                        $('#noMessagesMsg').remove();
                        // Only append if it doesn't already exist (prevent race condition with sync)
                        if ($('.chat-bubble-row[data-id="' + res.id + '"]').length === 0) {
                            $('#chatMessagesBox').append(res.html);
                            scrollToBottom();
                            initPopovers();
                        }
                        
                        // Force a sync immediately after sending to update the watermark
                        syncChat();
                    }
                },
                complete: function() {
                    isSending = false;
                    btn.prop('disabled', false);
                    input.focus();
                }
            });
        });

        // Hide popovers when clicking outside
        $('body').on('click', function (e) {
            $('[data-bs-toggle="popover"]').each(function () {
                if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                    $(this).popover('hide');
                }
            });
        });
    });
</script>
@endpush
