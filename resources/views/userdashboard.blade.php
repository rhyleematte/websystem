@extends('layouts.dashboard')

@section('title', 'Dashboard - AskDocPH')

@section('content')
@php
  $user      = Auth::user();
  $avatarUrl = $user->avatar_url;
  $fullName  = $user->full_name ?: ($user->name ?? 'User');
  $shortName = $user->short_name ?: $fullName;
  $username  = $user->username ?? 'username';
@endphp

{{-- Inject JS routes --}}
<script>
window.DASH_ROUTES = {
  feed:          "{{ route('dashboard.feed') }}",
  storePost:     "{{ route('profile.posts.store') }}",
  toggleLike:    function(id){ return "/profile/posts/" + id + "/like"; },
  storeComment:  function(id){ return "/profile/posts/" + id + "/comments"; },
  destroyComment:function(id){ return "/profile/comments/" + id; },
  destroyPost:   function(id){ return "/profile/posts/" + id; },
};
window.MY_AVATAR      = "{{ $avatarUrl }}";
window.MY_NAME        = "{{ addslashes($fullName) }}";
window.MY_ID          = {{ Auth::id() }};
window.MY_PROFILE_URL = "{{ route('profile.show', Auth::id()) }}";
</script>

<main class="dash">

  {{-- Top bar --}}
  <header class="dash-topbar">
    <div class="brand">
      <img src="{{ asset('assets/img/AskDocPH.png') }}" class="logo" alt="AskDocPH">
    </div>

    <div class="dash-search">
      <i data-lucide="search"></i>
      <input type="text" placeholder="Search for support, resources, or people..." />
    </div>

    <div class="dash-actions">
      <button class="icon-btn" type="button" aria-label="Messages">
        <i data-lucide="message-circle"></i>
      </button>
      <button class="icon-btn" type="button" aria-label="Notifications">
        <i data-lucide="bell"></i>
        <span class="dot"></span>
      </button>

      {{-- Profile dropdown --}}
      <div class="avatar-dropdown">
        <button class="avatar-btn" type="button" id="profileToggle"
                aria-label="Profile" aria-haspopup="true" aria-expanded="false">
          <img src="{{ $avatarUrl }}" alt="User" />
          <div class="avatar-meta">
            <div class="avatar-name">{{ $shortName }}</div>
            <div class="avatar-username">{{ '@'.$username }}</div>
          </div>
          <i data-lucide="chevron-down" class="dropdown-icon"></i>
        </button>

        <div class="dropdown-menu" id="profileDropdown" aria-labelledby="profileToggle">
          <a href="{{ route('profile.show', Auth::id()) }}" class="dropdown-profile-link">
            <div class="dropdown-profile">
              <div class="dropdown-avatar"><img src="{{ $avatarUrl }}" alt="User" /></div>
              <div class="dropdown-info">
                <div class="profile-fullname">{{ $fullName }}</div>
                <div class="profile-username">{{ '@'.$username }}</div>
              </div>
            </div>
          </a>
          <hr class="dropdown-divider">
          <button type="button" class="dropdown-item" id="themeToggleBtn">
            <i data-lucide="moon"></i><span>Dark mode</span>
          </button>
          <hr class="dropdown-divider">
          <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit" class="dropdown-logout">
              <i data-lucide="log-out"></i><span>Logout</span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </header>

  {{-- Body --}}
  <div class="dash-body">

    {{-- Left sidebar --}}
    <aside class="dash-left">
      <div class="panel nav-panel">
        <a class="nav-item active" href="#"><i data-lucide="home"></i><span>Feed</span></a>
        <a class="nav-item" href="{{ route('groups.index') }}"><i data-lucide="users"></i><span>Support Groups</span></a>
        <a class="nav-item" href="#"><i data-lucide="book-open"></i><span>Resources</span></a>
        <a class="nav-item" href="{{ route('profile.show', Auth::id()) }}">
          <i data-lucide="user"></i><span>My Profile</span>
        </a>
        @if(Auth::user()->role !== 'doctor' && Auth::user()->doctor_status !== 'approved' && Auth::user()->doctor_status !== 'none' && Auth::user()->doctor_status !== null)
        <a class="nav-item" href="{{ route('profile.show', Auth::id()) }}?tab=application">
          <i data-lucide="stethoscope"></i><span>Apply as Doctor</span>
        </a>
        @endif
      </div>

      <div class="panel mini-panel">
        <div class="mini-title"><i data-lucide="sparkles"></i><span>Daily Affirmation</span></div>
        <p class="mini-text">"You are worthy of support and belonging. Your journey is unique, and every step forward is progress."</p>
      </div>

      <div class="panel mini-panel danger">
        <div class="mini-title"><i data-lucide="life-buoy"></i><span>Crisis Support</span></div>
        <p class="mini-sub">If you're in crisis, help is available 24/7</p>
        <button class="danger-btn" type="button">Get Help Now</button>
      </div>
    </aside>

    {{-- Main feed --}}
    <section class="dash-main">

      {{-- ── Post Composer ── --}}
      <div class="panel composer" id="composerPanel">
        <div class="composer-top">
          <div class="avatar sm">
            <img src="{{ $avatarUrl }}" alt="You" />
          </div>
          <textarea id="dashPostText" placeholder="Share your thoughts, feelings, or progress..."></textarea>
        </div>

        {{-- Media preview --}}
        <div id="mediaPreviewArea" class="media-preview-grid" style="display:none;"></div>

        {{-- Hashtag input --}}
        <div class="hashtag-row" id="hashtagRow" style="display:none;">
          <i data-lucide="hash"></i>
          <input type="text" id="hashtagInput" placeholder="anxiety, hope, recovery  (comma-separated)" />
        </div>

        {{-- Mood bar --}}
        <div class="mood-bar" id="moodBar" style="display:none;">
          <span class="mood-label">How are you feeling?</span>
          <div class="mood-options">
            <button class="mood-btn" type="button" data-mood="😊 Happy">😊 Happy</button>
            <button class="mood-btn" type="button" data-mood="😔 Sad">😔 Sad</button>
            <button class="mood-btn" type="button" data-mood="😰 Anxious">😰 Anxious</button>
            <button class="mood-btn" type="button" data-mood="😤 Stressed">😤 Stressed</button>
            <button class="mood-btn" type="button" data-mood="🥰 Grateful">🥰 Grateful</button>
            <button class="mood-btn" type="button" data-mood="😴 Tired">😴 Tired</button>
            <button class="mood-btn" type="button" data-mood="💪 Motivated">💪 Motivated</button>
            <button class="mood-btn" type="button" data-mood="😌 Calm">😌 Calm</button>
          </div>
          <div id="selectedMoodDisplay" class="selected-mood" style="display:none;"></div>
        </div>

        <div class="composer-bottom">
          {{-- Photo button --}}
          <label class="chip-btn" for="mediaUpload" title="Attach photo/video" style="cursor:pointer;">
            <i data-lucide="image"></i> Photo
          </label>
          <input type="file" id="mediaUpload" accept="image/*,video/*" multiple style="display:none;" />

          {{-- Mood button --}}
          <button class="chip-btn" type="button" id="moodToggleBtn" title="Add mood">
            <i data-lucide="smile"></i> Mood
          </button>

          {{-- Hashtag button --}}
          <button class="chip-btn" type="button" id="hashtagToggleBtn" title="Add hashtags">
            <i data-lucide="hash"></i> Tags
          </button>

          <div id="composerFeedback" class="composer-feedback"></div>

          <button class="share-btn" type="button" id="dashShareBtn">
            Share <i data-lucide="send"></i>
          </button>
        </div>
      </div>

      {{-- Feed --}}
      <div id="dashFeed">
        <div class="feed-loading panel" id="feedLoading">
          <i data-lucide="loader"></i>
          <span>Loading posts…</span>
        </div>
      </div>

      <div id="feedEmpty" class="empty-state panel" style="display:none;">
        <i data-lucide="file-text"></i>
        <p>No posts yet. Be the first to share something!</p>
      </div>

    </section>
  </div>
</main>

{{-- Toast --}}
<div id="dash-toast" class="dash-toast" aria-live="polite"></div>

@endsection
