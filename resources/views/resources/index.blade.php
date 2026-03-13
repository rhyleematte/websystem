@extends('layouts.dashboard')

@section('title', 'Resources – AskDocPH')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/css/resources.css') }}?v={{ time() }}">
@endpush

@section('content')
@php
  $me = Auth::user();
@endphp

<div class="res-shell">

  <div class="res-body">
    <aside class="res-sidebar">
      <div class="panel nav-panel">
        <a class="nav-item" href="{{ route('user.dashboard') }}"><i data-lucide="home"></i><span>Feed</span></a>
        <a class="nav-item" href="{{ route('groups.index') }}"><i data-lucide="users"></i><span>Support Groups</span></a>
        <a class="nav-item active" href="{{ route('resources.index') }}"><i data-lucide="book-open"></i><span>Resources</span></a>
        <a class="nav-item" href="{{ route('profile.show', $me->id) }}"><i data-lucide="user"></i><span>My Profile</span></a>
      </div>
      
      <div class="panel mini-panel">
        <div class="mini-title"><i data-lucide="sparkles"></i><span>Curated for You</span></div>
        <p class="mini-text">Explore professional guides, audio sessions, and workbooks designed by verified experts.</p>
      </div>
    </aside>

    <main class="res-main">
      <div class="res-header-panel">
        <div class="res-header-left">
          <h1>Mental Health Resources</h1>
          <p>Find professional tools, articles, and media to support your wellness journey.</p>
        </div>
        @can('create', App\Models\Resource::class)
        <div class="res-header-right">
          <a href="{{ route('resources.create') }}" class="create-res-btn">
            <i data-lucide="plus"></i> Create Resource
          </a>
        </div>
        @endcan
      </div>

      {{-- Search + Filters --}}
      <div class="res-filterbar panel">
        <div class="res-search">
          <i data-lucide="search"></i>
          <input id="resSearchInput" type="text" placeholder="Search resources..." autocomplete="off">
        </div>
        <div class="res-filters" id="resFilters">
          <button class="chip-btn active" type="button" data-filter="all">All</button>
          <button class="chip-btn" type="button" data-filter="Article">Articles</button>
          <button class="chip-btn" type="button" data-filter="Audio">Audio</button>
          <button class="chip-btn" type="button" data-filter="Video">Videos</button>
          <button class="chip-btn" type="button" data-filter="Workbook">Workbooks</button>
        </div>
      </div>

      <div class="res-grid">
        @forelse($resources as $res)
        <div class="res-card"
             data-type="{{ $res->type }}"
             data-title="{{ strtolower($res->title ?? '') }}"
             data-desc="{{ strtolower($res->description ?? '') }}">
          <div class="res-card-thumb">
            <img src="{{ $res->thumbnail_url }}" alt="{{ $res->title }}">
            <span class="res-card-type">{{ $res->type }}</span>
          </div>
          <div class="res-card-content">
            <h3 class="res-card-title">{{ \Illuminate\Support\Str::limit($res->title, 50) }}</h3>
            <p class="res-card-desc">{{ \Illuminate\Support\Str::limit($res->description, 100) }}</p>
            
            <div class="res-card-footer">
              <div class="res-card-meta">
                <div class="res-meta-item">
                  <i data-lucide="user"></i>
                  <span>{{ $res->user->short_name ?: $res->user->full_name }}</span>
                </div>
                @if($res->duration_meta)
                <div class="res-meta-item">
                  <i data-lucide="clock"></i>
                  <span>{{ $res->duration_meta }}</span>
                </div>
                @endif
              </div>
              <div>
                @auth
                  @php
                    $isJoined = in_array($res->id, $joinedResourceIds ?? []);
                  @endphp
                  <a href="{{ route('resources.show', $res->id) }}" class="res-card-btn">
                    {{ $isJoined ? 'Joined' : 'View More' }}
                  </a>
                @endauth

                @guest
                  <a href="{{ route('resources.show', $res->id) }}" class="res-card-btn">View More</a>
                @endguest
              </div>
            </div>
          </div>
        </div>
        @empty
        <div class="res-empty" style="grid-column: 1 / -1;">
          <i data-lucide="book-copy"></i>
          <p>No resources found yet. Check back soon!</p>
        </div>
        @endforelse
      </div>

      <div class="res-empty hidden" id="resNoResults" style="grid-column: 1 / -1; margin-top: 16px;">
        <i data-lucide="search-x"></i>
        <p>No matching resources.</p>
      </div>
    </main>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  var search = document.getElementById('resSearchInput');
  var filters = document.getElementById('resFilters');
  var cards = Array.from(document.querySelectorAll('.res-grid .res-card'));
  var noResults = document.getElementById('resNoResults');
  var buttons = Array.from(document.querySelectorAll('.res-card-btn')).slice(0, 5);

  // #region agent log: resource card button layout probe (H1: button clipped)
  try {
    if (buttons.length) {
      var payload = {
        sessionId: 'b31335',
        runId: 'res-btn-fit',
        hypothesisId: 'H1',
        location: 'resources/index.blade.php:res-card-btn-probe',
        message: 'res_card_btn_metrics',
        data: buttons.map(function (btn, idx) {
          return {
            idx: idx,
            text: btn.textContent.trim(),
            clientWidth: btn.clientWidth,
            scrollWidth: btn.scrollWidth,
            parentWidth: btn.parentElement ? btn.parentElement.clientWidth : null
          };
        }),
        timestamp: Date.now()
      };
      fetch('http://127.0.0.1:7658/ingest/8b61fa6d-3718-4953-90ff-348851f37aa5', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Debug-Session-Id': 'b31335'
        },
        body: JSON.stringify(payload)
      }).catch(function () {});
    }
  } catch (e) {
    // ignore debug failures
  }
  // #endregion agent log: resource card button layout probe

  function norm(s){ return (s || '').toString().trim().toLowerCase(); }
  var state = { q: '', type: 'all' };

  function apply() {
    var shown = 0;
    cards.forEach(function (card) {
      var type = card.dataset.type || '';
      var hay = (card.dataset.title || '') + ' ' + (card.dataset.desc || '') + ' ' + norm(type);
      var okType = (state.type === 'all') || (type === state.type);
      var okQ = !state.q || hay.indexOf(state.q) !== -1;
      var show = okType && okQ;
      card.style.display = show ? '' : 'none';
      if (show) shown++;
    });

    if (noResults) noResults.classList.toggle('hidden', shown !== 0 || cards.length === 0);
    if (window.lucide) lucide.createIcons();
  }

  if (search) {
    search.addEventListener('input', function () {
      state.q = norm(search.value);
      apply();
    });
  }

  if (filters) {
    filters.addEventListener('click', function (e) {
      var btn = e.target.closest('button[data-filter]');
      if (!btn) return;
      state.type = btn.dataset.filter || 'all';
      filters.querySelectorAll('button[data-filter]').forEach(function (b) {
        b.classList.toggle('active', b === btn);
      });
      apply();
    });
  }
});
</script>
@endpush
