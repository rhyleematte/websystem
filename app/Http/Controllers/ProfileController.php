<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\PostMedia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    // ── View any user's profile ───────────────────────────────────
    public function show($id)
    {
        $profileUser = User::findOrFail($id);
        $posts = Post::where('user_id', $id)
            ->with(['user', 'likes', 'comments.user', 'comments.replies.user', 'media', 'resource'])
            ->latest()
            ->get();

        $application = null;
        $requirements = null;
        if (Auth::check() && Auth::id() === $profileUser->id) {
            $application = \App\Models\DoctorApplication::where('user_id', Auth::id())->first();
            $requirements = \App\Models\DoctorRequirement::all();
        }

        return view('profile.show', compact('profileUser', 'posts', 'application', 'requirements'));
    }

    // ── Update bio / name / username ─────────────────────────────
    public function updateInfo(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'fname' => ['required', 'min:2'],
            'mname' => ['nullable', 'min:2'],
            'lname' => ['required', 'min:2'],
            'username' => ['required', 'min:3', 'max:20', Rule::unique('users')->ignore($user->id)],
            'bio' => ['nullable', 'max:300'],
        ], [
            'fname.required' => 'First name is required.',
            'fname.min' => 'First name must be at least 2 characters.',
            'lname.required' => 'Last name is required.',
            'lname.min' => 'Last name must be at least 2 characters.',
            'username.required' => 'Username is required.',
            'username.min' => 'Username must be at least 3 characters.',
            'username.max' => 'Username cannot exceed 20 characters.',
            'username.unique' => 'Username already taken.',
            'bio.max' => 'Bio cannot exceed 300 characters.',
        ]);

        // Normalize name parts to Title Case before saving,
        // e.g. "EDWIN" or "edwin" → "Edwin"
        $titleCase = function (string $str): string {
            return mb_convert_case(mb_strtolower(trim($str)), MB_CASE_TITLE, 'UTF-8');
        };

        $user->update([
            'fname' => $titleCase($data['fname']),
            'mname' => (isset($data['mname']) && trim($data['mname']) !== '')
            ? $titleCase($data['mname'])
            : null,
            'lname' => $titleCase($data['lname']),
            'username' => $data['username'],
            'bio' => $data['bio'] ?? null,
        ]);

        // Re-read from DB to get the final normalized values
        $user->refresh();

        return response()->json([
            'ok' => true,
            'message' => 'Profile updated successfully!',
            'full_name' => $user->full_name,
            'username' => $user->username,
            'bio' => $user->bio,
        ]);
    }

    // ── Upload / change profile photo ────────────────────────────
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:4096'],
        ]);

        $user = Auth::user();

        if ($user->profile_photo && $user->profile_photo !== 'profiles/default.png') {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $path = $request->file('photo')->store('profiles', 'public');
        $user->update(['profile_photo' => $path]);

        return response()->json([
            'ok' => true,
            'avatar_url' => $user->avatar_url,
            'message' => 'Profile photo updated!',
        ]);
    }

    // ── Delete profile photo ─────────────────────────────────────
    public function deletePhoto()
    {
        $user = Auth::user();

        if ($user->profile_photo && $user->profile_photo !== 'profiles/default.png') {
            try {
                if (Storage::disk('public')->exists($user->profile_photo)) {
                    Storage::disk('public')->delete($user->profile_photo);
                }
            }
            catch (\Exception $e) {
            // Fallback gracefully if disk error
            }
        }

        $user->update(['profile_photo' => 'profiles/default.png']);

        return response()->json([
            'ok' => true,
            'avatar_url' => asset('assets/img/default.png'),
            'message' => 'Profile photo removed successfully.',
        ]);
    }

    // ── Dashboard feed (all users, latest) ───────────────────────
    public function dashboardFeed(Request $request)
    {
        $posts = Post::with(['user', 'likes', 'comments.user', 'comments.replies.user', 'media', 'resource'])
            ->latest()
            ->paginate(15);

        $formatted = $posts->getCollection()->map(function ($p) {
            return $this->formatPost($p);
        });

        return response()->json([
            'ok' => true,
            'posts' => $formatted,
            'has_more' => $posts->hasMorePages(),
        ]);
    }

    // ── Search users ─────────────────────────────────────────────
    public function searchUsers(Request $request)
    {
        $query = $request->input('q');
        if (!$query) {
            return response()->json(['ok' => true, 'users' => []]);
        }

        // Split query into words to match partial names
        $terms = explode(' ', trim($query));
        $usersQuery = User::query();

        foreach ($terms as $term) {
            $usersQuery->where(function ($q) use ($term) {
                $q->where('fname', 'like', "%{$term}%")
                    ->orWhere('lname', 'like', "%{$term}%")
                    ->orWhere('mname', 'like', "%{$term}%")
                    ->orWhere('username', 'like', "%{$term}%");
            });
        }

        $users = $usersQuery->take(8)->get()->map(function ($u) {
            return [
            'id' => $u->id,
            'name' => $u->short_name ?: $u->full_name,
            'username' => $u->username,
            'avatar_url' => $u->avatar_url,
            'profile_url' => route('profile.show', $u->id),
            ];
        });

        return response()->json([
            'ok' => true,
            'users' => $users
        ]);
    }

    // ── Create post ───────────────────────────────────────────────
    public function storePost(Request $request)
    {
        $request->validate([
            'text_content' => ['required_without:media', 'nullable', 'max:5000'],
            'media.*' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp,gif,mp4,mov', 'max:20480'],
            'mood' => ['nullable', 'string', 'max:64'],
            'hashtags' => ['nullable', 'string', 'max:500'],
            'group_id' => ['nullable', 'exists:groups,id'],
        ]);

        $user = Auth::user();
        $hasText = filled($request->text_content);
        $hasMedia = $request->hasFile('media');

        $postType = ($hasText && $hasMedia) ? 'mixed' : ($hasMedia ? 'media' : 'text');

        // Normalise hashtags: strip leading #, trim spaces, rejoin with comma
        $rawTags = $request->input('hashtags', '');
        $tags = array_values(array_filter(
            array_map(function ($t) {
            return ltrim(trim($t), '#');
        },
            preg_split('/[\s,]+/', $rawTags))
        ));
        $hashtagsStr = count($tags) ? implode(',', $tags) : null;

        $post = Post::create([
            'user_id' => $user->id,
            'group_id' => $request->input('group_id') ?: null,
            'post_type' => $postType,
            'text_content' => $request->text_content,
            'mood' => $request->input('mood') ?: null,
            'hashtags' => $hashtagsStr,
        ]);

        if ($hasMedia) {
            $order = 0;
            foreach ($request->file('media') as $file) {
                $mimeType = $file->getMimeType();
                $mediaType = (strpos($mimeType, 'video') === 0) ? 'video' : 'image';
                $filePath = $file->store('post_media', 'public');

                PostMedia::create([
                    'post_id' => $post->id,
                    'media_type' => $mediaType,
                    'path' => $filePath,
                    'mime_type' => $mimeType,
                    'size_bytes' => $file->getSize(),
                    'sort_order' => $order++,
                ]);
            }
        }

        $post->load(['user', 'likes', 'comments.user', 'media']);

        return response()->json(['ok' => true, 'post' => $this->formatPost($post)]);
    }

    // ── Edit post ─────────────────────────────────────────────────
    public function updatePost(Request $request, Post $post)
    {
        abort_if($post->user_id !== Auth::id(), 403);

        $request->validate([
            'text_content' => ['nullable', 'max:5000'],
            'media.*' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp,gif,mp4,mov', 'max:20480'],
            'deleted_media' => ['nullable', 'array'],
            'deleted_media.*' => ['integer', 'exists:post_media,id'],
        ]);

        // Delete specified media
        if ($request->has('deleted_media') && is_array($request->deleted_media)) {
            $mediaToDelete = PostMedia::whereIn('id', $request->deleted_media)
                ->where('post_id', $post->id)
                ->get();
            foreach ($mediaToDelete as $m) {
                try {
                    Storage::disk('public')->delete($m->path);
                }
                catch (\Exception $e) {
                // Ignore missing files
                }
                $m->delete();
            }
        }

        // Add new media
        if ($request->hasFile('media')) {
            $order = $post->media()->max('sort_order') + 1;
            foreach ($request->file('media') as $file) {
                $mimeType = $file->getMimeType();
                $mediaType = (strpos($mimeType, 'video') === 0) ? 'video' : 'image';
                $filePath = $file->store('post_media', 'public');

                PostMedia::create([
                    'post_id' => $post->id,
                    'media_type' => $mediaType,
                    'path' => $filePath,
                    'mime_type' => $mimeType,
                    'size_bytes' => $file->getSize(),
                    'sort_order' => $order++,
                ]);
            }
        }

        $post->refresh();
        $hasText = filled($request->text_content);
        $hasMedia = $post->media()->exists();

        // Validation for empty post
        if (!$hasText && !$hasMedia) {
            return response()->json(['ok' => false, 'message' => 'Post cannot be empty.'], 400);
        }

        $postType = ($hasText && $hasMedia) ? 'mixed' : ($hasMedia ? 'media' : 'text');

        $post->update([
            'text_content' => $request->text_content,
            'post_type' => $postType,
        ]);

        $post->load(['user', 'likes', 'comments.user', 'media']);

        return response()->json([
            'ok' => true,
            'message' => 'Post updated.',
            'post' => $this->formatPost($post)
        ]);
    }

    // ── Delete post ───────────────────────────────────────────────
    public function destroyPost(Post $post)
    {
        abort_if($post->user_id !== Auth::id(), 403);

        foreach ($post->media as $media) {
            Storage::disk('public')->delete($media->path);
        }

        $post->delete();

        return response()->json(['ok' => true, 'message' => 'Post deleted.']);
    }

    // ── Toggle like ───────────────────────────────────────────────
    public function toggleLike(Request $request, Post $post)
    {
        $userId = Auth::id();
        $existing = PostLike::where('post_id', $post->id)->where('user_id', $userId)->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        }
        else {
            PostLike::create([
                'post_id' => $post->id,
                'user_id' => $userId,
                'reaction_type' => $request->input('reaction_type', 'heart'),
            ]);
            $liked = true;
        }

        return response()->json([
            'ok' => true,
            'liked' => $liked,
            'like_count' => $post->likes()->count(),
        ]);
    }

    // ── Add comment ───────────────────────────────────────────────
    public function storeComment(Request $request, Post $post)
    {
        $request->validate([
            'comment_text' => ['required', 'max:1000'],
            'parent_comment_id' => ['nullable', 'exists:post_comments,id'],
        ]);

        $comment = PostComment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'parent_comment_id' => $request->parent_comment_id,
            'comment_text' => $request->comment_text,
        ]);

        $comment->load('user');

        return response()->json([
            'ok' => true,
            'comment' => $this->formatComment($comment),
        ]);
    }

    // ── Delete comment ────────────────────────────────────────────
    public function destroyComment(PostComment $comment)
    {
        abort_if($comment->user_id !== Auth::id(), 403);
        $comment->delete();

        return response()->json(['ok' => true, 'message' => 'Comment deleted.']);
    }

    public function updateCoverPhoto(Request $request)
    {
        $request->validate([
            'cover_photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB limit
        ]);

        $user = Auth::user();

        if ($request->hasFile('cover_photo')) {
            $file = $request->file('cover_photo');
            $filename = time() . '_' . uniqid() . '.' . $file->extension();
            $path = $file->storeAs('covers', $filename, 'public');

            // Delete old cover if exists
            if ($user->cover_photo) {
                try {
                    if (Storage::disk('public')->exists($user->cover_photo)) {
                        Storage::disk('public')->delete($user->cover_photo);
                    }
                }
                catch (\Exception $e) {
                // Ignore
                }
            }

            $user->update(['cover_photo' => $path]);

            return response()->json([
                'ok' => true,
                'cover_url' => $user->cover_url,
                'message' => 'Cover photo updated successfully.',
            ]);
        }

        return response()->json(['ok' => false, 'message' => 'No image uploaded.'], 400);
    }

    public function deleteCoverPhoto()
    {
        $user = Auth::user();

        if ($user->cover_photo) {
            try {
                if (Storage::disk('public')->exists($user->cover_photo)) {
                    Storage::disk('public')->delete($user->cover_photo);
                }
            }
            catch (\Exception $e) {
            // Fallback gracefully
            }
        }

        $user->update(['cover_photo' => null]);

        return response()->json([
            'ok' => true,
            'cover_url' => $user->cover_url,
            'message' => 'Cover photo removed successfully.',
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────
    private function formatPost(Post $post): array
    {
        $mediaData = [];
        foreach ($post->media as $m) {
            $mediaData[] = [
                'id' => $m->id,
                'media_type' => $m->media_type,
                'url' => asset('storage/' . $m->path),
            ];
        }

        $commentsData = [];
        foreach ($post->comments as $c) {
            $commentsData[] = $this->formatComment($c);
        }

        return [
            'id' => $post->id,
            'text_content' => $post->text_content,
            'post_type' => $post->post_type,
            'mood' => $post->mood,
            'hashtags' => $post->hashtags_array, // array like ['anxiety','hope']
            'created_at' => $post->created_at->diffForHumans(),
            'like_count' => $post->likes->count(),
            'comment_count' => $post->allComments()->count(),
            'is_liked' => $post->isLikedBy(Auth::id()),
            'can_manage' => $post->user_id === Auth::id(),
            'user' => [
                'id' => $post->user->id,
                'name' => $post->user->full_name,
                'username' => $post->user->username,
                'avatar_url' => $post->user->avatar_url,
                'profile_url' => route('profile.show', $post->user->id),
            ],
            'media' => $mediaData,
            'comments' => $commentsData,
            'resource' => $post->resource ? [
                'id' => $post->resource->id,
                'title' => $post->resource->title,
                'type' => $post->resource->type,
                'description' => $post->resource->description,
                'thumbnail_url' => $post->resource->thumbnail_url,
                'url' => route('resources.show', $post->resource->id),
            ] : null,
        ];
    }

    private function formatComment(PostComment $comment): array
    {
        $repliesData = [];
        if ($comment->replies) {
            foreach ($comment->replies as $r) {
                $repliesData[] = $this->formatComment($r);
            }
        }

        return [
            'id' => $comment->id,
            'post_id' => $comment->post_id,
            'comment_text' => $comment->comment_text,
            'created_at' => $comment->created_at->diffForHumans(),
            'can_delete' => $comment->user_id === Auth::id(),
            'user' => [
                'id' => $comment->user->id,
                'name' => $comment->user->full_name,
                'username' => $comment->user->username,
                'avatar_url' => $comment->user->avatar_url,
                'profile_url' => route('profile.show', $comment->user->id),
            ],
            'replies' => $repliesData,
        ];
    }
}
