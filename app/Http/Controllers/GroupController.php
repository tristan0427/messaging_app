<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        // Get all users
        $allUsers = User::select('id', 'name', 'avatar', 'is_online', 'last_seen_at')
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $user->avatar,
                    'is_online' => $user->status ?? 'offline',
                    'last_seen_at' => $user->last_seen,
                    'role' => 'member'
                ];
            });

        // Get latest message
        $lastMessage = Group::with('user')->latest()->first();

        $groupData = [
            'id' => 1, // Fixed ID for the single group
            'name' => 'Everyone',
            'description' => 'Group chat for all users',
            'avatar' => null,
            'member_count' => User::count(),
            'members' => $allUsers,
            'last_message' => $lastMessage ?
                $lastMessage->user->name . ': ' . $lastMessage->message :
                'No messages yet',
            'last_message_time' => $lastMessage ? $lastMessage->created_at->toISOString() : null,
            'unread_count' => 0, // Simple version - no unread tracking
            'href' => '/group-chat',
            'type' => 'global'
        ];

        return response()->json(['data' => [$groupData]]);
    }

    // Get all messages
    public function getMessages()
    {
        $messages = Group::with('user')
            ->orderBy('created_at', 'asc')
            ->limit(100)
            ->get()
            ->map(function($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'user_id' => $message->user_id,
                    'user_name' => $message->user->name,
                    'user_avatar' => $message->user->avatar,
                    'created_at' => $message->created_at->toISOString()
                ];
            });

        return response()->json(['messages' => $messages]);
    }

    // Send a message
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $message = Group::create([
            'user_id' => auth()->id(),
            'message' => $request->message
        ]);

        $message->load('user');

        return response()->json([
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'user_id' => $message->user_id,
                'user_name' => $message->user->name,
                'user_avatar' => $message->user->avatar,
                'created_at' => $message->created_at->toISOString()
            ]
        ]);
    }
}
