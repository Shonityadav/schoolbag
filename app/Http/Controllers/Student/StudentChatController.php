<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\ChatUserState;

class StudentChatController extends Controller
{
    /**
     * Fetch all chat rooms available to the student
     */
    public function fetchRooms()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Fetch all rooms for the institute
        $allRooms = ChatRoom::where('institute_id', $user->institute_id)->get();

        // Filter rooms the student can access
        $accessibleRooms = $allRooms->filter(function ($room) use ($user) {
            return $user->canAccessChatRoom($room);
        });

        $roomsData = $accessibleRooms->map(function ($room) use ($user) {
            return [
                'id' => $room->id,
                'name' => $room->name,
                'type' => $room->type,
                'unread_count' => $room->unreadCountForUser($user->id)
            ];
        })->values();

        return response()->json(['rooms' => $roomsData]);
    }

    /**
     * Fetch messages for a specific room
     */
    public function fetchMessages(ChatRoom $room)
    {
        $user = auth()->user();
        if (!$user->canAccessChatRoom($room)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $messages = $room->messages()
            ->with('sender')
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values();

        // Update read state
        if ($messages->isNotEmpty()) {
            ChatUserState::updateOrCreate(
                ['user_id' => $user->id, 'chat_room_id' => $room->id],
                ['last_read_message_id' => $messages->last()->id]
            );
        }

        $messagesData = $messages->map(function ($msg) use ($user) {
            return [
                'id' => $msg->id,
                'message' => $msg->message,
                'created_at' => $msg->created_at->format('M d, H:i'),
                'is_mine' => $msg->sender_id === $user->id,
                'sender_name' => $msg->sender->name ?? 'Unknown',
            ];
        });

        return response()->json(['messages' => $messagesData]);
    }

    /**
     * Send a new message to a specific room
     */
    public function sendMessage(Request $request, ChatRoom $room)
    {
        $user = auth()->user();
        if (!$user->canAccessChatRoom($room)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $request->validate(['message' => 'required|string']);

        $message = ChatMessage::create([
            'chat_room_id' => $room->id,
            'sender_id' => $user->id,
            'message' => $request->message,
        ]);

        // Automatically mark as read for sender
        ChatUserState::updateOrCreate(
            ['user_id' => $user->id, 'chat_room_id' => $room->id],
            ['last_read_message_id' => $message->id]
        );

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'created_at' => $message->created_at->format('M d, H:i'),
                'is_mine' => true,
                'sender_name' => $user->name,
            ]
        ]);
    }
}
