<?php $__env->startSection('title', 'Admin - Daily Affirmations'); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* Premium Dark Mode Glassmorphism Theme */
:root {
    --glass-bg: rgba(25, 30, 45, 0.6);
    --glass-border: rgba(255, 255, 255, 0.08);
    --glass-hover-border: rgba(255, 255, 255, 0.2);
    --neon-blue: #3b82f6;
    --neon-green: #10b981;
    --neon-orange: #f59e0b;
    --neon-purple: #8b5cf6;
    --neon-pink: #ec4899;
}

@keyframes  floatIn {
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

.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--glass-border);
}

.header-title h1 {
    font-size: 1.8rem;
    font-weight: 800;
    margin: 0 0 8px;
    background: linear-gradient(135deg, #fff, rgba(255,255,255,0.7));
    -webkit-background-clip: text;
    color: transparent;
    letter-spacing: 0.5px;
}

.header-title p {
    margin: 0;
    color: rgba(255,255,255,0.5);
    font-size: 0.95rem;
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
.btn-action.edit:hover { background: rgba(59, 130, 246, 0.15); color: #3b82f6; border-color: rgba(59, 130, 246, 0.3); }
.btn-action.delete:hover { background: rgba(239, 68, 68, 0.15); color: #ef4444; border-color: rgba(239, 68, 68, 0.3); }
.btn-action.post:hover { background: rgba(16, 185, 129, 0.15); color: #10b981; border-color: rgba(16, 185, 129, 0.3); }

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

/* Glass Card for Current Live Affirmation */
.live-affirmation-card {
    background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(59, 130, 246, 0.05));
    border: 1px solid rgba(139, 92, 246, 0.2);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
}
.live-affirmation-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; width: 4px; height: 100%;
    background: linear-gradient(to bottom, #8b5cf6, #3b82f6);
}
.live-affirmation-card h3 {
    margin: 0 0 15px;
    font-size: 1.1rem;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 8px;
}
.affirmation-quote {
    font-size: 1.2rem;
    line-height: 1.6;
    font-style: italic;
    color: rgba(255,255,255,0.9);
    margin-bottom: 12px;
}
.affirmation-meta {
    font-size: 0.9rem;
    color: rgba(255,255,255,0.5);
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

/* Status Pills */
.status-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.status-pill.live { color: #10b981; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); }
.status-pill.scheduled { color: #f59e0b; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); }
.status-pill.draft { color: #94a3b8; background: rgba(148, 163, 184, 0.15); border: 1px solid rgba(148, 163, 184, 0.3); }
.status-pill.offline { color: #64748b; background: rgba(100, 116, 139, 0.15); border: 1px solid rgba(100, 116, 139, 0.3); }

/* Glass Table */
.glass-table-wrap {
    overflow-x: auto;
    border-radius: 16px;
    border: 1px solid var(--glass-border);
    background: rgba(0,0,0,0.2);
}
table { width: 100%; border-collapse: collapse; }
th, td { padding: 16px 24px; text-align: left; border-bottom: 1px solid var(--glass-border); }
th { background: rgba(255,255,255,0.02); font-weight: 700; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem; }
td { color: #fff; font-size: 0.95rem; vertical-align: middle; }
tr:last-child td { border-bottom: none; }
tr:hover td { background: rgba(255,255,255,0.03); }

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
    max-width: 600px;
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
    color-scheme: dark;
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
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<main class="dash">
  <div class="admin-body">
      <div class="glass-panel">
        
        <div class="admin-header">
            <div class="header-title">
                <h1>Daily Affirmations</h1>
                <p>Create, schedule, edit, and post the quote shown in the Daily Affirmation panel across the app.</p>
            </div>
            <button class="btn-primary" onclick="openCreateModal()">
                <i data-lucide="plus"></i> Add Affirmation
            </button>
        </div>

        <?php if(session('success')): ?>
            <div class="alert-success">
                <i data-lucide="check-circle"></i> <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="alert-error">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div><?php echo e($error); ?></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        
        <div class="live-affirmation-card">
            <h3><i data-lucide="sparkles" style="color: #a855f7;"></i> Current Live Affirmation</h3>
            <?php if($currentAffirmation): ?>
                <div class="affirmation-quote">"<?php echo e($currentAffirmation->quote); ?>"</div>
                <div class="affirmation-meta">
                    <span class="status-pill live"><?php echo e($currentAffirmation->display_status); ?></span>
                    <?php if($currentAffirmation->author): ?>
                        <span><i data-lucide="user" style="width:14px; margin-right:4px;"></i><?php echo e($currentAffirmation->author); ?></span>
                    <?php endif; ?>
                    <?php if($currentAffirmation->publish_at): ?>
                        <span><i data-lucide="clock" style="width:14px; margin-right:4px;"></i>Posted: <?php echo e($currentAffirmation->publish_at->format('M d, Y g:i A')); ?></span>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="affirmation-quote">"You are worthy of support and belonging. Your journey is unique, and every step forward is progress."</div>
                <div class="affirmation-meta">
                    <span class="status-pill draft">Fallback</span>
                    <span>No published affirmation yet. The app is using the default fallback quote.</span>
                </div>
            <?php endif; ?>
        </div>

        <div class="glass-table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Quote</th>
                  <th>Status</th>
                  <th>Schedule</th>
                  <th>Created By</th>
                  <th style="text-align: right;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $affirmations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $affirmation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  <?php
                    $isCurrent = $currentAffirmation && $currentAffirmation->id === $affirmation->id;
                    $state = strtolower($affirmation->display_status);
                    
                    $editState = $state;
                    if ($editState === 'live' || $editState === 'offline') {
                        $editState = 'publish_now';
                    }

                    $editPayload = [
                      'id' => $affirmation->id,
                      'quote' => $affirmation->quote,
                      'author' => $affirmation->author,
                      'publish_state' => $editState,
                      'scheduled_for' => $affirmation->publish_at ? $affirmation->publish_at->format('Y-m-d\\TH:i') : null,
                    ];
                  ?>
                  <tr>
                    <td style="max-width: 300px;">
                      <div style="line-height: 1.5; margin-bottom: 4px;">"<?php echo e(Str::limit($affirmation->quote, 80)); ?>"</div>
                      <?php if($affirmation->author): ?>
                        <div style="color: rgba(255,255,255,0.4); font-size: 0.85rem;">- <?php echo e($affirmation->author); ?></div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <span class="status-pill <?php echo e($state); ?>"><?php echo e($affirmation->display_status); ?></span>
                      <?php if($isCurrent): ?>
                        <div style="margin-top: 8px; color: #10b981; font-size: 0.8rem; font-weight: 700; display:flex; align-items:center; gap:4px;">
                          <i data-lucide="sparkles" style="width:12px;"></i> Currently shown
                        </div>
                      <?php endif; ?>
                    </td>
                    <td style="color: rgba(255,255,255,0.6); font-size: 0.85rem;">
                      <?php if($affirmation->publish_at): ?>
                        <?php echo e($affirmation->publish_at->format('M d, Y g:i A')); ?>

                      <?php else: ?>
                        Not scheduled
                      <?php endif; ?>
                    </td>
                    <td style="color: rgba(255,255,255,0.6);"><?php echo e(optional($affirmation->creator)->short_name ?? 'Admin'); ?></td>
                    <td>
                      <div style="display:flex; justify-content: flex-end; gap: 8px; align-items: center;">
                        <?php if($state === 'scheduled'): ?>
                        <form action="<?php echo e(route('admin.daily-affirmations.publish-now', $affirmation)); ?>" method="POST" style="margin:0;">
                          <?php echo csrf_field(); ?>
                          <button type="submit" class="btn-action post"><i data-lucide="send" style="width:14px;"></i> Post Now</button>
                        </form>
                        <?php endif; ?>

                        <button type="button" class="btn-action edit" data-edit='<?php echo json_encode($editPayload, 15, 512) ?>' onclick="openEditModal(this)">
                          <i data-lucide="edit-2" style="width:14px;"></i> Edit
                        </button>

                        <form action="<?php echo e(route('admin.daily-affirmations.destroy', $affirmation)); ?>" method="POST" onsubmit="return confirm('Delete this affirmation?');" style="margin:0;">
                          <?php echo csrf_field(); ?>
                          <?php echo method_field('DELETE'); ?>
                          <button type="submit" class="btn-action delete"><i data-lucide="trash-2" style="width:14px;"></i> Delete</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                  <tr>
                    <td colspan="5" style="text-align:center; padding: 40px; color: rgba(255,255,255,0.4); font-weight: 600;">No daily affirmations added yet.</td>
                  </tr>
                <?php endif; ?>
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
      <h2>Add New Affirmation</h2>
      <button class="modal-close" type="button" onclick="closeCreateModal()"><i data-lucide="x"></i></button>
    </div>
    <form action="<?php echo e(route('admin.daily-affirmations.store')); ?>" method="POST">
      <?php echo csrf_field(); ?>
      <div class="form-group">
        <label for="quote">Quote</label>
        <textarea name="quote" id="quote" class="form-control" required placeholder="Write the affirmation quote here..."></textarea>
      </div>
      <div class="form-group">
        <label for="author">Author or Source (Optional)</label>
        <input type="text" name="author" id="author" class="form-control" placeholder="e.g. Maya Angelou">
      </div>
      <div class="form-group">
        <label for="publish_state">Posting Option</label>
        <select name="publish_state" id="publish_state" class="form-control" onchange="toggleScheduleField(this, 'createScheduleWrap')">
          <option value="draft">Save as Draft</option>
          <option value="publish_now">Post Today / Post Now</option>
          <option value="scheduled">Schedule for Specific Day and Time</option>
        </select>
      </div>
      <div class="form-group" id="createScheduleWrap" style="display: none;">
        <label for="scheduled_for">Schedule Date and Time <span style="font-weight:normal;color:rgba(255,255,255,0.4);">(in Philippine Time / PHT)</span></label>
        <input type="datetime-local" name="scheduled_for" id="scheduled_for" class="form-control">
      </div>
      <div class="modal-actions">
        <button type="button" class="btn-cancel" onclick="closeCreateModal()">Cancel</button>
        <button type="submit" class="btn-primary">Save Affirmation</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="glass-modal-backdrop" id="editModal">
  <div class="glass-modal">
    <div class="modal-header">
      <h2>Edit Affirmation</h2>
      <button class="modal-close" type="button" onclick="closeEditModal()"><i data-lucide="x"></i></button>
    </div>
    <form id="editForm" method="POST">
      <?php echo csrf_field(); ?>
      <?php echo method_field('PUT'); ?>
      <div class="form-group">
        <label for="edit_quote">Quote</label>
        <textarea name="quote" id="edit_quote" class="form-control" required></textarea>
      </div>
      <div class="form-group">
        <label for="edit_author">Author or Source (Optional)</label>
        <input type="text" name="author" id="edit_author" class="form-control">
      </div>
      <div class="form-group">
        <label for="edit_publish_state">Posting Option</label>
        <select name="publish_state" id="edit_publish_state" class="form-control" onchange="toggleScheduleField(this, 'editScheduleWrap')">
          <option value="draft">Save as Draft</option>
          <option value="publish_now">Post Today / Post Now</option>
          <option value="scheduled">Schedule for Specific Day and Time</option>
        </select>
      </div>
      <div class="form-group" id="editScheduleWrap" style="display:none;">
        <label for="edit_scheduled_for">Schedule Date and Time</label>
        <input type="datetime-local" name="scheduled_for" id="edit_scheduled_for" class="form-control">
      </div>
      <div class="modal-actions">
        <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
        <button type="submit" class="btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (typeof lucide !== 'undefined') lucide.createIcons();
});

function toggleScheduleField(select, wrapId) {
    const wrap = document.getElementById(wrapId);
    if (!wrap) return;
    wrap.style.display = select.value === 'scheduled' ? 'block' : 'none';
}

function openCreateModal() {
    document.getElementById('createModal').classList.add('open');
}
function closeCreateModal() {
    document.getElementById('createModal').classList.remove('open');
}

function openEditModal(button) {
    const payload = JSON.parse(button.dataset.edit || '{}');
    const modal = document.getElementById('editModal');
    const form = document.getElementById('editForm');

    if (!payload.id || !modal || !form) return;

    form.action = '/admin/daily-affirmations/' + payload.id;
    document.getElementById('edit_quote').value = payload.quote || '';
    document.getElementById('edit_author').value = payload.author || '';
    document.getElementById('edit_publish_state').value = payload.publish_state || 'draft';
    document.getElementById('edit_scheduled_for').value = payload.scheduled_for || '';
    toggleScheduleField(document.getElementById('edit_publish_state'), 'editScheduleWrap');
    
    modal.classList.add('open');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('open');
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\websystem\resources\views/admin/daily_affirmations/index.blade.php ENDPATH**/ ?>