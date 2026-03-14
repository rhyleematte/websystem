<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    public function index()
    {
        $resources = Resource::with('user')->latest()->get();

        $joinedResourceIds = [];
        if (Auth::check()) {
            $joinedResourceIds = Auth::user()
                ->joinedResources()
                ->pluck('resources.id')
                ->toArray();
        }

        return view('resources.index', [
            'resources' => $resources,
            'joinedResourceIds' => $joinedResourceIds,
        ]);
    }

    public function show(Resource $resource)
    {
        $isJoined = false;
        if (Auth::check()) {
            $isJoined = Auth::user()
                ->joinedResources()
                ->where('resources.id', $resource->id)
                ->exists();
        }

        // #region agent log: resource show navigation context
        try {
            $user = Auth::user();
            $payload = [
                'sessionId' => 'b31335',
                'runId' => 'resource-nav',
                'hypothesisId' => 'H-back-profile',
                'location' => 'app/Http/Controllers/ResourceController.php:show',
                'message' => 'resource_show_context',
                'data' => [
                    'resource_id' => $resource->id,
                    'auth_user_id' => $user ? $user->id : null,
                    'is_joined' => $isJoined,
                ],
                'timestamp' => (int) round(microtime(true) * 1000),
            ];
            file_put_contents(base_path('debug-b31335.log'), json_encode($payload) . PHP_EOL, FILE_APPEND);
        } catch (\Throwable $e) {
            // ignore logging failures
        }
        // #endregion agent log: resource show navigation context

        // For Article vs others, logic might differ but we'll use same show page for now
        return view('resources.show', compact('resource', 'isJoined'));
    }

    public function create()
    {
        $this->authorize('create', Resource::class);
        return view('resources.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Resource::class);

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:Article,Audio,Workbook,Media,Video',
            'description' => 'required|string',
            'content' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,mp3,wav,ogg,mp4,webm,mov|max:51200', // 50MB max
            'thumbnail' => 'nullable|image|max:5120',
            'duration_meta' => 'nullable|string|max:50',
            'hashtags' => 'nullable|string',
        ]);

        $data = $request->except(['thumbnail', 'file']);
        $data['user_id'] = Auth::id();

        // Truncate title to 50 chars as requested
        $data['title'] = mb_substr($request->title, 0, 50);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('resources/thumbnails', 'public');
        }

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('resources/files', 'public');
            $data['file_type'] = $request->file('file')->getClientOriginalExtension();
        }

        $resource = Resource::create($data);

        return redirect()->route('resources.show', $resource->id)->with('success', 'Resource created successfully.');
    }

    public function edit(Resource $resource)
    {
        $this->authorize('update', $resource);
        return view('resources.edit', compact('resource'));
    }

    public function update(Request $request, Resource $resource)
    {
        $this->authorize('update', $resource);

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:Article,Audio,Workbook,Media,Video',
            'description' => 'required|string',
            'content' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,mp3,wav,ogg,mp4,webm,mov|max:51200', // 50MB max
            'thumbnail' => 'nullable|image|max:5120',
            'duration_meta' => 'nullable|string|max:50',
            'hashtags' => 'nullable|string',
        ]);

        $data = $request->except(['thumbnail', 'file']);

        // Truncate title to 50 chars
        $data['title'] = mb_substr($request->title, 0, 50);

        if ($request->hasFile('thumbnail')) {
            if ($resource->thumbnail) {
                Storage::disk('public')->delete($resource->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('resources/thumbnails', 'public');
        }

        if ($request->hasFile('file')) {
            if ($resource->file_path) {
                Storage::disk('public')->delete($resource->file_path);
            }
            $data['file_path'] = $request->file('file')->store('resources/files', 'public');
            $data['file_type'] = $request->file('file')->getClientOriginalExtension();
        }

        $resource->update($data);

        return redirect()->route('resources.show', $resource->id)->with('success', 'Resource updated successfully.');
    }

    public function share(Request $request, Resource $resource)
    {
        $request->validate([
            'text_content' => 'nullable|string|max:5000',
            'hashtags' => 'nullable|string|max:500',
        ]);

        // Sharing a resource creates a post
        $post = Post::create([
            'user_id' => Auth::id(),
            'resource_id' => $resource->id,
            'post_type' => 'resource_share',
            'text_content' => $request->text_content ?: ("Shared a resource: " . $resource->title),
            'hashtags' => $request->hashtags ?: $resource->hashtags,
        ]);

        return response()->json(['ok' => true, 'message' => 'Shared to feed!', 'post_id' => $post->id]);
    }

    public function join(Resource $resource)
    {
        $user = Auth::user();
        $user->joinedResources()->syncWithoutDetaching([
            $resource->id => ['status' => 'joined'],
        ]);

        return back()->with('success', 'Resource joined.');
    }

    public function unjoin(Resource $resource)
    {
        $user = Auth::user();
        $user->joinedResources()->detach($resource->id);

        return back()->with('success', 'Resource unjoined.');
    }

    public function destroy(Resource $resource)
    {
        $this->authorize('delete', $resource);

        if ($resource->thumbnail) {
            Storage::disk('public')->delete($resource->thumbnail);
        }

        if ($resource->file_path) {
            Storage::disk('public')->delete($resource->file_path);
        }

        $resource->delete();

        return redirect()->route('resources.index')->with('success', 'Resource deleted successfully.');
    }

    public function serveFile($path)
    {
        $fullPath = 'resources/files/' . $path;
        $storagePath = storage_path('app/public/' . $fullPath);

        if (!file_exists($storagePath)) {
            abort(404);
        }

        $extension = strtolower(pathinfo($storagePath, PATHINFO_EXTENSION));

        $mimeTypes = [
            'pdf' => 'application/pdf',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'ogg' => 'audio/ogg',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'mov' => 'video/quicktime',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';

        return response()->file($storagePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($storagePath) . '"',
        ]);
    }

    public function uploadMedia(Request $request)
    {
        $this->authorize('create', Resource::class);

        $request->validate([
            'media' => 'required|file|mimes:mp3,wav,ogg,mp4,webm,mov|max:51200',
        ]);

        $file = $request->file('media');
        $path = $file->store('resources/files', 'public');
        $filename = basename($path);
        $url = route('resource.file', $filename);

        return response()->json([
            'success' => true,
            'url' => $url,
            'filename' => $filename,
        ]);
    }
}
