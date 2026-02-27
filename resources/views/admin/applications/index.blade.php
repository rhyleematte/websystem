@extends('layouts.app')

@section('title', 'Admin - Doctor Applications')

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
.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    padding-bottom: 20px;
}
.admin-header h1 {
    font-size: 2rem;
    margin: 0;
}
.table {
    width: 100%;
    border-collapse: collapse;
}
.table th, .table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}
.table th {
    background: rgba(0, 0, 0, 0.2);
    font-weight: 600;
}
.table tr:hover {
    background: rgba(255, 255, 255, 0.05);
}
.badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
}
.badge.pending {
    background: rgba(243, 156, 18, 0.2);
    color: #f39c12;
    border: 1px solid rgba(243, 156, 18, 0.5);
}
.badge.approved {
    background: rgba(46, 204, 113, 0.2);
    color: #2ecc71;
    border: 1px solid rgba(46, 204, 113, 0.5);
}
.badge.rejected {
    background: rgba(231, 76, 60, 0.2);
    color: #e74c3c;
    border: 1px solid rgba(231, 76, 60, 0.5);
}
.btn-sm {
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.9rem;
    color: white;
    background: rgba(255, 255, 255, 0.1);
    transition: background 0.3s;
}
.btn-sm:hover {
    background: rgba(255, 255, 255, 0.2);
}
</style>
@endpush

@section('content')
<main class="wrap" style="height: auto; min-height: 100vh; padding: 40px 20px;">
    <div class="admin-container">
        <div class="admin-header">
            <h1>Doctor Applications</h1>
        </div>

        @if(session('success'))
            <div style="padding: 15px; background: rgba(46, 204, 113, 0.2); color: #2ecc71; border-radius: 8px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if($applications->isEmpty())
            <p style="text-align: center; color: rgba(255,255,255,0.6); margin-top: 40px;">No doctor applications found.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($applications as $app)
                        <tr>
                            <td>#{{ $app->id }}</td>
                            <td>{{ $app->user->fname }} {{ $app->user->lname }}</td>
                            <td>{{ $app->user->email }}</td>
                            <td>
                                <span class="badge {{ $app->status }}">
                                    {{ $app->status }}
                                </span>
                            </td>
                            <td>{{ $app->submitted_at->format('M d, Y h:i A') }}</td>
                            <td>
                                <a href="{{ route('admin.applications.show', $app->id) }}" class="btn-sm">View Details</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</main>
@endsection
