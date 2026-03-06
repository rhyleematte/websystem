@extends('layouts.dashboard')

@section('title', 'Support Groups – AskDocPH')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/css/groups.css') }}">
@endpush

@section('content')
@php
  $me        = Auth::user();
  $avatarUrl = $me ? $me->avatar_url : asset('assets/img/default.png');
  $fullName  = $me ? ($me->short_name ?: $me->full_name) : 'User';
  $username  = $me ? $me->username : 'username';
@endphp

<div class="groups-shell">
  {{-- ── Topbar ── --}}
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
        <button class="avatar-btn" type="button" id="profileToggle">
          <img src="{{ $avatarUrl }}" alt="User" />
          <div class="avatar-meta">
            <div class="avatar-name">{{ $fullName }}</div>
            <div class="avatar-username">{{ '@' . $username }}</div>
          </div>
          <i data-lucide="chevron-down" class="dropdown-icon"></i>
        </button>

        <div class="dropdown-menu" id="profileDropdown">
          <a href="{{ route('profile.show', $me->id) }}" class="dropdown-profile-link">
            <div class="dropdown-profile">
              <div class="dropdown-avatar"><img src="{{ $avatarUrl }}" alt="User" /></div>
              <div class="dropdown-info">
                <div class="profile-fullname">{{ $fullName }}</div>
                <div class="profile-username">{{ '@' . $username }}</div>
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

  <div class="groups-body">
    {{-- ══ LEFT – sticky sidebar ══ --}}
    <aside class="groups-sidebar">
      <div class="panel nav-panel">
        <a class="nav-item" href="{{ route('user.dashboard') }}">
          <i data-lucide="home"></i><span>Feed</span>
        </a>
        <a class="nav-item active" href="{{ route('groups.index') }}">
          <i data-lucide="users"></i><span>Support Groups</span>
        </a>
        <a class="nav-item" href="#">
          <i data-lucide="book-open"></i><span>Resources</span>
        </a>
        <a class="nav-item" href="{{ route('profile.show', $me->id) }}">
          <i data-lucide="user"></i><span>My Profile</span>
        </a>
      </div>
      
      <div class="panel mini-panel">
        <div class="mini-title"><i data-lucide="sparkles"></i><span>Daily Affirmation</span></div>
        <p class="mini-text" style="font-style:italic; color:#7c3aed;">"You are worthy of support and belonging. Your journey is unique, and every step forward is progress."</p>
      </div>

      <div class="panel mini-panel danger">
        <div class="mini-title"><i data-lucide="life-buoy"></i><span>Crisis Support</span></div>
        <p class="mini-sub">If you're in crisis, help is available 24/7</p>
        <button class="danger-btn" type="button">Get Help Now</button>
      </div>
    </aside>

    {{-- ══ CENTER – main content ══ --}}
    <main class="groups-main">
      <div class="groups-header-panel">
        <div class="groups-header-left">
          <h1>Support Groups</h1>
          <p>Connect with others who understand your journey. Join supportive communities focused on healing and growth.</p>
        </div>
        @if($me->doctor_status === 'approved')
        <div class="groups-header-right">
          <button class="create-group-btn" onclick="document.getElementById('createGroupModal').classList.add('open')">
            <i data-lucide="plus"></i> Create Group
          </button>
        </div>
        @endif
      </div>

      <div class="groups-grid">
        @foreach($groups as $group)
        @php
            $isJoined = in_array($group->id, $myGroupIds);
        @endphp
        <div class="group-card">
          <div class="group-cover" style="{{ $group->cover_photo ? 'background-image: url(' . asset('storage/' . $group->cover_photo) . '); background-size: cover; background-position: center;' : 'background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);' }}">
          </div>
          <div class="group-info">
            <h2 class="group-title">{{ $group->name }}</h2>
            <p class="group-desc">{{ Str::limit($group->description, 100) }}</p>
            
            <div class="group-stats">
              <div class="group-stats-item">
                <i data-lucide="users"></i> {{ number_format($group->members_count) }} members
              </div>
              <div class="group-stats-item group-active-stat">
                <i data-lucide="trending-up"></i> {{ $group->activity_level }}
              </div>
            </div>

            @if($isJoined)
              <a href="{{ route('groups.show', $group->id) }}" class="group-btn joined">
                <i data-lucide="eye"></i> View
              </a>
            @else
              <button class="group-btn join" onclick="joinGroup({{ $group->id }})">
                Join Group
              </button>
            @endif
          </div>
        </div>
        @endforeach
      </div>
    </main>
  </div>
</div>

{{-- Create Group Modal --}}
@if($me->doctor_status === 'approved')
<div class="modal-backdrop" id="createGroupModal">
  <div class="modal-box" style="max-width: 500px;">
    <div class="modal-header">
      <h2>Create Support Group</h2>
      <button class="modal-close" onclick="document.getElementById('createGroupModal').classList.remove('open')" type="button"><i data-lucide="x"></i></button>
    </div>
    
    <div style="padding: 0 24px 20px; color: var(--muted); font-size: 14px;">
      Please fill out the necessary details below to create a new support community for users.
    </div>

    <form id="createGroupForm" onsubmit="createGroup(event)" enctype="multipart/form-data" style="padding: 0 24px 24px;">
      @csrf

      <div class="form-group" style="margin-bottom: 20px;">
        <label style="display:block; margin-bottom:8px; font-weight:600; color:var(--text); font-size:14px;">Group Cover Photo <span style="font-weight:400; color:var(--muted); font-size:13px;">(Optional)</span></label>
        <div class="cover-upload-wrapper" style="position:relative; width:100%; height:120px; border:2px dashed var(--border); border-radius:10px; background:var(--input-bg); display:flex; flex-direction:column; align-items:center; justify-content:center; cursor:pointer; overflow:hidden; transition:all 0.2s;">
            <input type="file" name="cover_photo" id="groupCover" accept="image/*" style="opacity:0; position:absolute; inset:0; z-index:10; cursor:pointer; width:100%; height:100%;">
            <div id="coverUploadCTA" style="text-align:center; color:var(--muted); pointer-events:none;">
                <i data-lucide="image" style="width:24px; height:24px; margin-bottom:8px; opacity:0.6;"></i>
                <div style="font-size:13px; font-weight:500;">Click to upload a cover photo</div>
                <div style="font-size:11px; margin-top:4px; opacity:0.8;">JPG, PNG up to 10MB</div>
            </div>
            <img id="coverPreview" src="" style="display:none; position:absolute; width:100%; height:100%; object-fit:cover; inset:0; z-index:5;">
        </div>
      </div>

      <div class="form-group" style="margin-bottom: 20px;">
        <label style="display:block; margin-bottom:8px; font-weight:600; color:var(--text); font-size:14px;">Group Name <span style="color:var(--danger);">*</span></label>
        <input type="text" name="name" required placeholder="e.g. Anxiety Support Circle" style="width:100%; padding:12px; border:1px solid var(--border); background:var(--input-bg); color:var(--text); border-radius:10px; font-size:14px;">
      </div>
      <div class="form-group" style="margin-bottom: 20px;">
        <label style="display:block; margin-bottom:8px; font-weight:600; color:var(--text); font-size:14px;">Description <span style="color:var(--danger);">*</span></label>
        <textarea name="description" required rows="3" placeholder="What is the main focus or journey of this group?" style="width:100%; padding:12px; border:1px solid var(--border); background:var(--input-bg); color:var(--text); border-radius:10px; font-size:14px; resize:vertical;"></textarea>
      </div>
      <div class="form-group" style="margin-bottom: 24px;">
        <label style="display:block; margin-bottom:8px; font-weight:600; color:var(--text); font-size:14px;">Guidelines <span style="font-weight:400; color:var(--muted); font-size:13px;">(Optional)</span></label>
        <textarea name="guidelines" rows="4" placeholder="e.g. Be respectful, no medical advice..." style="width:100%; padding:12px; border:1px solid var(--border); background:var(--input-bg); color:var(--text); border-radius:10px; font-size:14px; resize:vertical;"></textarea>
      </div>
      <div class="form-actions" style="display: flex; justify-content: flex-end; gap: 12px;">
        <button type="button" class="btn secondary" onclick="document.getElementById('createGroupModal').classList.remove('open')" style="padding:10px 20px; border-radius:10px; font-weight:600; background:var(--hover); color:var(--text); border:1px solid var(--border);">Cancel</button>
        <button type="submit" class="btn primary" style="background:linear-gradient(90deg, #7c3aed, #4f46e5); color:#fff; border:none; padding:10px 24px; border-radius:10px; font-weight:600; box-shadow:0 4px 12px rgba(124,58,237,0.2);">Create Group</button>
      </div>
    </form>
  </div>
</div>
@endif

{{-- Toast --}}
<div id="toast" class="toast"></div>

@endsection

@push('scripts')
<script>
async function joinGroup(id) {
    try {
        let res = await fetch(`/groups/${id}/join`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        });
        let data = await res.json();
        if(data.ok) location.reload();
    } catch(e) {}
}

async function createGroup(e) {
    e.preventDefault();
    let fd = new FormData(e.target);
    try {
        let res = await fetch(`{{ route('groups.store') }}`, {
            method: 'POST',
            body: fd,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        let data = await res.json();
        if(data.ok) window.location.href = data.redirect;
    } catch(e) {}
}

document.addEventListener('DOMContentLoaded', () => {
    const coverInput = document.getElementById('groupCover');
    const coverPreview = document.getElementById('coverPreview');
    const coverUploadCTA = document.getElementById('coverUploadCTA');

    if (coverInput) {
        coverInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    coverPreview.src = e.target.result;
                    coverPreview.style.display = 'block';
                    coverUploadCTA.style.display = 'none';
                }
                reader.readAsDataURL(file);
            } else {
                coverPreview.src = '';
                coverPreview.style.display = 'none';
                coverUploadCTA.style.display = 'block';
            }
        });
    }
});
</script>
@endpush
