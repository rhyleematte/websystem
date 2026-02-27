@extends('layouts.dashboard')

@section('title', $profileUser->full_name . ' – AskDocPH')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">
@endpush

@section('content')
@php
  $me        = Auth::user();
  $isOwn     = $me && $me->id === $profileUser->id;
  $avatarUrl = $profileUser->avatar_url;
  $fullName  = $profileUser->short_name ?: $profileUser->full_name;
  $shortName = $me ? ($me->short_name ?: $me->full_name) : '';
  $username  = $profileUser->username ?? 'username';
@endphp

{{-- ════════════════════════════════════════════════════════════════
     PROFILE WRAPPER
═══════════════════════════════════════════════════════════════════ --}}
<div class="prof-shell">

  {{-- ── Topbar (same style as dashboard) ── --}}
  <header class="dash-topbar">
    <div class="brand">
      <img src="{{ asset('assets/img/AskDocPH.png') }}" class="logo" alt="AskDocPH">
    </div>

    <div class="dash-search">
      <i data-lucide="search"></i>
      <input type="text" placeholder="Search for support, resources, or people…" />
    </div>

    <div class="dash-actions">
      <a href="{{ route('user.dashboard') }}" class="icon-btn" title="Dashboard">
        <i data-lucide="home"></i>
      </a>

      <button class="icon-btn" type="button" aria-label="Notifications">
        <i data-lucide="bell"></i>
      </button>

      {{-- Profile dropdown --}}
      <div class="avatar-dropdown">
        <button class="avatar-btn" type="button" id="profileToggle" aria-label="Profile">
          <img src="{{ $me ? $me->avatar_url : asset('assets/img/default.png') }}" alt="User" />
          <div class="avatar-meta">
            <div class="avatar-name">{{ $shortName }}</div>
            <div class="avatar-username">{{ $me ? '@' . $me->username : '' }}</div>
          </div>
          <i data-lucide="chevron-down" class="dropdown-icon"></i>
        </button>

        <div class="dropdown-menu" id="profileDropdown">
          <a href="{{ route('profile.show', $me->id) }}" class="dropdown-profile-link">
            <div class="dropdown-profile">
              <div class="dropdown-avatar"><img src="{{ $me->avatar_url }}" alt="User" /></div>
              <div class="dropdown-info">
                <div class="profile-fullname">{{ $shortName }}</div>
                <div class="profile-username">{{ '@' . $me->username }}</div>
              </div>
            </div>
          </a>
          <button type="button" class="dropdown-item" id="themeToggleBtn">
            <i data-lucide="moon"></i><span>Dark mode</span>
          </button>
          <hr class="dropdown-divider">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dropdown-logout">
              <i data-lucide="log-out"></i><span>Logout</span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </header>

  {{-- ── Main Layout ── --}}
  <div class="prof-body">

    {{-- ══ LEFT – sticky sidebar ══ --}}
    <aside class="prof-sidebar">
      <div class="panel nav-panel">
        <a class="nav-item" href="{{ route('user.dashboard') }}">
          <i data-lucide="home"></i><span>Feed</span>
        </a>
        <a class="nav-item" href="#">
          <i data-lucide="users"></i><span>Support Groups</span>
        </a>
        <a class="nav-item" href="#">
          <i data-lucide="book-open"></i><span>Resources</span>
        </a>
        <a class="nav-item active" href="{{ route('profile.show', $profileUser->id) }}">
          <i data-lucide="user"></i><span>My Profile</span>
        </a>
      </div>
    </aside>

    {{-- ══ CENTER – profile content ══ --}}
    <main class="prof-main">

      {{-- ─ Cover + Avatar card ─ --}}
      <div class="panel prof-card">
        <div class="prof-cover">
          <div class="prof-cover-grad"></div>
        </div>

        <div class="prof-card-body">
          {{-- Avatar with upload overlay --}}
          <div class="prof-avatar-wrap">
            <img src="{{ $avatarUrl }}" alt="{{ $fullName }}" class="prof-avatar" id="previewAvatar">
            @if($isOwn)
            <div class="prof-avatar-actions">
              <label for="photoUpload" class="avatar-action-btn" title="Change photo">
                <i data-lucide="camera"></i>
              </label>
              <button type="button" class="avatar-action-btn danger" id="deletePhotoBtn" title="Remove photo">
                <i data-lucide="trash-2"></i>
              </button>
            </div>
            <input type="file" id="photoUpload" accept="image/*" class="hidden-input">
            @endif
          </div>

          <div class="prof-card-info">
            <div class="prof-card-names">
              <h1 class="prof-fullname">{{ $fullName }}</h1>
              <span class="prof-handle" id="profHandle">{{ '@' . $username }}</span>
              @if($profileUser->role === 'doctor')
                <span class="verified-badge"><i data-lucide="badge-check"></i> Verified Doctor</span>
              @endif
            </div>

            @if($profileUser->bio)
            <p class="prof-bio" id="bioDisplay">{{ $profileUser->bio }}</p>
            @else
            <p class="prof-bio muted" id="bioDisplay">{{ $isOwn ? 'Add a short bio…' : 'No bio yet.' }}</p>
            @endif

            <div class="prof-stats">
              <div class="stat-item">
                <span class="stat-num" id="postCountBadge">{{ $posts->count() }}</span>
                <span class="stat-lbl">Posts</span>
              </div>
              <div class="stat-item">
                <span class="stat-num">0</span>
                <span class="stat-lbl">Groups</span>
              </div>
              <div class="stat-item">
                <span class="stat-num">0</span>
                <span class="stat-lbl">Resources</span>
              </div>
            </div>
          </div>

          @if($isOwn)
          <button class="edit-profile-btn" id="editProfileBtn" type="button">
            <i data-lucide="pencil"></i> Edit Profile
          </button>
          @endif
        </div>
      </div>

      {{-- ─ Edit Profile Modal ─ --}}
      @if($isOwn)
      <div class="modal-backdrop" id="editModal">
        <div class="modal-box">
          <div class="modal-header">
            <h2>Edit Profile</h2>
            <button class="modal-close" id="closeEditModal" type="button"><i data-lucide="x"></i></button>
          </div>
          <form id="editProfileForm" novalidate>
            @csrf
            <div class="form-row">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="fname" id="inp_fname" value="{{ $profileUser->fname }}" required>
              </div>
              <div class="form-group">
                <label>Middle Name</label>
                <input type="text" name="mname" id="inp_mname" value="{{ $profileUser->mname }}">
              </div>
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="lname" id="inp_lname" value="{{ $profileUser->lname }}" required>
              </div>
            </div>
            <div class="form-group">
              <label>Username</label>
              <div class="input-prefix-wrap">
                <span class="input-prefix">@</span>
                <input type="text" name="username" id="inp_username" value="{{ $profileUser->username }}" required>
              </div>
            </div>
            <div class="form-group">
              <label>Bio <span class="muted">(max 300 chars)</span></label>
              <textarea name="bio" id="inp_bio" maxlength="300" rows="3">{{ $profileUser->bio }}</textarea>
              <div class="char-count"><span id="bioCharCount">{{ strlen($profileUser->bio ?? '') }}</span>/300</div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn-cancel" id="cancelEditBtn">Cancel</button>
              <button type="submit" class="btn-save" id="saveProfileBtn">
                <i data-lucide="save"></i> Save Changes
              </button>
            </div>
            <div class="form-feedback" id="editFeedback"></div>
          </form>
        </div>
      </div>
      @endif

      {{-- ─ Tabs ─ --}}
      <div class="panel prof-tabs-wrap">
        <nav class="prof-tabs" id="profTabs">
          <button class="tab-btn active" data-tab="posts">
            <i data-lucide="file-text"></i> Posts
          </button>
          <button class="tab-btn" data-tab="groups">
            <i data-lucide="users"></i> Groups
          </button>
          <button class="tab-btn" data-tab="resources">
            <i data-lucide="book-open"></i> Resources
          </button>
        </nav>
      </div>

      {{-- ─ Posts Tab ─ --}}
      <div class="tab-content" id="tab-posts">

        {{-- Composer (own profile only) --}}
        @if($isOwn)
        <div class="panel composer">
          <div class="composer-top">
            <div class="avatar sm"><img src="{{ $avatarUrl }}" alt="User"></div>
            <textarea id="postText" placeholder="Share your thoughts, feelings, or progress…" rows="3"></textarea>
          </div>
          <div class="composer-preview" id="mediaPreviewArea"></div>
          <div class="composer-bottom">
            <label for="postMedia" class="chip-btn">
              <i data-lucide="image"></i> Photo / Video
            </label>
            <input type="file" id="postMedia" accept="image/*,video/*" multiple class="hidden-input">
            <button class="share-btn" type="button" id="submitPostBtn">
              Post <i data-lucide="send"></i>
            </button>
          </div>
          <div class="composer-feedback" id="postFeedback"></div>
        </div>
        @endif

        {{-- Posts List --}}
        <div id="postsFeed">
          @forelse($posts as $post)
          @include('profile._post', ['post' => $post, 'isOwn' => $isOwn, 'me' => $me])
          @empty
          <div class="empty-state panel">
            <i data-lucide="file-text"></i>
            <p>No posts yet.</p>
          </div>
          @endforelse
        </div>
      </div>

      {{-- ─ Groups Tab ─ --}}
      <div class="tab-content hidden" id="tab-groups">
        <div class="empty-state panel">
          <i data-lucide="users"></i>
          <p>No support group activity yet.</p>
          <a href="#" class="btn-primary">Browse Groups</a>
        </div>
      </div>

      {{-- ─ Resources Tab ─ --}}
      <div class="tab-content hidden" id="tab-resources">
        <div class="empty-state panel">
          <i data-lucide="book-open"></i>
          <p>No resource activity yet.</p>
          <a href="#" class="btn-primary">Browse Resources</a>
        </div>
      </div>

    </main>
  </div>
</div>

{{-- Toast notification --}}
<div class="toast" id="toast"></div>

{{-- JS Config — routes and page state --}}
<script>
  window.PROFILE_USER_ID = {{ $profileUser->id }};
  window.IS_OWN_PROFILE  = {{ $isOwn ? 'true' : 'false' }};
  window.AUTH_USER_ID    = {{ $me ? $me->id : 'null' }};
  window.ROUTES = {
    updateInfo:    '{{ route('profile.update.info') }}',
    updatePhoto:   '{{ route('profile.update.photo') }}',
    deletePhoto:   '{{ route('profile.delete.photo') }}',
    storePost:     '{{ route('profile.posts.store') }}',
    updatePost:    function(id){ return '/profile/posts/' + id; },
    destroyPost:   function(id){ return '/profile/posts/' + id; },
    toggleLike:    function(id){ return '/profile/posts/' + id + '/like'; },
    storeComment:  function(id){ return '/profile/posts/' + id + '/comments'; },
    destroyComment:function(id){ return '/profile/comments/' + id; },
  };
</script>
@endsection

@push('scripts')
  <script src="{{ asset('assets/js/profile.js') }}" defer></script>
@endpush
