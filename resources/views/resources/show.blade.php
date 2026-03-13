@extends('layouts.dashboard')

@section('title', $resource->title . ' – AskDocPH')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/css/resources.css') }}?v={{ time() }}">
  <style>
    .res-show-container {
        width: 100%;
        max-width: 900px;
        background: var(--panel-bg);
        border-radius: 24px;
        overflow: hidden;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-md);
        margin-bottom: 40px;
    }
    .res-show-cover {
        width: 100%;
        height: 400px;
        object-fit: cover;
    }
    .res-show-content {
        padding: 48px;
    }
    .res-show-badge {
        display: inline-block;
        padding: 6px 16px;
        background: var(--res-primary);
        color: #fff;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 24px;
    }
    .res-show-title {
        font-size: 36px;
        font-weight: 900;
        line-height: 1.2;
        margin-bottom: 24px;
        color: var(--text);
        overflow-wrap: break-word;
        word-wrap: break-word;
        word-break: break-word;
    }
    .res-show-meta {
        display: flex;
        align-items: center;
        gap: 24px;
        margin-bottom: 40px;
        padding-bottom: 24px;
        border-bottom: 1px solid var(--border);
        flex-wrap: wrap;
    }
    .res-author {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .res-author img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }
    .res-author-info {
        display: flex;
        flex-direction: column;
    }
    .res-author-name {
        font-weight: 700;
        font-size: 14px;
        color: var(--text);
    }
    .res-author-role {
        font-size: 12px;
        color: var(--muted);
    }
    .res-body-text {
        font-size: 18px;
        line-height: 1.8;
        color: var(--text);
        white-space: pre-wrap;
        overflow-wrap: break-word;
        word-wrap: break-word;
        word-break: break-all;
    }
    .res-actions-bar {
        position: sticky;
        bottom: 24px;
        background: #3b82f6;
        margin-top: 24px;
        padding: 16px 24px;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
        z-index: 10;
        color: #fff;
    }
    .share-btn-lg {
        background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
        color: #fff;
        border: none;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .share-btn-lg:hover {
        transform: scale(1.02);
    }
  </style>
@endpush

@section('content')
<div class="res-shell">

    <div class="res-body">
        <aside class="res-sidebar">
            @php $me = Auth::user(); @endphp
            @if(request('from') === 'profile' && request('profile_id'))
                <a href="{{ route('profile.show', request('profile_id')) }}?tab=resources" class="nav-item active" style="margin-bottom:16px; font-weight:500;">
                    <i data-lucide="arrow-left"></i><span>Back to My Profile</span>
                </a>
            @else
                <a href="{{ route('resources.index') }}" class="nav-item active" style="margin-bottom:16px; font-weight:500;">
                    <i data-lucide="arrow-left"></i><span>Back to Resources</span>
                </a>
            @endif
            
            <div class="panel mini-panel" style="margin-top: 24px;">
                <div class="mini-title"><i data-lucide="sparkles"></i><span>Curated Resource</span></div>
                <p class="mini-text">This expert-led content is part of our verified professional library.</p>
            </div>
            
            @if(Auth::check() && Auth::user()->can('update', $resource))
            <div class="panel mini-panel" style="margin-top: 16px; border-color: var(--border);">
                <div class="mini-title"><i data-lucide="settings"></i><span>Management</span></div>
                <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 12px;">
                    <a href="{{ route('resources.edit', $resource->id) }}" class="chip-btn" style="width: 100%; justify-content: center; background: var(--hover); border-color: var(--border);">
                        <i data-lucide="edit-3"></i> Edit Resource
                    </a>
                    
                    <form action="{{ route('resources.destroy', $resource->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this resource?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="chip-btn" style="width: 100%; justify-content: center; color: var(--danger); background: transparent; border-color: var(--danger); opacity: 0.8;">
                            <i data-lucide="trash-2"></i> Delete Resource
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </aside>

        <main class="res-main">
            <div class="res-show-container">
                @if($resource->thumbnail)
                <img src="{{ $resource->thumbnail_url }}" alt="{{ $resource->title }}" class="res-show-cover">
                @endif

                <div class="res-show-content">
                    <span class="res-show-badge">{{ $resource->type }}</span>
                    <h1 class="res-show-title">{{ $resource->title }}</h1>
                    
                    <div class="res-show-meta">
                        <div class="res-author">
                            <img src="{{ $resource->user->avatar_url }}" alt="{{ $resource->user->full_name }}">
                            <div class="res-author-info">
                                <span class="res-author-name">{{ $resource->user->full_name }}</span>
                                <span class="res-author-role">Verified Expert</span>
                            </div>
                        </div>
                        <div class="res-meta-item">
                            <i data-lucide="calendar"></i>
                            <span>Published {{ $resource->created_at->format('M d, Y') }}</span>
                        </div>
                        @if($resource->duration_meta)
                        <div class="res-meta-item">
                            <i data-lucide="clock"></i>
                            <span>{{ $resource->duration_meta }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="res-body-content" style="display: flex; flex-direction: column; gap: 32px;">
                        @if(in_array($resource->file_type, ['pdf']))
                            <div class="res-inline-viewer" style="height: 600px; border-radius: 16px; overflow: hidden; border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
                                <iframe src="{{ $resource->file_url }}" width="100%" height="100%" style="border: none;"></iframe>
                            </div>
                        @endif

                            {{-- Legacy: Inline viewer for docs only --}}
                            @if(in_array($resource->file_type, ['doc', 'docx']))
                                <div class="res-inline-viewer" style="height: 600px; border-radius: 16px; overflow: hidden; border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
                                    <iframe src="https://docs.google.com/gview?url={{ urlencode($resource->file_url) }}&embedded=true" width="100%" height="100%" style="border: none;"></iframe>
                                </div>
                            @elseif(in_array($resource->file_type, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                <img src="{{ $resource->file_url }}" style="width: 100%; border-radius: 16px; border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
                            @endif
                        </div>

                        @php
                            $safeContent = $resource->content ?: $resource->description;
                            // Multi-pass cleanup for any persistent blob URLs
                            $safeContent = preg_replace('/<(video|audio|source|img)\s+[^>]*src="blob:[^"]+"[^>]*>.*?<\/\1>/is', '', $safeContent);
                            $safeContent = preg_replace('/<(video|audio|source|img)\s+[^>]*src="blob:[^"]+"[^>]*>/is', '', $safeContent);
                            // Cleanup empty paragraphs left behind
                            $safeContent = preg_replace('/<p>\s*<\/p>/i', '', $safeContent);
                        @endphp
                        <div class="res-body-text">{!! $safeContent !!}</div>
                    </div>

                <div class="res-actions-bar">
                    <div style="font-size: 14px; font-weight: 600;">
                        Found this helpful? Join it and share with your community.
                    </div>
                    <div style="display:flex; gap:10px; align-items:center;">
                        @auth
                          @if($isJoined ?? false)
                            <form method="POST" action="{{ route('resources.unjoin', $resource->id) }}">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="share-btn-lg" style="background:#10b981;">
                                <i data-lucide="check"></i> Joined
                              </button>
                            </form>
                          @else
                            <form method="POST" action="{{ route('resources.join', $resource->id) }}">
                              @csrf
                              <button type="submit" class="share-btn-lg" style="background:#22c55e;">
                                <i data-lucide="user-plus"></i> Join Resource
                              </button>
                            </form>
                          @endif
                        @endauth

                        <button class="share-btn-lg js-share-resource" type="button" data-resource-id="{{ $resource->id }}" data-preview="{{ $resource->title }}">
                            <i data-lucide="share-2"></i> Share to Feed
                        </button>
                    </div>
                </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Activate inline PDF placeholders with the actual resource file URL
    const fileUrl = "{{ $resource->file_url }}";
    if (fileUrl) {
        document.querySelectorAll('.pdf-link-placeholder').forEach(link => {
            link.href = fileUrl;
            link.target = "_blank";
        });
    }
});
</script>

<div id="dash-toast" class="dash-toast" style="position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); z-index: 1000;"></div>

@endsection
