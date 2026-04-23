@extends('layouts.admin')

@section('title', 'Admin - Application Details')

@push('styles')
<style>
/* Premium Dark Mode Glassmorphism Theme */
:root {
    --glass-bg: rgba(25, 30, 45, 0.6);
    --glass-border: rgba(255, 255, 255, 0.08);
    --glass-hover-border: rgba(255, 255, 255, 0.2);
    --neon-blue: #3b82f6;
    --neon-green: #10b981;
    --neon-orange: #f59e0b;
    --neon-red: #ef4444;
}

@keyframes floatIn {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
}

.admin-body {
    padding: 40px 24px;
    background: radial-gradient(circle at top right, rgba(139, 92, 246, 0.05), transparent 40%),
                radial-gradient(circle at bottom left, rgba(59, 130, 246, 0.05), transparent 40%);
    min-height: 100vh;
}

.glass-panel {
    background: var(--glass-bg);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
    animation: floatIn 0.6s ease-out forwards;
    margin: 0 auto;
    max-width: 1200px;
}

.header-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--glass-border);
}

.header-top h1 {
    font-size: 1.8rem;
    font-weight: 800;
    margin: 0;
    background: linear-gradient(135deg, #fff, rgba(255,255,255,0.7));
    -webkit-background-clip: text;
    color: transparent;
    letter-spacing: 0.5px;
}

.badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    letter-spacing: 0.5px;
}
.badge.pending { background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); box-shadow: 0 0 10px rgba(245, 158, 11, 0.2); }
.badge.approved { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); box-shadow: 0 0 10px rgba(16, 185, 129, 0.2); }
.badge.rejected { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); box-shadow: 0 0 10px rgba(239, 68, 68, 0.2); }

.section-title {
    font-size: 0.85rem;
    font-weight: 800;
    color: rgba(255,255,255,0.5);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 20px;
    display: block;
}

.glass-card {
    background: rgba(0, 0, 0, 0.2);
    border: 1px solid var(--glass-border);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 40px;
    transition: all 0.3s;
}
.glass-card:hover {
    border-color: rgba(255,255,255,0.15);
    background: rgba(0,0,0,0.25);
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 25px;
}
.detail-item strong {
    display: block;
    color: rgba(255,255,255,0.5);
    font-size: 0.75rem;
    margin-bottom: 8px;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 0.5px;
}
.detail-item span {
    font-size: 1.05rem;
    color: #fff;
    font-weight: 600;
}

.document-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: rgba(255,255,255,0.02);
    border: 1px solid var(--glass-border);
    border-radius: 12px;
    margin-bottom: 12px;
    transition: all 0.3s;
}
.document-item:hover {
    background: rgba(255,255,255,0.05);
    border-color: rgba(255,255,255,0.15);
}
.doc-name {
    font-weight: 700;
    color: #fff;
    margin-bottom: 6px;
    font-size: 1.05rem;
}
.doc-desc {
    font-size: 0.85rem;
    color: rgba(255,255,255,0.5);
}

.btn-outline {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 10px;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 700;
    color: #3b82f6;
    background: rgba(59, 130, 246, 0.1);
    border: 1px solid rgba(59, 130, 246, 0.3);
    transition: all 0.2s;
}
.btn-outline:hover {
    background: rgba(59, 130, 246, 0.2);
    transform: translateY(-2px);
}

.action-bar {
    display: flex;
    gap: 16px;
    justify-content: flex-end;
    margin-top: 40px;
    padding-top: 30px;
    border-top: 1px solid var(--glass-border);
}

.btn-solid {
    padding: 12px 28px;
    border-radius: 12px;
    border: none;
    font-weight: 800;
    font-size: 0.95rem;
    cursor: pointer;
    color: white;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.btn-approve {
    background: linear-gradient(135deg, #10b981, #059669);
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}
.btn-approve:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4); }
.btn-reject {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
}
.btn-reject:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4); }

.notes-box {
    background: rgba(0,0,0,0.3);
    padding: 24px;
    border-radius: 16px;
    border-left: 4px solid var(--glass-border);
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: rgba(255,255,255,0.6);
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 24px;
    transition: color 0.2s;
}
.back-link:hover { color: #fff; }

/* Modals */
.glass-modal-backdrop {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(8px);
    z-index: 2000;
    display: none;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s;
}
.glass-modal-backdrop.open { display: flex; opacity: 1; }
.glass-modal {
    background: rgba(15, 23, 42, 0.85);
    backdrop-filter: blur(24px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    width: 100%;
    max-width: 500px;
    padding: 30px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    transform: scale(0.95) translateY(20px);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.glass-modal-backdrop.open .glass-modal { transform: scale(1) translateY(0); }

.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.modal-header h2 { font-size: 1.4rem; font-weight: 800; color: #fff; margin: 0; }
.modal-close { background: none; border: none; color: rgba(255,255,255,0.5); cursor: pointer; transition: color 0.2s; }
.modal-close:hover { color: #fff; }

.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 700; color: rgba(255,255,255,0.7); font-size: 0.9rem; }
.form-control {
    width: 100%;
    background: rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.1);
    padding: 14px 16px;
    border-radius: 12px;
    color: #fff;
    font-size: 0.95rem;
    transition: all 0.3s;
    font-family: inherit;
}
.form-control:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2); }
textarea.form-control { resize: vertical; min-height: 100px; }

.modal-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 30px; }
.btn-cancel {
    background: rgba(255,255,255,0.05);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.1);
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-cancel:hover { background: rgba(255,255,255,0.1); }

</style>
@endpush

@section('content')

<main class="dash">
  <div class="admin-body">
    
    <a href="{{ route('admin.applications.index', ['tab' => $tab, 'search' => $search, 'from_date' => $fromDate, 'to_date' => $toDate]) }}" class="back-link">
        <i data-lucide="arrow-left" style="width:16px;"></i> Back to Applications
    </a>

    <div class="glass-panel">
        <div class="header-top">
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <span style="font-size: 0.8rem; font-weight: 800; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 1px;">Doctor Application Review</span>
                <h1>Application #{{ $application->id }}</h1>
            </div>
            <span class="badge {{ $application->status }}">{{ $application->status }}</span>
        </div>

        {{-- === SECTION 1: PERSONAL INFORMATION === --}}
        <span class="section-title">1. Personal Information</span>
        @php
            $bday = $application->user->bday ? \Carbon\Carbon::parse($application->user->bday) : null;
            $age   = $bday ? $bday->age : null;
            $genderMap = ['male' => 'Male', 'female' => 'Female', 'other' => 'Other', 'prefer_not_say' => 'Prefer not to say'];
        @endphp
        <div class="glass-card detail-grid">
            <div class="detail-item"><strong>First Name</strong><span>{{ $application->user->fname ?? '—' }}</span></div>
            <div class="detail-item"><strong>Middle Name</strong><span>{{ $application->user->mname ?? '—' }}</span></div>
            <div class="detail-item"><strong>Last Name</strong><span>{{ $application->user->lname ?? '—' }}</span></div>
            <div class="detail-item"><strong>Gender</strong><span>{{ $genderMap[$application->user->gender] ?? ucfirst($application->user->gender ?? '—') }}</span></div>
            <div class="detail-item"><strong>Birthday</strong><span>{{ $bday ? $bday->format('M d, Y') : '—' }}</span></div>
            <div class="detail-item"><strong>Age</strong><span>{{ $age !== null ? $age . ' years old' : '—' }}</span></div>
        </div>

        {{-- === SECTION 2: ACCOUNT CREDENTIALS === --}}
        <span class="section-title">2. Account Credentials</span>
        <div class="glass-card detail-grid">
            <div class="detail-item"><strong>Username</strong><span>{{ '@' . ($application->user->username ?? '—') }}</span></div>
            <div class="detail-item"><strong>Email Address</strong><span>{{ $application->user->email ?? '—' }}</span></div>
            <div class="detail-item"><strong>Account Role</strong><span style="text-transform: capitalize;">{{ $application->user->role ?? '—' }}</span></div>
            <div class="detail-item"><strong>Doctor Status</strong><span class="badge {{ $application->status }}">{{ ucfirst($application->status) }}</span></div>
        </div>

        {{-- === SECTION 3: PROFESSIONAL INFORMATION === --}}
        <span class="section-title">3. Professional Information</span>
        <div class="glass-card detail-grid">
            <div class="detail-item"><strong>Professional Title</strong><span>{{ $application->professional_titles ?? 'Not specified' }}</span></div>
            <div class="detail-item"><strong>Submitted On</strong><span>{{ $application->submitted_at->format('M d, Y') }}</span></div>
            <div class="detail-item"><strong>Time Submitted</strong><span>{{ $application->submitted_at->format('h:i A') }}</span></div>
        </div>

        {{-- === SECTION 4: BIOMETRIC VERIFICATION === --}}
        <span class="section-title">4. Biometric Verification</span>
        <div class="glass-card detail-grid">
            <div class="detail-item">
                <strong>Biometric Consent</strong>
                @if($application->biometric_consent)
                    <span style="color: #10b981;">✓ Agreed</span>
                @else
                    <span style="color: #ef4444;">✗ Not Given</span>
                @endif
            </div>
            <div class="detail-item">
                <strong>Liveness Verified</strong>
                @if($application->liveness_verified)
                    <span style="color: #10b981;">✓ Verified</span>
                @else
                    <span style="color: #ef4444;">✗ Not Verified</span>
                @endif
            </div>
            <div class="detail-item"><strong>Verified At</strong><span>{{ $application->biometric_verified_at ? \Carbon\Carbon::parse($application->biometric_verified_at)->format('M d, Y h:i A') : '—' }}</span></div>
        </div>

        {{-- === SECTION 5: SUBMITTED DOCUMENTS === --}}
        <span class="section-title">5. Submitted Documents</span>
        @php
            $submittedDocs = $application->documents->keyBy('doctor_requirement_id');
        @endphp

        @if(isset($requirements) && $requirements->isNotEmpty())
            <div class="document-list" style="margin-bottom: 40px;">
                @foreach($requirements as $req)
                    @php $doc = $submittedDocs->get($req->id); @endphp
                    <div class="document-item" style="{{ !$doc && $req->is_required ? 'border-left: 4px solid #ef4444;' : (!$doc ? 'border-left: 4px solid #f59e0b;' : 'border-left: 4px solid #10b981;') }}">
                        <div style="flex: 1; min-width: 200px;">
                            <div class="doc-name">
                                {{ $req->name }}
                                @if($req->is_required)
                                    <span style="background: rgba(239,68,68,0.1); color:#ef4444; padding: 2px 8px; border-radius: 6px; font-size: 0.7rem; font-weight: 800; margin-left: 8px;">Required</span>
                                @else
                                    <span style="background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.5); padding: 2px 8px; border-radius: 6px; font-size: 0.7rem; font-weight: 800; margin-left: 8px;">Optional</span>
                                @endif
                            </div>
                            <div class="doc-desc">{{ $req->description ?? 'No description provided.' }}</div>

                            @if(!$doc)
                                <div style="margin-top: 12px; padding: 10px 16px; background: rgba(0,0,0,0.2); border: 1px solid {{ $req->is_required ? 'rgba(239,68,68,0.3)' : 'rgba(245,158,11,0.3)' }}; border-radius: 8px; font-size: 0.85rem; color: {{ $req->is_required ? '#ef4444' : '#f59e0b' }};">
                                    <i data-lucide="alert-triangle" style="width:14px; margin-right:4px;"></i> 
                                    <strong>Not Submitted</strong> — {{ $req->is_required ? 'This required document is missing.' : 'This optional document was not provided.' }}
                                </div>
                            @else
                                @php
                                    $ext = $doc->file_path ? strtoupper(pathinfo($doc->file_path, PATHINFO_EXTENSION)) : null;
                                    $isVideo = in_array(strtolower($ext ?? ''), ['mp4', 'webm', 'mov', 'avi']);
                                @endphp
                                <div style="margin-top: 12px; display: flex; align-items: center; gap: 8px;">
                                    @if($ext)
                                        <span style="background: rgba(59,130,246,0.15); color:#3b82f6; padding: 4px 10px; border-radius: 6px; font-size:0.75rem; font-weight:800; border: 1px solid rgba(59,130,246,0.3);">
                                            {{ $isVideo ? '🎥' : '📄' }} {{ $ext }}
                                        </span>
                                    @endif
                                    <span style="font-size: 0.85rem; color: rgba(255,255,255,0.5);">{{ basename($doc->file_path ?? '—') }}</span>
                                </div>
                            @endif
                        </div>

                        <div style="display: flex; align-items: center; gap: 16px; flex-shrink: 0; margin-left: 20px;">
                            @if($doc)
                                <span style="font-size: 0.85rem; color: {{ $doc->status === 'accepted' ? '#10b981' : ($doc->status === 'rejected' ? '#ef4444' : '#f59e0b') }}; font-weight: 700; text-transform: uppercase;">
                                    {{ $doc->status ?? 'submitted' }}
                                </span>
                                @if($doc->file_path)
                                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn-outline">
                                        <i data-lucide="external-link" style="width: 14px;"></i> View File
                                    </a>
                                @endif
                            @else
                                <span style="font-size: 0.85rem; color: rgba(255,255,255,0.4); font-style: italic;">No file attached</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p style="color: rgba(255,255,255,0.5); padding: 30px; text-align: center; background: rgba(0,0,0,0.2); border-radius: 12px; font-weight: 600;">No requirement definitions found.</p>
        @endif

        {{-- === REVIEW ACTIONS === --}}
        @if($application->status === 'pending')
            <div class="action-bar">
                <button type="button" class="btn-solid btn-reject" onclick="openRejectModal()">
                    <i data-lucide="x-circle"></i> Reject Application
                </button>
                <button type="button" class="btn-solid btn-approve" onclick="openApproveModal()">
                    <i data-lucide="check-circle"></i> Approve & Verify
                </button>
            </div>
        @else
            <span class="section-title">Review History</span>
            <div class="notes-box" style="border-left-color: {{ $application->status === 'approved' ? '#10b981' : '#ef4444' }};">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                    <strong style="font-size: 1.1rem; color: #fff;">Administrator Feedback</strong>
                    <span style="font-size: 0.85rem; color: rgba(255,255,255,0.5);">Reviewed on {{ $application->reviewed_at ? $application->reviewed_at->format('M d, Y h:i A') : '—' }}</span>
                </div>
                <p style="white-space: pre-wrap; margin: 0; color: rgba(255,255,255,0.8); line-height: 1.6; font-size: 0.95rem;">{{ $application->admin_notes ?? 'No additional notes provided by the administrator.' }}</p>
            </div>
        @endif

    </div>
  </div>
</main>

<!-- Approve Modal -->
<div class="glass-modal-backdrop" id="approveModal">
    <div class="glass-modal" style="border-top: 4px solid #10b981;">
        <div class="modal-header">
            <h2>Approve Application</h2>
            <button class="modal-close" onclick="closeApproveModal()"><i data-lucide="x"></i></button>
        </div>
        <p style="color: rgba(255,255,255,0.7); font-size: 0.95rem; margin-bottom: 24px;">You are about to approve <strong>{{ $application->user->fname }} {{ $application->user->lname }}</strong> as a Verified Doctor. They will gain access to the Doctor Portal.</p>
        <form action="{{ route('admin.applications.approve', $application->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Admin Notes (Optional)</label>
                <textarea name="admin_notes" class="form-control" placeholder="Add approval notes or feedback for the doctor..."></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeApproveModal()">Cancel</button>
                <button type="submit" class="btn-solid btn-approve">Confirm Approval</button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div class="glass-modal-backdrop" id="rejectModal">
    <div class="glass-modal" style="border-top: 4px solid #ef4444;">
        <div class="modal-header">
            <h2>Reject Application</h2>
            <button class="modal-close" onclick="closeRejectModal()"><i data-lucide="x"></i></button>
        </div>
        <p style="color: rgba(255,255,255,0.7); font-size: 0.95rem; margin-bottom: 24px;">You are rejecting the application for <strong>{{ $application->user->fname }} {{ $application->user->lname }}</strong>. Please provide a reason so they can correct it and reapply.</p>
        <form action="{{ route('admin.applications.reject', $application->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Reason for Rejection <span style="color:#ef4444;">*</span></label>
                <textarea name="admin_notes" class="form-control" placeholder="Explain what is missing or invalid..." required></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeRejectModal()">Cancel</button>
                <button type="submit" class="btn-solid btn-reject">Confirm Rejection</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (typeof lucide !== 'undefined') lucide.createIcons();
});

function openApproveModal() { document.getElementById('approveModal').classList.add('open'); }
function closeApproveModal() { document.getElementById('approveModal').classList.remove('open'); }

function openRejectModal() { document.getElementById('rejectModal').classList.add('open'); }
function closeRejectModal() { document.getElementById('rejectModal').classList.remove('open'); }
</script>
@endpush
@endsection
