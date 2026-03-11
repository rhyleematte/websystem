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
        return view('resources.index', compact('resources'));
    }

    public function show(Resource $resource)
    {
        // For Article vs others, logic might differ but we'll use same show page for now
        return view('resources.show', compact('resource'));
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
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
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
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
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

    public function share(Resource $resource)
    {
        // Sharing a resource creates a post
        $post = Post::create([
            'user_id' => Auth::id(),
            'resource_id' => $resource->id,
            'post_type' => 'resource_share',
            'text_content' => "Shared a resource: " . $resource->title,
            'hashtags' => $resource->hashtags,
        ]);

        return response()->json(['ok' => true, 'message' => 'Shared to feed!']);
    }
}
