@extends('layouts.dashboard')
@section('title', 'Admin Notifications')
@section('content')
<style>
.notif-list { max-width: 780px; margin: 30px auto; }
.notif-item {
    display: flex; align-items: flex-start; gap: 16px;
    padding: 18px 20px; background: var(--panel); border: 1px solid var(--border);
    border-radius: 12px; margin-bottom: 12px; transition: border-color 0.2s;
}
.notif-item.unread { border-left: 4px solid var(--teal,#0c8f98); }
.notif-icon {
    width: 42px; height: 42px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    background: rgba(12,143,152,0.12); color: var(--teal,#0c8f98);
}
.notif-icon svg { width: 20px; height: 20px; }
.notif-body { flex: 1; }
.notif-title { font-weight: 700; font-size: 0.92rem; color: var(--text); margin-bottom: 4px; }
.notif-desc { font-size: 0.83rem; color: var(--muted); line-height: 1.5; }
.notif-time { font-size: 0.75rem; color: var(--muted); margin-top: 6px; }
.notif-action {
    flex-shrink: 0; padding: 7px 14px; border-radius: 8px; font-size: 0.82rem;
    font-weight: 600; background: var(--teal,#0c8f98); color: white;
    text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
}
</style>

<main class="dash">
<div class="notif-list">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
        <h1 style="font-size: 1.4rem; font-weight: 700; margin: 0;">Notifications</h1>
        <span style="font-size: 0.85rem; color: var(--muted);">{{ $notifications->total() }} total</span>
    </div>

    @forelse($notifications as $notif)
        @php
            $data = $notif->data;
            $isRead = $notif->isReadBy($me->id);
        @endphp
        <div class="notif-item {{ $isRead ? '' : 'unread' }}">
            <div class="notif-icon">
                @if($notif->type === 'doctor_application')
                    <i data-lucide="file-text"></i>
                @else
                    <i data-lucide="bell"></i>
                @endif
            </div>
            <div class="notif-body">
                @if($notif->type === 'doctor_application')
                    <div class="notif-title">New Doctor Application</div>
                    <div class="notif-desc">
                        <strong>{{ $data['applicant_name'] ?? 'Unknown' }}</strong>
                        ({{ $data['applicant_email'] ?? '' }}) submitted a doctor application and is awaiting review.
                    </div>
                @else
                    <div class="notif-title">{{ ucwords(str_replace('_', ' ', $notif->type)) }}</div>
                    <div class="notif-desc">{{ json_encode($data) }}</div>
                @endif
                <div class="notif-time">{{ $notif->created_at->diffForHumans() }} — {{ $notif->created_at->format('M d, Y h:i A') }}</div>
            </div>
            @if(isset($data['url']))
                <a href="{{ $data['url'] }}" class="notif-action">
                    <i data-lucide="external-link" style="width:14px;height:14px;"></i> Review
                </a>
            @endif
        </div>
    @empty
        <div style="text-align: center; padding: 60px 20px; color: var(--muted);">
            <i data-lucide="bell-off" style="width:48px; height:48px; opacity:0.3; display:block; margin: 0 auto 12px;"></i>
            <p>No notifications yet.</p>
        </div>
    @endforelse

    <div style="margin-top: 20px;">
        {{ $notifications->links() }}
    </div>
</div>
</main>
@endsection
