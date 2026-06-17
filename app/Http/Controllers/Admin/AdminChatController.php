<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\ChatUserState;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class AdminChatController extends Controller
{
    public function index()
    {
        $rooms = ChatRoom::where('institute_id', auth()->user()->institute_id)->get();

        $rooms = $rooms->filter(function ($room) {
            return auth()->user()->canAccessChatRoom($room);
        });

        return view('admin.chat.index', compact('rooms'));
    }

    public function show(ChatRoom $room)
    {
        abort_unless(auth()->user()->canAccessChatRoom($room), 403);

        $messages = $room->messages()
            ->with('sender')
            ->latest()
            ->take(50)
            ->get()
            ->reverse();

        // Update read state on initial load
        if ($messages->isNotEmpty()) {
            ChatUserState::updateOrCreate(
                ['user_id' => auth()->id(), 'chat_room_id' => $room->id],
                ['last_read_message_id' => $messages->last()->id]
            );
        }

        return view('admin.chat.show', compact('room', 'messages'));
    }

    public function send(Request $request, ChatRoom $room)
    {
        abort_unless(auth()->user()->canAccessChatRoom($room), 403);

        $request->validate(['message' => 'required']);

        $message = ChatMessage::create([
            'chat_room_id' => $room->id,
            'sender_id' => auth()->id(),
            'message' => $request->message,
        ]);

        // Automatically mark as read for sender
        ChatUserState::updateOrCreate(
            ['user_id' => auth()->id(), 'chat_room_id' => $room->id],
            ['last_read_message_id' => $message->id]
        );

        if ($request->ajax()) {
            $html = view('admin.chat.partials.message', ['msg' => $message, 'room' => $room])->render();
            return response()->json(['success' => true, 'html' => $html, 'id' => $message->id]);
        }

        return back();
    }

    public function sync(Request $request, ChatRoom $room)
    {
        abort_unless(auth()->user()->canAccessChatRoom($room), 403);

        // 1. Update last read watermark
        if ($request->has('current_max_id') && $request->current_max_id > 0) {
            ChatUserState::updateOrCreate(
                ['user_id' => auth()->id(), 'chat_room_id' => $room->id],
                ['last_read_message_id' => $request->current_max_id]
            );
        }

        // 2. Fetch new messages
        $lastFetchedId = $request->last_fetched_id ?? 0;
        $messages = $room->messages()
            ->with('sender')
            ->where('id', '>', $lastFetchedId)
            ->orderBy('id', 'asc')
            ->get();

        $html = '';
        foreach ($messages as $msg) {
            $html .= view('admin.chat.partials.message', ['msg' => $msg, 'room' => $room])->render();
        }

        // 3. Typists
        $typists = Cache::get("room_{$room->id}_typists", []);
        $activeTypists = [];
        foreach ($typists as $id => $data) {
            if ($data['expires'] > now()->timestamp && $id != auth()->id()) {
                $activeTypists[] = $data['name'];
            }
        }

        // 4. Online Members Count
        // Find users who have accessed this room recently and are currently online
        $onlineCount = User::whereIn('id', $room->userStates()->select('user_id'))
            ->where('last_active_at', '>', now()->subMinutes(2))
            ->count();

        // 5. Read Receipts update (which of my sent messages have been seen?)
        // Fetch IDs of messages sent by me that have been read by at least one other person
        // To be extremely efficient, we just query if there's any state with last_read > my message id
        $myRecentMessageIds = $room->messages()
            ->where('sender_id', auth()->id())
            ->latest()->take(20)->pluck('id');
        
        $seenMessageIds = [];
        if ($myRecentMessageIds->isNotEmpty()) {
            $maxReadByOthers = ChatUserState::where('chat_room_id', $room->id)
                ->where('user_id', '!=', auth()->id())
                ->max('last_read_message_id') ?? 0;

            foreach ($myRecentMessageIds as $mid) {
                if ($maxReadByOthers >= $mid) {
                    $seenMessageIds[] = $mid;
                }
            }
        }

        return response()->json([
            'html' => $html,
            'typists' => $activeTypists,
            'last_id' => $messages->max('id') ?? $lastFetchedId,
            'online_count' => $onlineCount,
            'seen_messages' => $seenMessageIds
        ]);
    }

    public function typing(Request $request, ChatRoom $room)
    {
        abort_unless(auth()->user()->canAccessChatRoom($room), 403);

        $typists = Cache::get("room_{$room->id}_typists", []);
        $typists[auth()->id()] = [
            'name' => auth()->user()->name,
            'expires' => now()->addSeconds(3)->timestamp
        ];
        Cache::put("room_{$room->id}_typists", $typists, now()->addMinutes(1));

        return response()->json(['success' => true]);
    }

    public function sidebarSync()
    {
        $rooms = ChatRoom::where('institute_id', auth()->user()->institute_id)->get()
            ->filter(fn($r) => auth()->user()->canAccessChatRoom($r));
        
        $counts = [];
        foreach ($rooms as $room) {
            $counts[$room->id] = $room->unreadCountForUser(auth()->id());
        }

        return response()->json(['counts' => $counts]);
    }

    public function messageInfo(ChatMessage $message)
    {
        abort_unless(auth()->user()->canAccessChatRoom($message->room), 403);

        $states = ChatUserState::with('user')
            ->where('chat_room_id', $message->chat_room_id)
            ->where('last_read_message_id', '>=', $message->id)
            ->where('user_id', '!=', $message->sender_id)
            ->get();
        
        $seenBy = $states->map(function($state) {
            return [
                'name' => $state->user->name,
                'time' => $state->updated_at->format('M d, h:i A')
            ];
        });

        return response()->json(['seen_by' => $seenBy]);
    }
}
