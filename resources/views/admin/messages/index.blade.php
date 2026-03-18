@extends('layouts.dashboard')
@section('title', 'Admin Messages')
@section('content')
<style>
.admin-msg-layout { display: flex; height: calc(100vh - 64px); overflow: hidden; }
.admin-contacts { width: 300px; min-width: 300px; border-right: 1px solid var(--border); overflow-y: auto; }
.admin-contacts-header { padding: 20px; border-bottom: 1px solid var(--border); }
.admin-contacts-header h2 { font-size: 1rem; font-weight: 700; margin: 0; }
.admin-contact-item {
    display: flex; align-items: center; gap: 12px; padding: 14px 20px;
    cursor: pointer; text-decoration: none; transition: background 0.15s;
    border-bottom: 1px solid var(--border); color: var(--text);
}
.admin-contact-item:hover, .admin-contact-item.active { background: var(--hover); }
.admin-contact-avatar {
    width: 40px; height: 40px; border-radius: 50%; object-fit: cover;
    background: var(--teal, #0c8f98); display: flex; align-items: center;
    justify-content: center; font-weight: 700; color: white; font-size: 1rem; flex-shrink: 0;
}
.admin-contact-info { flex: 1; min-width: 0; }
.admin-contact-name { font-weight: 600; font-size: 0.88rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.admin-contact-preview { font-size: 0.78rem; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.admin-contact-badge {
    min-width: 18px; height: 18px; background: #ef4444; color: white;
    border-radius: 9px; font-size: 0.65rem; font-weight: 800;
    display: flex; align-items: center; justify-content: center; padding: 0 4px;
}
.admin-placeholder { display: flex; flex: 1; align-items: center; justify-content: center; flex-direction: column; gap: 12px; color: var(--muted); }
</style>

<main class="dash">
<div class="admin-msg-layout">
    {{-- Contact list --}}
    <aside class="admin-contacts">
        <div class="admin-contacts-header"><h2>Messages</h2></div>
        @forelse($admins as $admin)
            <a href="{{ route('admin.messages.show', $admin->id) }}" class="admin-contact-item">
                <div class="admin-contact-avatar">
                    {{ strtoupper(substr($admin->fname ?: $admin->email, 0, 1)) }}
                </div>
                <div class="admin-contact-info">
                    <div class="admin-contact-name">{{ $admin->short_name ?: $admin->email }}</div>
                    <div class="admin-contact-preview">
                        {{ $admin->last_message ? Str::limit($admin->last_message->body, 40) : 'No messages yet' }}
                    </div>
                </div>
                @if($admin->unread > 0)
                    <span class="admin-contact-badge">{{ $admin->unread }}</span>
                @endif
            </a>
        @empty
            <p style="padding: 20px; color: var(--muted); font-size: 0.88rem;">No other admins found.</p>
        @endforelse
    </aside>

    {{-- Placeholder --}}
    <div class="admin-placeholder">
        <i data-lucide="message-square" style="width:48px;height:48px;opacity:0.3;"></i>
        <p>Select an admin to start chatting</p>
    </div>
</div>
</main>
@endsection
