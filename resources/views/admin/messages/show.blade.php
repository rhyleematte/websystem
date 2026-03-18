@extends('layouts.dashboard')
@section('title', 'Chat with ' . ($other->short_name ?: $other->email))
@section('content')
<style>
.admin-msg-layout { display: flex; height: calc(100vh - 64px); overflow: hidden; }
.admin-thread-header {
    display: flex; align-items: center; gap: 12px; padding: 16px 20px;
    border-bottom: 1px solid var(--border); background: var(--panel); flex-shrink: 0;
}
.admin-thread-header .back-link { color: var(--muted); text-decoration: none; display: flex; align-items:center; gap:6px; font-size:0.88rem; }
.admin-thread-body { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 12px; }
.msg-bubble-wrap { display: flex; gap: 8px; }
.msg-bubble-wrap.mine { flex-direction: row-reverse; }
.msg-avatar {
    width: 32px; height: 32px; border-radius: 50%; background: var(--teal,#0c8f98);
    color: white; font-weight: 700; display: flex; align-items: center; justify-content: center;
    font-size: 0.8rem; flex-shrink: 0;
}
.msg-bubble {
    max-width: 65%; padding: 10px 14px; border-radius: 16px; font-size: 0.88rem; line-height: 1.5;
    background: var(--hover); color: var(--text);
}
.msg-bubble-wrap.mine .msg-bubble { background: var(--teal,#0c8f98); color: white; border-radius: 16px 16px 4px 16px; }
.msg-bubble-wrap:not(.mine) .msg-bubble { border-radius: 16px 16px 16px 4px; }
.msg-time { font-size: 0.7rem; color: var(--muted); margin-top: 3px; text-align: right; }
.msg-bubble-wrap:not(.mine) .msg-time { text-align: left; }
.admin-thread-input {
    border-top: 1px solid var(--border); padding: 14px 20px;
    background: var(--panel); flex-shrink: 0;
}
.admin-thread-input form { display: flex; gap: 10px; align-items: flex-end; }
.admin-thread-input textarea {
    flex: 1; resize: none; border: 1px solid var(--border); border-radius: 10px;
    background: var(--input-bg); color: var(--text); padding: 10px 14px;
    font-family: inherit; font-size: 0.9rem; max-height: 120px; min-height: 44px;
}
.admin-thread-input button {
    background: var(--teal,#0c8f98); color: white; border: none;
    border-radius: 10px; padding: 10px 18px; font-weight: 700; cursor: pointer;
    display: flex; align-items: center; gap: 6px;
}
</style>

<main class="dash" style="padding: 0;">
<div style="display: flex; flex-direction: column; height: calc(100vh - 64px);">
    <div class="admin-thread-header">
        <a href="{{ route('admin.messages.index') }}" class="back-link">
            <i data-lucide="arrow-left" style="width:16px;height:16px;"></i> Back
        </a>
        <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--teal,#0c8f98); color: white; font-weight: 700; display:flex; align-items:center; justify-content:center;">
            {{ strtoupper(substr($other->fname ?: $other->email, 0, 1)) }}
        </div>
        <div>
            <div style="font-weight: 700; font-size: 0.95rem;">{{ $other->short_name ?: $other->email }}</div>
            <div style="font-size: 0.75rem; color: var(--teal,#0c8f98); font-weight: 600;">Administrator</div>
        </div>
    </div>

    <div class="admin-thread-body" id="threadBody">
        @forelse($messages as $msg)
            @php $isMine = $msg->from_admin_id === $me->id; @endphp
            <div class="msg-bubble-wrap {{ $isMine ? 'mine' : '' }}">
                <div class="msg-avatar">
                    {{ strtoupper(substr($isMine ? ($me->fname ?: $me->email) : ($other->fname ?: $other->email), 0, 1)) }}
                </div>
                <div>
                    <div class="msg-bubble">{{ $msg->body }}</div>
                    <div class="msg-time">{{ $msg->created_at->format('M d, h:i A') }}</div>
                </div>
            </div>
        @empty
            <div style="text-align: center; color: var(--muted); padding: 40px; font-size: 0.88rem;">
                No messages yet. Start the conversation!
            </div>
        @endforelse
    </div>

    <div class="admin-thread-input">
        <form method="POST" action="{{ route('admin.messages.store', $other->id) }}">
            @csrf
            <textarea name="body" id="msgBody" placeholder="Type a message..." required rows="1"
                onkeydown="if(event.key==='Enter' && !event.shiftKey){event.preventDefault();this.form.submit();}"></textarea>
            <button type="submit">
                <i data-lucide="send" style="width:16px;height:16px;"></i> Send
            </button>
        </form>
    </div>
</div>
</main>

<script>
    // Scroll to bottom on load
    const tbody = document.getElementById('threadBody');
    if (tbody) tbody.scrollTop = tbody.scrollHeight;
</script>
@endsection
