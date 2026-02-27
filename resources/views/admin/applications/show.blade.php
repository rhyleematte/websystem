@extends('layouts.app')

@section('title', 'Admin - Application #' . $application->id)

@push('styles')
<style>
.admin-container {
    max-width: 1000px;
    margin: 40px auto;
    padding: 30px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    color: white;
}
.header-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    padding-bottom: 15px;
}
.header-top h1 {
    font-size: 1.8rem;
    margin: 0;
}
.badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
}
.badge.pending { background: rgba(243, 156, 18, 0.2); color: #f39c12; border: 1px solid rgba(243, 156, 18, 0.5); }
.badge.approved { background: rgba(46, 204, 113, 0.2); color: #2ecc71; border: 1px solid rgba(46, 204, 113, 0.5); }
.badge.rejected { background: rgba(231, 76, 60, 0.2); color: #e74c3c; border: 1px solid rgba(231, 76, 60, 0.5); }

.detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 30px;
}
.detail-item {
    background: rgba(0, 0, 0, 0.2);
    padding: 15px;
    border-radius: 8px;
}
.detail-item strong {
    display: block;
    color: rgba(255,255,255,0.6);
    font-size: 0.85rem;
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.document-list {
    margin-top: 20px;
    background: rgba(0, 0, 0, 0.1);
    padding: 20px;
    border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.05);
}
.document-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.document-item:last-child {
    border-bottom: none;
}
.doc-name {
    font-weight: 500;
}
.doc-desc {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.5);
}
.btn-dl {
    padding: 8px 16px;
    background: rgba(0, 210, 255, 0.2);
    color: #00d2ff;
    text-decoration: none;
    border-radius: 6px;
    border: 1px solid rgba(0, 210, 255, 0.5);
    transition: background 0.3s;
    font-size: 0.9rem;
}
.btn-dl:hover {
    background: rgba(0, 210, 255, 0.3);
}

.action-bar {
    margin-top: 40px;
    padding-top: 20px;
    border-top: 1px solid rgba(255,255,255,0.1);
    display: flex;
    gap: 15px;
}
.action-box {
    flex: 1;
    background: rgba(0, 0, 0, 0.2);
    padding: 20px;
    border-radius: 8px;
}
.action-box textarea {
    width: 100%;
    height: 80px;
    padding: 10px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 6px;
    color: white;
    margin-bottom: 15px;
    resize: none;
}
.btn-approve {
    background: #2ecc71;
    border: none;
    color: white;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
}
.btn-reject {
    background: #e74c3c;
    border: none;
    color: white;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
}
</style>
@endpush

@section('content')
<main class="wrap" style="height: auto; min-height: 100vh; padding: 40px 20px;">
    <div class="admin-container">
        <a href="{{ route('admin.applications.index') }}" style="color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.9em; display: inline-block; margin-bottom: 15px;">&larr; Back to Applications</a>
        
        <div class="header-top">
            <h1>Application #{{ $application->id }}</h1>
            <span class="badge {{ $application->status }}">{{ $application->status }}</span>
        </div>

        <div class="detail-grid">
            <div class="detail-item">
                <strong>Applicant Name</strong>
                {{ $application->user->fname }} {{ $application->user->lname }}
            </div>
            <div class="detail-item">
                <strong>Email Address</strong>
                {{ $application->user->email }}
            </div>
            <div class="detail-item">
                <strong>Submitted At</strong>
                {{ $application->submitted_at->format('F d, Y h:i A') }}
            </div>
            <div class="detail-item">
                <strong>Last Updated</strong>
                {{ $application->updated_at->format('F d, Y h:i A') }}
            </div>
        </div>

        <h2>Submitted Documents</h2>
        @if($application->documents->isEmpty())
            <p style="color: rgba(255,255,255,0.5);">No documents uploaded.</p>
        @else
            <div class="document-list">
                @foreach($application->documents as $doc)
                    <div class="document-item">
                        <div>
                            <div class="doc-name">{{ $doc->requirement->name ?? 'Unknown Requirement' }}</div>
                            <div class="doc-desc">{{ $doc->requirement->description ?? '' }}</div>
                        </div>
                        <div>
                            @if($doc->file_path)
                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn-dl">
                                    <i data-lucide="download" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle; margin-right: 5px;"></i>
                                    View File
                                </a>
                            @else
                                <span style="color: rgba(255,255,255,0.4);">No file</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if($application->status === 'pending')
            <div class="action-bar">
                <div class="action-box">
                    <form action="{{ route('admin.applications.approve', $application->id) }}" method="POST">
                        @csrf
                        <textarea name="admin_notes" placeholder="Approval notes (optional)"></textarea>
                        <button type="submit" class="btn-approve">Approve Application</button>
                    </form>
                </div>
                <div class="action-box">
                    <form action="{{ route('admin.applications.reject', $application->id) }}" method="POST">
                        @csrf
                        <textarea name="admin_notes" placeholder="Rejection reason (required)" required></textarea>
                        <button type="submit" class="btn-reject">Reject Application</button>
                    </form>
                </div>
            </div>
        @else
            <div style="margin-top: 40px; padding: 20px; background: rgba(0,0,0,0.2); border-radius: 8px;">
                <strong style="color: rgba(255,255,255,0.6); text-transform: uppercase; font-size: 0.85em; display: block; margin-bottom: 5px;">Admin Notes ({{ $application->status }})</strong>
                <p style="white-space: pre-wrap; margin: 0;">{{ $application->admin_notes ?? 'No notes provided.' }}</p>
                <div style="margin-top: 15px; color: rgba(255,255,255,0.5); font-size: 0.85em;">
                    Reviewed at: {{ $application->reviewed_at ? $application->reviewed_at->format('M d, Y h:i A') : 'N/A' }}
                </div>
            </div>
        @endif
    </div>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>
@endpush
