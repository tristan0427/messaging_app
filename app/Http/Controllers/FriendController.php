<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FriendController extends Controller
{
    public function list(Request $request)
    {
        $currentUser = auth()->user();

        // Get all users except the current one
        $friends = User::where('id', '!=', $currentUser->id)
            ->when($request->search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->select('id', 'name', 'email', 'avatar', 'is_online', 'last_seen_at')
            ->orderBy('is_online', 'desc') // Online users first
            ->orderBy('name', 'asc')       // Then alphabetical
            ->get()
            ->map(function($friend) {
                return [
                    'id' => $friend->id,
                    'name' => $friend->name,
                    'email' => $friend->email,
                    'avatar' => $friend->avatar,
                    'status' => $this->getUserStatus($friend),
                    'last_seen' => $friend->last_seen_at ? $friend->last_seen_at->diffForHumans() : null,
                    'href' => "/chat/{$friend->id}"
                ];
            });

        return response()->json($friends);
    }

    private function getUserStatus($user)
    {
        if ($user->is_online) {
            return 'online';
        }

        // If they have a last_seen_at timestamp
        if ($user->last_seen_at) {
            $minutesAgo = $user->last_seen_at->diffInMinutes(now());

            if ($minutesAgo <= 5) {
                return 'away'; // Recently active
            }
        }

        return 'offline';
    }
}
