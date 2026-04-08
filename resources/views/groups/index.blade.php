@extends('layouts.dashboard')

@section('title', 'Support Groups – AskDocPH')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/css/groups.css') }}">
@endpush

@section('content')
@php
  $me        = Auth::user();
@endphp

<main class="dash">
  <div class="dash-body">
    @include('partials.sidebar', ['active' => 'groups'])

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

      <div class="groups-toolbar">
        <div class="groups-search-wrap">
          <i data-lucide="search"></i>
          <input type="text" id="groupHeaderSearch" placeholder="Search groups..." autocomplete="off">
        </div>
        <div class="groups-filter-wrap">
          <select id="groupSortSelect">
            <option value="newest" {{ ($sort ?? 'newest') === 'newest' ? 'selected' : '' }}>Newest</option>
            <option value="oldest" {{ ($sort ?? 'newest') === 'oldest' ? 'selected' : '' }}>Oldest</option>
            <option value="members_desc" {{ ($sort ?? 'newest') === 'members_desc' ? 'selected' : '' }}>Highest Members</option>
            <option value="members_asc" {{ ($sort ?? 'newest') === 'members_asc' ? 'selected' : '' }}>Lowest Members</option>
            <option value="active_desc" {{ ($sort ?? 'newest') === 'active_desc' ? 'selected' : '' }}>Most Active</option>
            <option value="active_asc" {{ ($sort ?? 'newest') === 'active_asc' ? 'selected' : '' }}>Least Active</option>
          </select>
        </div>
      </div>

      <div class="groups-grid">
        @foreach($groups as $group)
        @php
            $isJoined = in_array($group->id, $myGroupIds);
        @endphp
        <div class="group-card">
          <div class="group-cover" style="background-image: url('{{ $group->cover_url }}'); background-size: cover; background-position: center;">
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
</main>

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

// Simple header search filter for groups list
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('groupHeaderSearch');
    const cards = Array.from(document.querySelectorAll('.groups-grid .group-card'));

    if (!searchInput || !cards.length) return;

    const norm = (s) => (s || '').toString().trim().toLowerCase();

    function apply() {
        const q = norm(searchInput.value);
        cards.forEach(card => {
            const titleEl = card.querySelector('.group-title');
            const descEl = card.querySelector('.group-desc');
            const hay = norm((titleEl?.textContent || '') + ' ' + (descEl?.textContent || ''));
            const show = !q || hay.indexOf(q) !== -1;
            card.style.display = show ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', apply);
});

// Sort selector
document.addEventListener('DOMContentLoaded', () => {
    const sortSelect = document.getElementById('groupSortSelect');
    if (!sortSelect) return;

    sortSelect.addEventListener('change', () => {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', sortSelect.value);
        window.location.href = url.toString();
    });
});
</script>
@endpush
