<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function getOrCreateChat($friendId)
    {
        $userId = Auth::id();

        // Check if chat already exists
        $chat = Chat::whereHas('users', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->whereHas('users', function ($q) use ($friendId) {
            $q->where('user_id', $friendId);
        })->where('is_group', false)->first();

        // If not, create new chat
        if (!$chat) {
            $chat = Chat::create([
                'is_group' => false,
                'created_by' => $userId,
            ]);

            $chat->users()->attach([$userId, $friendId]);
        }

        return response()->json($chat->load('messages.user'));
    }

    // Store a new message
    public function sendMessage(Request $request, $chatId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'chat_id' => $chatId,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return response()->json($message->load('user'));
    }
}
