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
        
        <div class="ms-auto">
            <button onclick="window.location.reload();" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh Chat
            </button>
        </div>
    </div>

    <div class="chat-messages" id="chatMessagesBox">
        @forelse($messages as $msg)
            @php
                $isMe = $msg->sender_id === auth()->id();
            @endphp
            <div class="chat-bubble-row {{ $isMe ? 'me' : '' }}">
                <div class="chat-bubble-wrapper">
                    @if(!$isMe)
                        <div class="chat-sender-name">{{ $msg->sender->name ?? 'Unknown' }}</div>
                    @endif
                    <div class="chat-bubble">
                        {{ $msg->message }}
                        <div class="chat-timestamp">
                            {{ $msg->created_at->format('h:i A') }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted my-auto">
                <i class="bi bi-chat-dots" style="font-size: 40px; opacity: 0.5;"></i>
                <p class="mt-2">No messages yet. Be the first to say hi!</p>
            </div>
        @endforelse
    </div>

    <div class="chat-input-area">
        <form action="{{ route('admin.chat.send', $room) }}" method="POST" class="m-0">
            @csrf
            <div class="chat-input-wrapper shadow-sm">
                <input type="text" name="message" placeholder="Type a message..." required autocomplete="off" autofocus>
                <button type="submit" class="btn-send shadow-sm">
                    <i class="bi bi-send-fill" style="margin-left:-2px;"></i>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('admin-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatBox = document.getElementById('chatMessagesBox');
        chatBox.scrollTop = chatBox.scrollHeight;
    });
</script>
@endpush
