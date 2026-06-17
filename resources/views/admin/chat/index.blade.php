@extends('layouts.admin')

@section('title', 'Chat Groups')
@section('admin_page_title', 'Chat Groups')
@section('admin_nav_chat', 'active')

@section('admin_content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="m-0 fw-bold">My Chat Groups</h5>
</div>

@php
    $classRooms = $rooms->where('type', 'class');
    $categoryRooms = $rooms->where('type', 'staff_category');
@endphp

@if($rooms->isEmpty())
    <div class="col-12 text-center py-5">
        <div class="sb-stat-icon mx-auto mb-3" style="background:#F1F5F9; color:#94A3B8; width: 60px; height: 60px; font-size: 24px;">
            <i class="bi bi-chat-square"></i>
        </div>
        <h6 class="fw-bold text-dark">No chat groups found</h6>
        <p class="text-muted small">You don't have access to any chat groups right now.</p>
    </div>
@else

    {{-- Class Chat Groups --}}
    @if($classRooms->isNotEmpty())
        <h6 class="fw-bold text-muted mb-3 text-uppercase" style="font-size: 12px; letter-spacing: 0.5px;">Class Groups</h6>
        <div class="row g-4 mb-5">
            @foreach($classRooms as $room)
                <div class="col-md-4 col-sm-6">
                    <a href="{{ route('admin.chat.show', $room) }}" class="text-decoration-none">
                        <div class="sb-panel sb-stat-card border-0 shadow-sm h-100 p-4 d-flex flex-column align-items-center text-center">
                            <div class="sb-stat-icon mb-3 blue rounded-circle" style="width: 60px; height: 60px; font-size: 24px;">
                                <i class="bi bi-book"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">{{ $room->name }}</h6>
                            <span class="badge bg-light text-secondary border px-2 py-1">
                                Class: {{ $room->schoolClass->standard ?? '' }} {{ $room->schoolClass->section ?? '' }}
                            </span>
                            
                            <div class="mt-4 w-100 border-top pt-3 text-muted d-flex justify-content-between align-items-center" style="font-size: 12px; font-weight: 500;">
                                <span><i class="bi bi-chat-dots me-1"></i> Open Chat</span>
                                <span class="badge bg-danger rounded-pill" id="unread-badge-{{ $room->id }}" style="display: none;">0</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Staff Category Chat Groups --}}
    @if($categoryRooms->isNotEmpty())
        <h6 class="fw-bold text-muted mb-3 text-uppercase" style="font-size: 12px; letter-spacing: 0.5px;">Staff Category Groups</h6>
        <div class="row g-4 mb-4">
            @foreach($categoryRooms as $room)
                <div class="col-md-4 col-sm-6">
                    <a href="{{ route('admin.chat.show', $room) }}" class="text-decoration-none">
                        <div class="sb-panel sb-stat-card border-0 shadow-sm h-100 p-4 d-flex flex-column align-items-center text-center">
                            <div class="sb-stat-icon mb-3 purple rounded-circle" style="width: 60px; height: 60px; font-size: 24px;">
                                <i class="bi bi-briefcase"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">{{ $room->name }}</h6>
                            <span class="badge bg-light text-secondary border px-2 py-1">
                                Category: {{ $room->category->name ?? '' }}
                            </span>
                            
                            <div class="mt-4 w-100 border-top pt-3 text-muted d-flex justify-content-between align-items-center" style="font-size: 12px; font-weight: 500;">
                                <span><i class="bi bi-chat-dots me-1"></i> Open Chat</span>
                                <span class="badge bg-danger rounded-pill" id="unread-badge-{{ $room->id }}" style="display: none;">0</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

@endif

@endsection

@push('admin-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function syncSidebar() {
            $.ajax({
                url: `{{ route('admin.chat.sidebar_sync') }}`,
                method: 'GET',
                success: function(res) {
                    if (res.counts) {
                        for (const roomId in res.counts) {
                            const count = res.counts[roomId];
                            const badge = $(`#unread-badge-${roomId}`);
                            if (count > 0) {
                                badge.text(count).show();
                            } else {
                                badge.hide();
                            }
                        }
                    }
                }
            });
        }

        // Poll every 5 seconds on the index page
        setInterval(syncSidebar, 5000);
        syncSidebar(); // initial call
    });
</script>
@endpush
