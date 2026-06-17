@php
    $isMe = $msg->sender_id === auth()->id();
@endphp
<div class="chat-bubble-row {{ $isMe ? 'me' : '' }}" data-id="{{ $msg->id }}">
    <div class="chat-bubble-wrapper">
        @if(!$isMe)
            <div class="chat-sender-name">{{ $msg->sender->name ?? 'Unknown' }}</div>
        @endif
        <div class="chat-bubble {{ $isMe ? 'my-bubble' : 'other-bubble' }}" 
             @if($isMe) data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus" data-msg-id="{{ $msg->id }}" tabindex="0" style="cursor: pointer;" @endif>
            
            {{ $msg->message }}
            
            <div class="chat-timestamp d-flex justify-content-end align-items-center gap-1">
                <span>{{ $msg->created_at->format('h:i A') }}</span>
                @if($isMe)
                    <span class="read-receipt ms-1" data-msg-id="{{ $msg->id }}">
                        {{-- Default is single grey tick (Sent). Will be upgraded by JS --}}
                        <i class="bi bi-check2 text-light" style="font-size: 14px; opacity: 0.8;"></i>
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
