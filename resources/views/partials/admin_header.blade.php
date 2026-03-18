@php
  $admin_header_user = Auth::guard('admin')->user();
  $admin_header_name  = $admin_header_user ? ($admin_header_user->short_name ?: $admin_header_user->email) : 'Admin';
  $admin_header_email = $admin_header_user ? $admin_header_user->email : '';
  $admin_avatar = $admin_header_user && $admin_header_user->avatar_url
      ? asset('storage/' . $admin_header_user->avatar_url)
      : asset('assets/img/default.png');

  $admin_notif_count = $admin_header_user
      ? \App\Models\AdminNotification::unreadCountFor($admin_header_user->id)
      : 0;

  // Count unread messages
  $admin_msg_count = $admin_header_user
      ? \App\Models\AdminMessage::where('to_admin_id', $admin_header_user->id)->whereNull('read_at')->count()
      : 0;
@endphp

<style>
.admin-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 64px;
    padding: 0 24px;
    background: var(--panel);
    border-bottom: 1px solid var(--border);
    position: sticky;
    top: 0;
    z-index: 100;
}
.admin-topbar .brand a {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
}
.admin-topbar .brand img { height: 36px; }
.admin-topbar .admin-badge {
    font-size: 0.7rem;
    font-weight: 800;
    background: var(--teal, #0c8f98);
    color: white;
    padding: 2px 8px;
    border-radius: 4px;
    letter-spacing: 1px;
    text-transform: uppercase;
}
.admin-topbar-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}
.admin-icon-btn {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: transparent;
    border: 1px solid var(--border);
    color: var(--text);
    cursor: pointer;
    text-decoration: none;
    transition: background 0.2s;
}
.admin-icon-btn:hover { background: var(--hover); }
.admin-icon-btn .badge-dot {
    position: absolute;
    top: 5px;
    right: 5px;
    min-width: 17px;
    height: 17px;
    background: #ef4444;
    color: white;
    font-size: 0.65rem;
    font-weight: 800;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 3px;
    line-height: 1;
}
.admin-profile-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 6px 12px;
    border-radius: 10px;
    border: 1px solid var(--border);
    background: transparent;
    cursor: pointer;
    position: relative;
}
.admin-profile-btn img {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
}
.admin-profile-btn .meta { text-align: left; line-height: 1.2; }
.admin-profile-btn .meta .name { font-weight: 700; font-size: 0.85rem; color: var(--text); }
.admin-profile-btn .meta .role { font-size: 0.72rem; color: var(--teal, #0c8f98); font-weight: 600; }
.admin-dropdown-menu {
    display: none;
    position: absolute;
    right: 0;
    top: calc(100% + 8px);
    min-width: 200px;
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    z-index: 200;
    overflow: hidden;
}
.admin-dropdown-menu.open { display: block; }
.admin-dropdown-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 16px;
    font-size: 0.88rem;
    color: var(--text);
    text-decoration: none;
    cursor: pointer;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    transition: background 0.15s;
}
.admin-dropdown-item:hover { background: var(--hover); }
.admin-dropdown-item i, .admin-dropdown-item svg { width: 16px; height: 16px; }
.admin-dropdown-divider { border: none; border-top: 1px solid var(--border); margin: 4px 0; }
</style>

<header class="admin-topbar">
    <div class="brand">
        <a href="{{ route('admin.applications.index') }}">
            <img src="{{ asset('assets/img/AskDocPH.png') }}" alt="AskDocPH">
            <span class="admin-badge">Admin</span>
        </a>
    </div>

    <div class="admin-topbar-actions">
        {{-- Messages --}}
        <a href="{{ route('admin.messages.index') }}" class="admin-icon-btn" title="Admin Messages">
            <i data-lucide="message-square"></i>
            @if($admin_msg_count > 0)
                <span class="badge-dot">{{ $admin_msg_count > 9 ? '9+' : $admin_msg_count }}</span>
            @endif
        </a>

        {{-- Notifications --}}
        <a href="{{ route('admin.notifications.index') }}" class="admin-icon-btn" title="Notifications" id="adminNotifBtn">
            <i data-lucide="bell"></i>
            @if($admin_notif_count > 0)
                <span class="badge-dot" id="adminNotifBadge">{{ $admin_notif_count > 9 ? '9+' : $admin_notif_count }}</span>
            @endif
        </a>

        {{-- Profile dropdown --}}
        <div style="position: relative;">
            <button class="admin-profile-btn" id="adminProfileBtn" type="button">
                <img src="{{ $admin_avatar }}" alt="Admin">
                <div class="meta">
                    <div class="name">{{ $admin_header_name }}</div>
                    <div class="role">Administrator</div>
                </div>
                <i data-lucide="chevron-down" style="width:14px;height:14px;color:var(--muted)"></i>
            </button>
            <div class="admin-dropdown-menu" id="adminProfileDropdown">
                <a href="{{ route('admin.applications.index') }}" class="admin-dropdown-item">
                    <i data-lucide="users"></i> Applications
                </a>
                <a href="{{ route('admin.messages.index') }}" class="admin-dropdown-item">
                    <i data-lucide="message-square"></i> Messages
                </a>
                <a href="{{ route('admin.guidelines.index') }}" class="admin-dropdown-item">
                    <i data-lucide="book-open"></i> AI Guidelines
                </a>
                <a href="{{ route('admin.professional-titles.index') }}" class="admin-dropdown-item">
                    <i data-lucide="briefcase"></i> Professional Titles
                </a>
                <hr class="admin-dropdown-divider">
                <button type="button" class="admin-dropdown-item" id="adminThemeBtn">
                    <i data-lucide="moon"></i> Dark Mode
                </button>
                <hr class="admin-dropdown-divider">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="admin-dropdown-item" style="color: #ef4444;">
                        <i data-lucide="log-out"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('adminProfileBtn');
        const dropdown = document.getElementById('adminProfileDropdown');
        if (btn && dropdown) {
            btn.addEventListener('click', () => dropdown.classList.toggle('open'));
            document.addEventListener('click', e => {
                if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.remove('open');
                }
            });
        }

        // Theme toggle
        const themeBtn = document.getElementById('adminThemeBtn');
        if (themeBtn) {
            themeBtn.addEventListener('click', () => {
                document.body.classList.toggle('dark');
                localStorage.setItem('theme', document.body.classList.contains('dark') ? 'dark' : 'light');
            });
            if (localStorage.getItem('theme') === 'dark') document.body.classList.add('dark');
        }
    });
</script>
