@extends('layouts.dashboard')

@section('title', 'Admin - AI Guidelines')

@push('styles')
<style>
.admin-container {
    width: 100%;
    margin: 0 auto;
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 8px;
    color: var(--text);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}
.admin-header {
    padding: 25px 30px;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.admin-header h1 {
    font-size: 1.6rem;
    font-weight: 600;
    margin: 0;
    color: var(--text);
}
.admin-body {
    padding: 24px;
    max-width: 1000px;
    margin: 0 auto;
    width: 100%;
}

.table-responsive {
    overflow-x: auto;
    padding: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table th, table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid var(--border);
}

table th {
    background-color: var(--input-bg);
    font-weight: 600;
}

.btn-primary {
    background: #3b82f6;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
    text-decoration: none;
    display: inline-block;
}
.btn-primary:hover {
    background: #2563eb;
}

.btn-danger {
    background: transparent;
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-danger:hover {
    background: rgba(239, 68, 68, 0.05);
}

.alert-success {
    padding: 15px;
    background: rgba(46, 204, 113, 0.1);
    color: #2ecc71;
    border: 1px solid rgba(46, 204, 113, 0.3);
    border-radius: 8px;
    margin: 20px;
}
.alert-error {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.4);
    color: #fca5a5;
    padding: 1rem 1.25rem;
    border-radius: 8px;
    margin: 20px;
    font-size: 0.9rem;
}

.upload-card {
    background: var(--input-bg);
    border: 1px dashed var(--border);
    border-radius: 8px;
    padding: 20px;
    margin: 20px;
}

.form-group {
    margin-bottom: 15px;
}
.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
}
.form-control {
    width: 100%;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid var(--border);
    background: var(--panel);
    color: var(--text);
}
</style>
@endpush

@section('content')

<main class="dash">

  <div class="admin-body">
    <section class="admin-main">
      <div class="admin-container">
        
        <div class="admin-header">
            <h1>AI Knowledge Base Guidelines</h1>
        </div>

        @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert-error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
            </div>
        @endif

        <div class="upload-card">
            <h3>Upload New Guideline Document</h3>
            <p style="font-size: 0.9rem; margin-bottom: 15px; color: var(--text-muted);">
                Supported formats: PDF, TXT. These documents will be parsed and injected into the AI's knowledge base context.
            </p>
            <form action="{{ route('admin.guidelines.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="title">Title / Description</label>
                    <input type="text" name="title" id="title" class="form-control" required placeholder="e.g. WHO Mental Health Action Plan 2023">
                </div>
                <div class="form-group">
                    <label for="document">Document File</label>
                    <input type="file" name="document" id="document" class="form-control" accept=".pdf,.txt" required>
                </div>
                <button type="submit" class="btn-primary">Upload & Process</button>
            </form>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Original File</th>
                        <th>Status</th>
                        <th>Uploaded On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guidelines as $guideline)
                        <tr>
                            <td>{{ $guideline->title }}</td>
                            <td>{{ $guideline->original_filename }}</td>
                            <td>
                                @if($guideline->is_parsed)
                                    <span style="color: #2ecc71; font-weight: 600;">Processed</span>
                                @else
                                    <span style="color: #eab308; font-weight: 600;">Pending</span>
                                @endif
                            </td>
                            <td>{{ $guideline->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                <form action="{{ route('admin.guidelines.destroy', $guideline->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this guideline? This will also remove its content from the AI knowledge base.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 20px;">No guidelines uploaded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

      </div>
    </section>
  </div>

</main>

@endsection
