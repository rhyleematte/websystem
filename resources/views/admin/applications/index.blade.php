@extends('layouts.admin')

@section('title', 'Admin - Doctor Applications')

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

.admin-header h1 {
    font-size: 1.8rem;
    font-weight: 800;
    margin: 0 0 20px;
    background: linear-gradient(135deg, #fff, rgba(255,255,255,0.7));
    -webkit-background-clip: text;
    color: transparent;
    letter-spacing: 0.5px;
}

.glass-widget {
    background: var(--glass-bg);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    padding: 24px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
    animation: floatIn 0.6s ease-out forwards;
    margin-bottom: 24px;
}

.admin-filters form {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    align-items: flex-end;
}

.admin-search-box {
    flex: 1;
    min-width: 300px;
    position: relative;
}
.admin-search-box i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255,255,255,0.4);
    width: 20px;
    height: 20px;
    pointer-events: none;
    transition: color 0.3s;
}
.admin-search-box input {
    width: 100%;
    padding: 14px 16px 14px 46px;
    border-radius: 12px;
    border: 1px solid var(--glass-border);
    background: rgba(0, 0, 0, 0.2);
    color: #fff;
    font-size: 0.95rem;
    transition: all 0.3s;
}
.admin-search-box input:focus {
    border-color: var(--neon-blue);
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}
.admin-search-box input:focus + i {
    color: var(--neon-blue);
}

.admin-date-filters {
    display: flex;
    gap: 16px;
}
.date-input-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.date-input-group label {
    font-size: 0.75rem;
    font-weight: 800;
    color: rgba(255,255,255,0.5);
    text-transform: uppercase;
    letter-spacing: 1px;
}
.date-input-group input[type="date"] {
    background: rgba(0, 0, 0, 0.2);
    border: 1px solid var(--glass-border);
    padding: 12px 16px;
    border-radius: 12px;
    color: #fff;
    font-size: 0.95rem;
    font-family: inherit;
    color-scheme: dark;
    transition: all 0.3s;
}
.date-input-group input[type="date"]:focus {
    border-color: var(--neon-blue);
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}

.admin-tabs {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 24px;
}
.admin-tabs a {
    text-decoration: none;
    color: rgba(255,255,255,0.6);
    font-weight: 600;
    padding: 10px 20px;
    border-radius: 12px;
    font-size: 0.9rem;
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--glass-border);
    transition: all 0.3s;
}
.admin-tabs a:hover {
    background: rgba(255,255,255,0.08);
    color: #fff;
}
.admin-tabs a.active {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(139, 92, 246, 0.15));
    color: #3b82f6;
    border-color: rgba(59, 130, 246, 0.3);
    box-shadow: 0 0 15px rgba(59, 130, 246, 0.1);
}

.glass-table-wrap {
    overflow-x: auto;
    border-radius: 16px;
}
table { width: 100%; border-collapse: collapse; }
th, td { padding: 18px 24px; text-align: left; border-bottom: 1px solid var(--glass-border); }
th { background: rgba(255,255,255,0.02); font-weight: 700; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem; }
td { color: #fff; font-size: 0.95rem; vertical-align: middle; }
tr:last-child td { border-bottom: none; }
tr { transition: background 0.2s, transform 0.2s; }
tr:hover { background: rgba(255,255,255,0.03); }

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

.btn-sm {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 10px;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 700;
    color: #fff;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    transition: all 0.3s;
}
.btn-sm:hover {
    background: rgba(59, 130, 246, 0.15);
    border-color: rgba(59, 130, 246, 0.3);
    color: #3b82f6;
    transform: translateY(-2px);
}

.table-footer {
    padding: 24px;
    color: rgba(255,255,255,0.5);
    font-size: 0.9rem;
    font-weight: 600;
}
</style>
@endpush

@section('content')

<main class="dash">

  <div class="admin-body">
    <div class="admin-header">
        <h1>Doctor Applications Overview</h1>
    </div>

    {{-- Filters Widget --}}
    <div class="glass-widget admin-filters" style="animation-delay: 0.1s;">
        <form action="{{ route('admin.applications.index') }}" method="GET">
            <input type="hidden" name="tab" value="{{ $tab }}">
            
            <div class="admin-search-box">
                <i data-lucide="search"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search by name or email..." autocomplete="off">
            </div>

            <div class="admin-date-filters">
                <div class="date-input-group">
                    <label>From Date</label>
                    <input type="date" name="from_date" value="{{ $fromDate ?? '' }}" onchange="this.form.submit()">
                </div>
                <div class="date-input-group">
                    <label>To Date</label>
                    <input type="date" name="to_date" value="{{ $toDate ?? '' }}" onchange="this.form.submit()">
                </div>
            </div>
        </form>
    </div>

    {{-- Tabs --}}
    <div class="admin-tabs" style="animation-delay: 0.2s;">
        <a href="{{ route('admin.applications.index', ['tab' => 'all', 'search' => $search, 'from_date' => $fromDate, 'to_date' => $toDate]) }}" class="{{ $tab === 'all' ? 'active' : '' }}">
            All {{ $counts['all'] > 0 ? '('.$counts['all'].')' : '' }}
        </a>
        <a href="{{ route('admin.applications.index', ['tab' => 'pending', 'search' => $search, 'from_date' => $fromDate, 'to_date' => $toDate]) }}" class="{{ $tab === 'pending' ? 'active' : '' }}">
            Pending {{ $counts['pending'] > 0 ? '('.$counts['pending'].')' : '' }}
        </a>
        <a href="{{ route('admin.applications.index', ['tab' => 'approved', 'search' => $search, 'from_date' => $fromDate, 'to_date' => $toDate]) }}" class="{{ $tab === 'approved' ? 'active' : '' }}">
            Approved {{ $counts['approved'] > 0 ? '('.$counts['approved'].')' : '' }}
        </a>
        <a href="{{ route('admin.applications.index', ['tab' => 'rejected', 'search' => $search, 'from_date' => $fromDate, 'to_date' => $toDate]) }}" class="{{ $tab === 'rejected' ? 'active' : '' }}">
            Rejected {{ $counts['rejected'] > 0 ? '('.$counts['rejected'].')' : '' }}
        </a>
    </div>

    @if(session('success'))
        <div style="padding: 16px 20px; background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 12px; margin-bottom: 24px; font-weight: 600;">
            <i data-lucide="check-circle" style="width:18px; margin-right:8px; vertical-align:middle;"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Data Table Widget --}}
    <div class="glass-widget" style="padding: 0; animation-delay: 0.3s;">
        @if($applications->isEmpty())
            <div style="text-align: center; color: rgba(255,255,255,0.4); padding: 60px 20px; font-weight: 600;">
                <i data-lucide="folder-open" style="width: 48px; height: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                <p>No doctor applications found.</p>
            </div>
        @else
            <div class="glass-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Submitted At</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications as $app)
                            <tr>
                                <td style="font-weight: 800; color: rgba(255,255,255,0.5);">#{{ $app->id }}</td>
                                <td style="font-weight: 700;">{{ $app->user->fname }} {{ $app->user->lname }}</td>
                                <td style="color: rgba(255,255,255,0.6);">{{ $app->user->email }}</td>
                                <td>
                                    <span class="badge {{ strtolower($app->status) }}">
                                        {{ $app->status }}
                                    </span>
                                </td>
                                <td>
                                    <div style="color: rgba(255,255,255,0.8); font-size: 0.9rem;">{{ $app->submitted_at->format('M d, Y') }}</div>
                                    <div style="color: rgba(255,255,255,0.4); font-size: 0.8rem;">{{ $app->submitted_at->format('h:i A') }}</div>
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('admin.applications.show', ['id' => $app->id, 'tab' => $tab, 'search' => $search, 'from_date' => $fromDate, 'to_date' => $toDate]) }}" class="btn-sm">
                                        <i data-lucide="eye"></i> View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="table-footer">
                Showing {{ $applications->count() }} applications
            </div>
        @endif
    </div>
  </div>

</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (typeof lucide !== 'undefined') lucide.createIcons();

    // Debounced Search Submission
    const searchInput = document.querySelector('input[name="search"]');
    const filterForm = searchInput ? searchInput.closest('form') : null;
    let searchTimeout;

    if (searchInput && filterForm) {
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterForm.submit();
            }, 500); 
        });

        if (searchInput.value) {
            searchInput.focus();
            searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
        }
    }
});
</script>
@endpush
@endsection
