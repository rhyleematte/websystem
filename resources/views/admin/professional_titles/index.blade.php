@extends('layouts.admin')

@section('title', 'Admin - Professional Titles')

@push('styles')
<style>
/* Premium Dark Mode Glassmorphism Theme */
:root {
    --glass-bg: rgba(25, 30, 45, 0.6);
    --glass-border: rgba(255, 255, 255, 0.08);
    --glass-hover-border: rgba(255, 255, 255, 0.2);
    --neon-blue: #3b82f6;
    --neon-green: #10b981;
    --neon-purple: #8b5cf6;
    --neon-pink: #ec4899;
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
    max-width: 1000px;
}

.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--glass-border);
}

.admin-header h1 {
    font-size: 1.8rem;
    font-weight: 800;
    margin: 0;
    background: linear-gradient(135deg, #fff, rgba(255,255,255,0.7));
    -webkit-background-clip: text;
    color: transparent;
    letter-spacing: 0.5px;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 800;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary:hover { 
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(139, 92, 246, 0.5);
}

.btn-action {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.8);
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-action.edit:hover {
    background: rgba(59, 130, 246, 0.15);
    color: #3b82f6;
    border-color: rgba(59, 130, 246, 0.3);
}

.btn-action.delete:hover {
    background: rgba(239, 68, 68, 0.15);
    color: #ef4444;
    border-color: rgba(239, 68, 68, 0.3);
}

.alert-success {
    padding: 16px 20px;
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.2);
    border-radius: 12px;
    margin-bottom: 24px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-error {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.2);
    color: #ef4444;
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 24px;
    font-weight: 600;
}

.glass-table-wrap {
    overflow-x: auto;
    border-radius: 16px;
    border: 1px solid var(--glass-border);
    background: rgba(0,0,0,0.2);
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 16px 24px;
    text-align: left;
    border-bottom: 1px solid var(--glass-border);
}

th {
    background: rgba(255,255,255,0.02);
    font-weight: 700;
    color: rgba(255,255,255,0.5);
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 0.8rem;
}

td {
    color: #fff;
    font-size: 0.95rem;
}

tr:last-child td { border-bottom: none; }
tr:hover td { background: rgba(255,255,255,0.03); }

.title-badge {
    background: linear-gradient(135deg, rgba(139, 92, 246, 0.15), rgba(236, 72, 153, 0.15));
    color: #d946ef;
    padding: 6px 14px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.85rem;
    border: 1px solid rgba(217, 70, 239, 0.3);
    display: inline-block;
}

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

.glass-modal-backdrop.open {
    display: flex;
    opacity: 1;
}

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

.glass-modal-backdrop.open .glass-modal {
    transform: scale(1) translateY(0);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.modal-header h2 {
    font-size: 1.4rem;
    font-weight: 800;
    color: #fff;
    margin: 0;
}

.modal-close {
    background: none;
    border: none;
    color: rgba(255,255,255,0.5);
    cursor: pointer;
    transition: color 0.2s;
}
.modal-close:hover { color: #fff; }

.form-group { margin-bottom: 20px; }
.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 700;
    color: rgba(255,255,255,0.7);
    font-size: 0.9rem;
}
.form-control {
    width: 100%;
    background: rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.1);
    padding: 14px 16px;
    border-radius: 12px;
    color: #fff;
    font-size: 0.95rem;
    transition: all 0.3s;
}
.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 30px;
}

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
      <div class="glass-panel">
        
        <div class="admin-header">
            <h1>Professional Titles</h1>
            <button class="btn-primary" onclick="openCreateModal()">
                <i data-lucide="plus"></i> Add New Title
            </button>
        </div>

        @if(session('success'))
            <div class="alert-success">
                <i data-lucide="check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert-error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
            </div>
        @endif

        <div class="glass-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Title Name</th>
                        <th>Created On</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($titles as $title)
                        <tr>
                            <td><span class="title-badge">{{ $title->name }}</span></td>
                            <td style="color: rgba(255,255,255,0.6);">{{ $title->created_at->format('M d, Y') }}</td>
                            <td>
                                <div style="display:flex; gap:10px; justify-content: flex-end;">
                                    <button type="button" class="btn-action edit" onclick="openEditModal({{ $title->id }}, '{{ addslashes($title->name) }}')">
                                        <i data-lucide="edit-2" style="width:14px;"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.professional-titles.destroy', $title->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this title?');" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action delete">
                                            <i data-lucide="trash-2" style="width:14px;"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 40px; color: rgba(255,255,255,0.4); font-weight: 600;">No professional titles added yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

      </div>
  </div>
</main>

<!-- Create Modal -->
<div class="glass-modal-backdrop" id="createModal">
    <div class="glass-modal">
        <div class="modal-header">
            <h2>Add Professional Title</h2>
            <button class="modal-close" onclick="closeCreateModal()"><i data-lucide="x"></i></button>
        </div>
        <form action="{{ route('admin.professional-titles.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Title Name</label>
                <input type="text" name="name" id="name" class="form-control" required placeholder="e.g. Clinical Psychologist">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeCreateModal()">Cancel</button>
                <button type="submit" class="btn-primary">Add Title</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="glass-modal-backdrop" id="editModal">
    <div class="glass-modal">
        <div class="modal-header">
            <h2>Edit Professional Title</h2>
            <button class="modal-close" onclick="closeEditModal()"><i data-lucide="x"></i></button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="editNameInput">Title Name</label>
                <input type="text" name="name" id="editNameInput" class="form-control" required>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    if (typeof lucide !== 'undefined') lucide.createIcons();
});

function openCreateModal() {
    document.getElementById('createModal').classList.add('open');
}
function closeCreateModal() {
    document.getElementById('createModal').classList.remove('open');
}

function openEditModal(id, currentName) {
    let form = document.getElementById('editForm');
    form.action = `/admin/professional-titles/${id}`;
    document.getElementById('editNameInput').value = currentName;
    document.getElementById('editModal').classList.add('open');
}
function closeEditModal() {
    document.getElementById('editModal').classList.remove('open');
}
</script>

@endsection
