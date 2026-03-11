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
  {{-- Reuse Topbar --}}
  @include('resources._topbar')

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

      {{-- Filters/Tabs (Optional but nice) --}}
      <div class="res-filters panel" style="margin-bottom: 24px; padding: 12px 20px; display: flex; gap: 12px; border-radius: 12px;">
        <button class="chip-btn active">All</button>
        <button class="chip-btn">Articles</button>
        <button class="chip-btn">Audio</button>
        <button class="chip-btn">Videos</button>
        <button class="chip-btn">Workbooks</button>
      </div>

      <div class="res-grid">
        @forelse($resources as $res)
        <div class="res-card">
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
              <a href="{{ route('resources.show', $res->id) }}" class="res-card-btn">View More</a>
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
    </main>
  </div>
</div>
@endsection
