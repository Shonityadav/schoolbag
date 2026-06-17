<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatRoom;
use App\Models\ChatMessage;

class AdminChatController extends Controller
{
    public function index()
    {
        $rooms = ChatRoom::where(
            'institute_id',
            auth()->user()->institute_id
        )->get();

        $rooms = $rooms->filter(function ($room) {

            return auth()->user()
                ->canAccessChatRoom($room);

        });

        return view(
            'admin.chat.index',
            compact('rooms')
        );
    }

    public function show(ChatRoom $room)
    {
        abort_unless(
            auth()->user()
                ->canAccessChatRoom($room),
            403
        );

        $messages = $room->messages()
            ->with('sender')
            ->latest()
            ->take(100)
            ->get()
            ->reverse();

        return view(
            'admin.chat.show',
            compact(
                'room',
                'messages'
            )
        );
    }

    public function send(
        Request $request,
        ChatRoom $room
    )
    {
        abort_unless(
            auth()->user()
                ->canAccessChatRoom($room),
            403
        );

        $request->validate([
            'message' => 'required'
        ]);

        ChatMessage::create([
            'chat_room_id' => $room->id,
            'sender_id' => auth()->id(),
            'message' => $request->message,
        ]);

        return back();
    }
}
