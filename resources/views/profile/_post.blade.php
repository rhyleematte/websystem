@php
  $liked    = $me ? $post->isLikedBy($me->id) : false;
  $canManage = $me && $post->user_id === $me->id;
@endphp

<article class="panel post" data-post-id="{{ $post->id }}">
  {{-- Post header --}}
  <div class="post-head">
    <div class="avatar md">
      <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->full_name }}">
    </div>
    <div class="post-meta">
      <div class="post-name" style="display:flex; align-items:center; gap:6px;">
        <span style="font-weight:600; color:var(--text);">{{ $post->user->full_name }}</span>
        @if($post->user->role === 'doctor' && $post->user->doctor_status === 'approved')
          @php
             $titles = $post->user->doctorApplication ? $post->user->doctorApplication->professional_titles : '';
          @endphp
          <span class="verified-doctor-badge" title="Certified Doctor" style="display:inline-flex; align-items:center; gap:4px; background:#eff6ff; color:#3b82f6; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:600; border:1px solid #bfdbfe; user-select:none;">
            <i data-lucide="badge-check" style="width:14px; height:14px;"></i>
            {{ trim($titles) ? $titles : 'Certified Doctor' }}
          </span>
        @endif
      </div>
      <div class="post-sub">{{ '@' . $post->user->username }} · {{ $post->created_at->diffForHumans() }}</div>
    </div>
    @if($canManage)
    <div class="post-menu-wrap">
      <button class="icon-btn post-menu-btn" type="button" title="Options">
        <i data-lucide="more-horizontal"></i>
      </button>
      <div class="post-menu hidden">
        <button class="post-menu-item edit-post-btn" type="button"
            data-post-id="{{ $post->id }}"
            data-text="{{ htmlspecialchars($post->text_content, ENT_QUOTES) }}">
          <i data-lucide="pencil"></i> Edit
        </button>
        <button class="post-menu-item delete-post-btn danger" type="button"
            data-post-id="{{ $post->id }}">
          <i data-lucide="trash-2"></i> Delete
        </button>
      </div>
    </div>
    @endif
  </div>

  {{-- Post body --}}
  @if($post->text_content)
  <div class="post-body post-text-content">{{ $post->text_content }}</div>
  @endif

  {{-- Media grid --}}
  @if($post->media->isNotEmpty())
  <div class="post-media-grid media-count-{{ min($post->media->count(), 4) }}">
    @foreach($post->media->take(4) as $media)
      @if($media->media_type === 'video')
        <video src="{{ asset('storage/' . $media->path) }}" controls class="post-media-item"></video>
      @else
        <img src="{{ asset('storage/' . $media->path) }}" alt="Post image" class="post-media-item">
      @endif
    @endforeach
    @if($post->media->count() > 4)
      <div class="media-more">+{{ $post->media->count() - 4 }}</div>
    @endif
  </div>
  @endif

  {{-- Actions --}}
  <div class="post-actions">
    <button class="post-btn like-btn {{ $liked ? 'liked' : '' }}" type="button"
        data-post-id="{{ $post->id }}">
      <i data-lucide="{{ $liked ? 'heart' : 'heart' }}" class="like-icon"></i>
      <span class="like-count">{{ $post->likes->count() }}</span>
    </button>

    <button class="post-btn comment-toggle-btn" type="button" data-post-id="{{ $post->id }}">
      <i data-lucide="message-square"></i>
      <span class="comment-count">{{ $post->allComments()->count() }}</span>
    </button>

    <button class="post-btn" type="button">
      <i data-lucide="share-2"></i>
    </button>

    <button class="post-btn end" type="button" title="Save">
      <i data-lucide="bookmark"></i>
    </button>
  </div>

  {{-- Comments section --}}
  <div class="comments-section hidden" id="comments-{{ $post->id }}">
    {{-- Comment input --}}
    @auth
    <div class="comment-composer">
      <div class="avatar sm">
        <img src="{{ Auth::user()->avatar_url }}" alt="You">
      </div>
      <div class="comment-input-wrap">
        <input type="text"
               class="comment-input"
               placeholder="Write a comment…"
               data-post-id="{{ $post->id }}">
        <button class="comment-send-btn" type="button" data-post-id="{{ $post->id }}">
          <i data-lucide="send"></i>
        </button>
      </div>
    </div>
    @endauth

    {{-- Existing comments --}}
    <div class="comments-list" id="comments-list-{{ $post->id }}">
      @foreach($post->comments as $comment)
        @include('profile._comment', ['comment' => $comment, 'me' => $me, 'post' => $post])
      @endforeach
    </div>
  </div>
</article>
