@extends('layouts.app')

@section('title', 'Dashboard - AskDocPH')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
@endpush

@section('content')
<main class="dash">

  {{-- Top bar --}}
  <header class="dash-topbar">
    <div class="brand">
    <img src="{{ asset('assets/img/AskDocPH.png') }}" class="logo">
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

      <div class="avatar" title="Profile">
        <img src="https://i.pravatar.cc/80?img=32" alt="User" />
      </div>
    </div>
  </header>

  {{-- Body --}}
  <div class="dash-body">

    {{-- Left sidebar --}}
    <aside class="dash-left">
      <div class="panel nav-panel">
        <a class="nav-item active" href="#">
          <i data-lucide="home"></i>
          <span>Feed</span>
        </a>
        <a class="nav-item" href="#">
          <i data-lucide="users"></i>
          <span>Support Groups</span>
        </a>
        <a class="nav-item" href="#">
          <i data-lucide="book-open"></i>
          <span>Resources</span>
        </a>
        <a class="nav-item" href="#">
          <i data-lucide="user"></i>
          <span>My Profile</span>
        </a>
      </div>

      <div class="panel mini-panel">
        <div class="mini-title">
          <i data-lucide="sparkles"></i>
          <span>Daily Affirmation</span>
        </div>
        <p class="mini-text">
          “You are worthy of support and belonging. Your journey is unique, and every step forward is progress.”
        </p>
      </div>

      <div class="panel mini-panel danger">
        <div class="mini-title">
          <i data-lucide="life-buoy"></i>
          <span>Crisis Support</span>
        </div>
        <p class="mini-sub">If you're in crisis, help is available 24/7</p>
        <button class="danger-btn" type="button">
          Get Help Now
        </button>
      </div>
    </aside>

    {{-- Main feed --}}
    <section class="dash-main">

      {{-- Create post --}}
      <div class="panel composer">
        <div class="composer-top">
          <div class="avatar sm">
            <img src="https://i.pravatar.cc/80?img=32" alt="User" />
          </div>
          <textarea placeholder="Share your thoughts, feelings, or progress..."></textarea>
        </div>

        <div class="composer-bottom">
          <button class="chip-btn" type="button">
            <i data-lucide="image"></i>
            Photo
          </button>
          <button class="chip-btn" type="button">
            <i data-lucide="smile"></i>
            Mood
          </button>

          <button class="share-btn" type="button">
            Share
            <i data-lucide="send"></i>
          </button>
        </div>
      </div>

      {{-- Post 1 --}}
      <article class="panel post">
        <div class="post-head">
          <div class="avatar md">
            <img src="https://i.pravatar.cc/80?img=47" alt="Doctor" />
          </div>
          <div class="post-meta">
            <div class="post-name">
              Dr. Sarah Johnson
              <span class="verified" title="Verified">
                <i data-lucide="badge-check"></i>
              </span>
            </div>
            <div class="post-sub">Clinical Psychologist | Anxiety & Depression Specialist</div>
          </div>
          <div class="post-time">• 2 hours ago</div>
        </div>

        <div class="post-body">
          Today marks 30 days of practicing daily meditation. It's been challenging, but I'm already noticing improvements
          in my anxiety levels. Small steps lead to big changes 🌱
        </div>

        <div class="post-tags">
          <span class="tag">#anxiety</span>
          <span class="tag">#meditation</span>
          <span class="tag">#progress</span>
        </div>

        <div class="post-actions">
          <button class="post-btn" type="button">
            <i data-lucide="heart"></i> <span>47</span>
          </button>
          <button class="post-btn" type="button">
            <i data-lucide="message-square"></i> <span>12</span>
          </button>
          <button class="post-btn" type="button">
            <i data-lucide="share-2"></i>
          </button>
          <button class="post-btn end" type="button" title="Save">
            <i data-lucide="bookmark"></i>
          </button>
        </div>
      </article>

      {{-- Post 2 --}}
      <article class="panel post">
        <div class="post-head">
          <div class="avatar md">
            <img src="https://i.pravatar.cc/80?img=12" alt="User" />
          </div>
          <div class="post-meta">
            <div class="post-name">Michael Chen</div>
            <div class="post-sub">Member</div>
          </div>
          <div class="post-time">• 5 hours ago</div>
        </div>

        <div class="post-body">
          Reminder: It's okay to not be okay. Reaching out for help is a sign of strength, not weakness.
          I started therapy last month and it's one of the best decisions I've ever made.
        </div>

        <div class="post-actions">
          <button class="post-btn" type="button">
            <i data-lucide="heart"></i> <span>18</span>
          </button>
          <button class="post-btn" type="button">
            <i data-lucide="message-square"></i> <span>3</span>
          </button>
          <button class="post-btn" type="button">
            <i data-lucide="share-2"></i>
          </button>
          <button class="post-btn end" type="button" title="Save">
            <i data-lucide="bookmark"></i>
          </button>
        </div>
      </article>

    </section>
  </div>
</main>
@endsection

@push('scripts')
  <script src="{{ asset('assets/js/dashboard.js') }}" defer></script>
@endpush
