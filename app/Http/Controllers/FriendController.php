<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
                    'avatar' => $this->getAvatarUrl($friend->avatar),
                    'status' => $this->getUserStatus($friend),
                    'last_seen' => $friend->last_seen_at ? $friend->last_seen_at->diffForHumans() : null,
                    'href' => "/chat/{$friend->id}"
                ];
            });

        return response()->json($friends);
    }

    private function getAvatarUrl($avatar)
    {
        // If no avatar, return null
        if (!$avatar) {
            return null;
        }

        // If the avatar already contains the full web path
        if (str_starts_with($avatar, '/storage/')) {

            $filename = basename($avatar);

            // Check if the actual file exists
            if (Storage::disk('public')->exists('Profile_Picture/' . $filename)) {

                return asset($avatar);
            }
        } else {
            // Handle case where avatar is just a filename
            $filename = basename($avatar);

            if (Storage::disk('public')->exists('Profile_Picture/' . $filename)) {
                return asset('storage/Profile_Picture/' . $filename);
            }
        }

        // Log missing file for debugging
        \Log::warning('Avatar file not found', [
            'original_avatar' => $avatar,
            'filename' => basename($avatar),
            'storage_path_exists' => Storage::disk('public')->exists('Profile_Picture/' . basename($avatar)),
            'full_file_path' => storage_path('app/public/Profile_Picture/' . basename($avatar)),
            'file_exists_on_disk' => file_exists(storage_path('app/public/Profile_Picture/' . basename($avatar)))
        ]);

        // File doesn't exist, return null so frontend can show fallback
        return null;
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
