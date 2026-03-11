@extends('layouts.dashboard')

@section('title', $group->name . ' – AskDocPH')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/css/groups.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
@endpush

@section('content')
@php
  $avatarUrl = $me ? $me->avatar_url : asset('assets/img/default.png');
  $fullName  = $me ? ($me->short_name ?: $me->full_name) : 'User';
  $username  = $me ? $me->username : 'username';
@endphp

{{-- Inject JS routes for the Composer --}}
<script>
window.DASH_ROUTES = {
  feed:          "", // No dynamic feed fetching on load here; posts are loaded server-side
  storePost:     "{{ route('profile.posts.store') }}",
  toggleLike:    function(id){ return "/profile/posts/" + id + "/like"; },
  storeComment:  function(id){ return "/profile/posts/" + id + "/comments"; },
  destroyComment:function(id){ return "/profile/comments/" + id; },
  destroyPost:   function(id){ return "/profile/posts/" + id; },
  updatePost:    function(id){ return "/profile/posts/" + id; },
};
window.MY_AVATAR      = "{{ $avatarUrl }}";
window.MY_NAME        = "{{ addslashes($fullName) }}";
window.MY_ID          = {{ $me->id ?? 'null' }};
window.MY_PROFILE_URL = "{{ route('profile.show', $me->id ?? 0) }}";
</script>

<div class="groups-shell">
  {{-- ── Topbar (Same as index) ── --}}
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
    <aside class="groups-sidebar">
      <a href="{{ route('groups.index') }}" class="nav-item active" style="margin-bottom:16px; font-weight:500;">
        <i data-lucide="arrow-left"></i><span>Back to Groups</span>
      </a>

      {{-- Guidelines Moved to Left Sidebar --}}
      <div class="panel group-guidelines-widget" style="margin-top: 24px; padding: 20px;">
        <h3 style="font-size:16px; color:var(--text); margin-bottom:16px; font-weight:700;">Group Guidelines</h3>
        @if($group->guidelines)
        <ul class="group-guidelines-list" style="padding-left: 20px;">
          @foreach(explode("\n", $group->guidelines) as $rule)
            @if(trim($rule))
              <li style="font-size:14px; margin-bottom:12px;">
                <div class="guideline-text">{{ trim($rule) }}</div>
                <button type="button" class="read-more-btn guideline-toggle" style="display:none;">Read More</button>
              </li>
            @endif
          @endforeach
        </ul>
        @else
        <ul class="group-guidelines-list" style="padding-left: 20px;">
          <li style="font-size:14px; margin-bottom:12px;">Be respectful and supportive of all members.</li>
          <li style="font-size:14px; margin-bottom:12px;">Maintain confidentiality.</li>
          <li style="font-size:14px; margin-bottom:12px;">No medical advice allowed.</li>
        </ul>
        @endif
      </div>
    </aside>

    <main class="groups-main">
      {{-- ── Group Hero Header ── --}}
      <div class="panel group-hero">
        <div class="group-hero-cover" style="{{ $group->cover_photo ? 'background-image: url(' . asset('storage/' . $group->cover_photo) . '); background-size: cover; background-position: center;' : 'background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);' }}">
          @if(Auth::id() === $group->creator_id)
          <div class="dropdown" style="position:absolute; bottom:24px; right:24px; z-index:10;">
            <button id="coverToggleBtn" class="btn dropdown-toggle" style="background:rgba(15,23,42,0.6); backdrop-filter:blur(8px); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:8px; padding:8px 16px; font-size:13px; font-weight:500; display:flex; gap:8px; align-items:center;">
              <i data-lucide="camera" style="width:16px; height:16px;"></i> Edit Cover
            </button>
            <div id="coverDropdownMenu" class="dropdown-menu" style="min-width:180px; padding:8px; border-radius:12px;">
              <label for="groupCoverInput" style="display:flex; width:100%; text-align:left; padding:10px 12px; border-radius:8px; cursor:pointer; align-items:center; gap:8px; font-size:13px; color:var(--text);" onmouseover="this.style.background='var(--hover)'" onmouseout="this.style.background='none'">
                <i data-lucide="upload" style="width:16px; height:16px;"></i> Upload New Photo
              </label>
              <button id="removeCoverBtn" onclick="deleteGroupCover({{ $group->id }})" style="display: {{ $group->cover_photo ? 'flex' : 'none' }}; width:100%; text-align:left; padding:10px 12px; border-radius:8px; background:none; border:none; color:var(--danger); align-items:center; gap:8px; font-size:13px; cursor:pointer;" onmouseover="this.style.background='var(--hover)'" onmouseout="this.style.background='none'">
                <i data-lucide="trash-2" style="width:16px; height:16px;"></i> Remove Cover
              </button>
            </div>
          </div>
          <input type="file" id="groupCoverInput" accept="image/*" style="display:none;" onchange="uploadGroupCover(this, {{ $group->id }})">
          @endif
        </div>

        <div class="group-hero-body">
          <div class="group-hero-title-row">
            <div>
              <h1 class="group-hero-title">{{ $group->name }}</h1>
              <p class="group-hero-desc" id="groupDesc">{{ $group->description }}</p>
              <button type="button" class="read-more-btn" id="descReadMore" style="display:none;">Read More</button>
              
              <div class="group-stats">
                <div class="group-stats-item">
                  <i data-lucide="users"></i> {{ number_format($group->members_count) }} members
                </div>
                <div class="group-stats-item group-active-stat" style="margin-left:16px;">
                  <i data-lucide="trending-up"></i> {{ $group->activity_level }}
                </div>
              </div>
            </div>

            @if($isMember)
              @if($me->id !== $group->creator_id)
              <button class="btn primary" onclick="leaveGroup({{ $group->id }})" style="padding:10px 24px; border-radius:8px; background:linear-gradient(90deg, #7c3aed, #4f46e5); box-shadow: 0 6px 16px rgba(124, 58, 237, 0.2); border:none; color:#fff;">
                Leave Group
              </button>
              @endif
            @else
            <button class="btn primary" onclick="joinGroup({{ $group->id }})" style="padding:10px 24px; border-radius:8px; background:linear-gradient(90deg, #7c3aed, #4f46e5); box-shadow: 0 6px 16px rgba(124, 58, 237, 0.2); border:none; color:#fff;">
              Join Group
            </button>
            @endif
          </div>

          <div class="group-mod-section">
            <h4 style="font-size:14px; color:#64748b; margin-bottom:12px; font-weight:600;">Moderators</h4>
            <div class="group-mod-list">
              <div class="group-mod-avatars" style="display:flex; gap:8px;">
                @if($group->creator)
                  <img src="{{ $group->creator->avatar_url }}" alt="{{ $group->creator->full_name }}" title="Creator: {{ $group->creator->full_name }}" style="width:36px; height:36px; border-radius:50%; object-fit:cover; border:2px solid #fff; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
                @endif
                @foreach($group->members->where('role', 'admin') as $adminMember)
                  @if(!$group->creator || $group->creator->id !== $adminMember->user_id)
                    <img src="{{ $adminMember->user->avatar_url }}" alt="{{ $adminMember->user->full_name }}" title="Moderator: {{ $adminMember->user->full_name }}" style="width:36px; height:36px; border-radius:50%; object-fit:cover; border:2px solid #fff; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
                  @endif
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- ── 2-Column Split ── --}}
      <div style="width: 100%;">
        {{-- Left: Feed --}}
        <div class="group-feed">
          
          @if($isMember)
          {{-- ── Post Composer ── --}}
          <input type="hidden" id="dashGroupId" value="{{ $group->id }}">
          
          <div class="panel composer" id="composerPanel" style="margin-bottom: 24px;">
            <div class="composer-top">
              <div class="avatar sm">
                <img src="{{ $avatarUrl }}" alt="You" />
              </div>
              <textarea id="dashPostText" placeholder="Share your thoughts with the group..."></textarea>
            </div>
            <div id="mediaPreviewArea" class="media-preview-grid" style="display:none;"></div>
            <div class="hashtag-row" id="hashtagRow" style="display:none;">
              <i data-lucide="hash"></i>
              <input type="text" id="hashtagInput" placeholder="anxiety, hope, recovery  (comma-separated)" />
            </div>
            

            <div class="mood-bar" id="moodBar" style="display:none;">
              <span class="mood-label">How are you feeling?</span>
              <div class="mood-options">
                <button class="mood-btn" type="button" data-mood="😊 Happy">😊 Happy</button>
                <button class="mood-btn" type="button" data-mood="😔 Sad">😔 Sad</button>
                <button class="mood-btn" type="button" data-mood="😰 Anxious">😰 Anxious</button>
              </div>
              <div id="selectedMoodDisplay" class="selected-mood" style="display:none;"></div>
            </div>

            <div class="composer-bottom">
              <label class="chip-btn" for="mediaUpload" title="Attach photo/video" style="cursor:pointer;">
                <i data-lucide="image"></i> Photo
              </label>
              <input type="file" id="mediaUpload" accept="image/*,video/*" multiple style="display:none;" />
              <button class="chip-btn" type="button" id="moodToggleBtn" title="Add mood"><i data-lucide="smile"></i> Mood</button>
              <button class="chip-btn" type="button" id="hashtagToggleBtn" title="Add hashtags"><i data-lucide="hash"></i> Tags</button>
              <div class="link-popup-wrap" id="linkWrap">
                <button class="chip-btn" type="button" id="linkToggleBtn" title="Add link">
                  <i data-lucide="link"></i> Link
                </button>
                <div class="link-popup-card" id="linkRow" onclick="event.stopPropagation()">
                  <div class="link-popup-inputs">
                    <div class="link-popup-row">
                      <i data-lucide="type" class="link-popup-icon" style="width:16px;height:16px;"></i>
                      <input type="text" id="linkNameInput" placeholder="Text">
                    </div>
                    <div class="link-popup-row">
                      <i data-lucide="link" class="link-popup-icon" style="width:16px;height:16px;"></i>
                      <input type="url" id="linkUrlInput" placeholder="Type or paste a link" onkeydown="if(event.key==='Enter'){document.getElementById('applyLinkBtn').click();event.preventDefault();}">
                    </div>
                  </div>
                  <button type="button" class="link-popup-apply" id="applyLinkBtn">Apply</button>
                </div>
              </div>
              <div id="composerFeedback" class="composer-feedback"></div>
              <button class="share-btn" type="button" id="dashShareBtn" style="background:var(--primary);">
                Post <i data-lucide="send"></i>
              </button>
            </div>
          </div>
          @else
          <div class="panel empty-state">
            <i data-lucide="lock"></i>
            <p>You must join this group to view and create posts.</p>
          </div>
          @endif

          {{-- Feed Container --}}
          @if($isMember)
          <div id="dashFeed">
            @forelse($posts as $post)
              @include('profile._post', ['post' => $post, 'me' => $me])
            @empty
              <div id="feedEmpty" class="empty-state panel">
                <i data-lucide="file-text"></i>
                <p>No posts yet. Be the first to share something!</p>
              </div>
            @endforelse
          </div>
          @endif
        </div>

        {{-- Right: Guidelines (Moved to Left Sidebar) --}}
      </div>
    </main>
  </div>
</div>

<div id="toast" class="toast"></div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

async function leaveGroup(id) {
    try {
        let res = await fetch(`/groups/${id}/leave`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        });
        let data = await res.json();
        if(data.ok) location.reload();
    } catch(e) {}
}

async function uploadGroupCover(input, groupId) {
    if (!input.files || !input.files[0]) return;
    
    // Instantly preview the image using a temporary object URL
    const file = input.files[0];
    const objectUrl = URL.createObjectURL(file);
    const heroCover = document.querySelector('.group-hero-cover');
    if (heroCover) {
        heroCover.style.background = 'none';
        heroCover.style.backgroundImage = `url("${objectUrl}")`;
        heroCover.style.backgroundSize = 'cover';
        heroCover.style.backgroundPosition = 'center';
    }

    // Close the dropdown immediately
    const dropdownMenu = document.getElementById('coverDropdownMenu');
    if (dropdownMenu) dropdownMenu.classList.remove('open');

    let fd = new FormData();
    fd.append('cover_photo', file);

    try {
        let res = await fetch(`/groups/${groupId}/update-cover`, {
            method: 'POST',
            body: fd,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        let data = await res.json();
        
        if (data.ok) {
            Swal.fire({
                title: 'Cover Updated',
                text: 'Your group cover photo has been successfully changed.',
                icon: 'success',
                confirmButtonColor: '#7c3aed'
            }).then(() => {
                const removeBtn = document.getElementById('removeCoverBtn');
                if (removeBtn) {
                    removeBtn.style.display = 'flex';
                } else {
                     const dropdownMenu = document.getElementById('coverDropdownMenu');
                     if(dropdownMenu) {
                         const btnHTML = `
                         <button id="removeCoverBtn" onclick="deleteGroupCover(${groupId})" style="width:100%; text-align:left; padding:10px 12px; border-radius:8px; background:none; border:none; color:var(--danger); display:flex; align-items:center; gap:8px; font-size:13px; cursor:pointer;" onmouseover="this.style.background='var(--hover)'" onmouseout="this.style.background='none'">
                           <i data-lucide="trash-2" style="width:16px; height:16px;"></i> Remove Cover
                         </button>`;
                         dropdownMenu.insertAdjacentHTML('beforeend', btnHTML);
                         if(window.lucide) lucide.createIcons();
                     }
                }
            });
            input.value = ''; // Reset input to allow selecting the same file again
        } else {
            Swal.fire('Error', data.message || 'Failed to update cover photo.', 'error');
        }
    } catch (e) {
        console.error(e);
        Swal.fire('Error', 'An unexpected error occurred.', 'error');
    }
}

async function deleteGroupCover(groupId) {
    const result = await Swal.fire({
        title: 'Remove Cover Photo?',
        text: 'This will permanently remove the custom cover photo and revert to the default gradient.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, remove it'
    });

    if (result.isConfirmed) {
        try {
            let res = await fetch(`/groups/${groupId}/delete-cover`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            let data = await res.json();
            
            if (data.ok) {
                Swal.fire({
                    title: 'Removed!',
                    text: 'The cover photo has been removed.',
                    icon: 'success',
                    confirmButtonColor: '#7c3aed'
                }).then(() => {
                    const heroCover = document.querySelector('.group-hero-cover');
                    if (heroCover) {
                        heroCover.style.backgroundImage = 'none';
                        heroCover.style.background = 'linear-gradient(135deg, #1e293b 0%, #0f172a 100%)';
                        heroCover.style.backgroundSize = 'auto';
                        heroCover.style.backgroundPosition = '0% 0%';
                    }
                    const removeBtn = document.getElementById('removeCoverBtn');
                    if(removeBtn) {
                        removeBtn.style.display = 'none';
                    }
                });
            } else {
                Swal.fire('Error', data.message || 'Failed to remove cover photo.', 'error');
            }
        } catch (e) {
            console.error(e);
            Swal.fire('Error', 'An unexpected error occurred.', 'error');
        }
    }
    
    // Close the dropdown immediately
    const dropdownMenu = document.getElementById('coverDropdownMenu');
    if (dropdownMenu) dropdownMenu.classList.remove('open');
}

document.addEventListener('DOMContentLoaded', function() {
    const coverToggleBtn = document.getElementById('coverToggleBtn');
    const coverDropdownMenu = document.getElementById('coverDropdownMenu');

    if (coverToggleBtn && coverDropdownMenu) {
        coverToggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            coverDropdownMenu.classList.toggle('open');
        });

        document.addEventListener('click', function(e) {
            if (!coverToggleBtn.contains(e.target) && !coverDropdownMenu.contains(e.target)) {
                coverDropdownMenu.classList.remove('open');
            }
        });
    }

    // Read More Logic for Description
    const desc = document.getElementById('groupDesc');
    const descBtn = document.getElementById('descReadMore');
    if (desc && descBtn) {
        if (desc.scrollHeight > desc.clientHeight) {
            descBtn.style.display = 'block';
        }
        descBtn.addEventListener('click', function() {
            desc.classList.toggle('expanded');
            this.textContent = desc.classList.contains('expanded') ? 'Read Less' : 'Read More';
        });
    }

    // Read More Logic for Guidelines
    document.querySelectorAll('.group-guidelines-list li').forEach(li => {
        const text = li.querySelector('.guideline-text');
        const btn = li.querySelector('.guideline-toggle');
        if (text && btn) {
            // Add initial clamping class to the div if not already on li
            text.style.display = '-webkit-box';
            text.style.webkitLineClamp = '3';
            text.style.webkitBoxOrient = 'vertical';
            text.style.overflow = 'hidden';

            if (text.scrollHeight > text.clientHeight) {
                btn.style.display = 'block';
            }

            btn.addEventListener('click', function() {
                if (text.style.webkitLineClamp === '3') {
                    text.style.webkitLineClamp = 'unset';
                    this.textContent = 'Read Less';
                } else {
                    text.style.webkitLineClamp = '3';
                    this.textContent = 'Read More';
                }
            });
        }
    });
});
</script>
{{-- Note: dashboard.js handles Composer and Post interactivity via window.DASH_ROUTES hooks --}}
@endpush
