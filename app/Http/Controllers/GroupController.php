<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public function index()
    {
        $sort = request()->query('sort', 'newest');

        $groupsQuery = Group::withCount([
            'members',
            'members as recent_members_count' => function ($query) {
            $query->where('group_members.created_at', '>=', now()->subDays(30));
        },
            'posts as recent_posts_count' => function ($query) {
            $query->where('posts.created_at', '>=', now()->subDays(30));
        },
            'allComments as recent_comments_count' => function ($query) {
            $query->where('post_comments.created_at', '>=', now()->subDays(30));
        },
            'allLikes as recent_likes_count' => function ($query) {
            $query->where('post_likes.created_at', '>=', now()->subDays(30));
        }
        ]);

        if ($sort === 'members_desc') {
            $groupsQuery->orderBy('members_count', 'desc');
        } elseif ($sort === 'members_asc') {
            $groupsQuery->orderBy('members_count', 'asc');
        } elseif ($sort === 'active_desc') {
            $groupsQuery->orderByRaw('(recent_posts_count * 5 + recent_members_count * 3 + recent_comments_count * 2 + recent_likes_count) desc');
        } elseif ($sort === 'active_asc') {
            $groupsQuery->orderByRaw('(recent_posts_count * 5 + recent_members_count * 3 + recent_comments_count * 2 + recent_likes_count) asc');
        } elseif ($sort === 'oldest') {
            $groupsQuery->orderBy('created_at', 'asc');
        } else {
            $groupsQuery->orderBy('created_at', 'desc');
        }

        $groups = $groupsQuery->get();
        // Get active user's memberships to show "Joined" status
        $user = Auth::user();
        $myGroupIds = $user ? $user->groups()->pluck('group_id')->toArray() : [];

        return view('groups.index', compact('groups', 'myGroupIds', 'sort'));
    }

    public function show($id)
    {
        $group = Group::with(['creator', 'members.user'])
            ->withCount([
            'members',
            'members as recent_members_count' => function ($query) {
            $query->where('group_members.created_at', '>=', now()->subDays(30));
        },
            'posts as recent_posts_count' => function ($query) {
            $query->where('posts.created_at', '>=', now()->subDays(30));
        },
            'allComments as recent_comments_count' => function ($query) {
            $query->where('post_comments.created_at', '>=', now()->subDays(30));
        },
            'allLikes as recent_likes_count' => function ($query) {
            $query->where('post_likes.created_at', '>=', now()->subDays(30));
        }
        ])->findOrFail($id);
        $user = Auth::user();
        $isMember = $user ? $group->members()->where('user_id', $user->id)->exists() : false;

        // Fetch posts exactly like the user feed, but scoped to this group
        $posts = $group->posts()
            ->with(['user.doctorApplication', 'media', 'likes', 'comments.user'])
            ->latest()
            ->get();

        $me = $user;

        return view('groups.show', compact('group', 'isMember', 'posts', 'me'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->doctor_status !== 'approved') {
            return response()->json(['ok' => false, 'message' => 'Only approved doctors can create groups.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'guidelines' => 'nullable|string',
            'cover_photo' => 'nullable|image|max:10240',
        ]);

        $data = $request->only(['name', 'description', 'guidelines']);
        $data['creator_id'] = $user->id;

        if ($request->hasFile('cover_photo')) {
            $file = $request->file('cover_photo');
            $filename = time() . '_' . uniqid() . '.' . $file->extension();
            $path = $file->storeAs('group_covers', $filename, 'public');
            $data['cover_photo'] = $path;
        }
        else {
            // Null cover_photo will intentionally trigger the CSS fallback gradient on the frontend.
            $data['cover_photo'] = null;
        }

        $group = Group::create($data);

        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => $user->id,
            'role' => 'admin',
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Group created successfully.',
            'redirect' => route('groups.show', $group->id)
        ]);
    }

    public function join($id)
    {
        $group = Group::findOrFail($id);
        $user = Auth::user();

        GroupMember::firstOrCreate([
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);

        return response()->json(['ok' => true, 'message' => 'Joined group!']);
    }

    public function leave($id)
    {
        $group = Group::findOrFail($id);
        $user = Auth::user();

        GroupMember::where('group_id', $group->id)->where('user_id', $user->id)->delete();

        return response()->json(['ok' => true, 'message' => 'Left group.']);
    }

    public function updateCoverPhoto(Request $request, $id)
    {
        $user = Auth::user();
        $group = Group::findOrFail($id);

        if ($group->creator_id !== $user->id) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized. Only the group creator can update the cover photo.'], 403);
        }

        $request->validate([
            'cover_photo' => 'required|image|max:10240',
        ]);

        if ($request->hasFile('cover_photo')) {
            // Delete old cover photo if it exists
            if ($group->cover_photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($group->cover_photo);
            }

            $file = $request->file('cover_photo');
            $filename = time() . '_' . uniqid() . '.' . $file->extension();
            $path = $file->storeAs('group_covers', $filename, 'public');

            $group->update(['cover_photo' => $path]);

            return response()->json([
                'ok' => true,
                'message' => 'Cover photo updated successfully.',
                'cover_photo_url' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['ok' => false, 'message' => 'No image uploaded.'], 400);
    }

    public function deleteCoverPhoto($id)
    {
        $user = Auth::user();
        $group = Group::findOrFail($id);

        if ($group->creator_id !== $user->id) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized. Only the group creator can delete the cover photo.'], 403);
        }

        if ($group->cover_photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($group->cover_photo);
            $group->update(['cover_photo' => null]);
            return response()->json(['ok' => true, 'message' => 'Cover photo removed.']);
        }

        return response()->json(['ok' => false, 'message' => 'No cover photo to remove.'], 400);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $group = Group::findOrFail($id);

        if ($group->creator_id !== $user->id) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized. Only the group creator can edit this group.'], 403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'guidelines' => ['nullable', 'string'],
        ]);

        $group->update($data);

        return response()->json([
            'ok' => true,
            'message' => 'Group updated.',
            'group' => [
                'name' => $group->name,
                'description' => $group->description,
                'guidelines' => $group->guidelines,
            ],
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $group = Group::findOrFail($id);

        if ($group->creator_id !== $user->id) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized. Only the group creator can delete this group.'], 403);
        }

        if ($group->cover_photo) {
            Storage::disk('public')->delete($group->cover_photo);
        }

        $group->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Group deleted.',
            'redirect' => route('groups.index'),
        ]);
    }
}
