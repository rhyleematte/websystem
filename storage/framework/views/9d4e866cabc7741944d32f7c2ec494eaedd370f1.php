<?php $__env->startSection('title', 'Admin - Platform Analytics'); ?>

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

@keyframes  growWidth {
    0% { width: 0; }
}

.admin-body {
    padding: 40px 24px;
    /* Optional: add a subtle ambient background glow for the whole page */
    background: radial-gradient(circle at top right, rgba(139, 92, 246, 0.05), transparent 40%),
                radial-gradient(circle at bottom left, rgba(59, 130, 246, 0.05), transparent 40%);
    min-height: 100vh;
}

.analytics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
}

.glass-card {
    background: var(--glass-bg);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    padding: 24px;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
    position: relative;
    overflow: hidden;
}

.glass-card:hover {
    transform: translateY(-6px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    border-color: var(--glass-hover-border);
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 20px;
    animation: floatIn 0.6s ease-out forwards;
    opacity: 0;
}
.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }

/* Glowing Icon Containers */
.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    position: relative;
    z-index: 1;
}
.stat-icon::before {
    content: '';
    position: absolute;
    inset: -2px;
    border-radius: 18px;
    background: inherit;
    filter: blur(12px);
    opacity: 0.5;
    z-index: -1;
    transition: opacity 0.3s;
}
.glass-card:hover .stat-icon::before { opacity: 0.8; }

.stat-icon i { width: 30px; height: 30px; color: #fff; }

.stat-info { display: flex; flex-direction: column; z-index: 2; }
.stat-value {
    font-size: 2.2rem;
    font-weight: 900;
    color: #fff;
    line-height: 1.1;
    text-shadow: 0 2px 10px rgba(0,0,0,0.2);
}
.stat-label {
    font-size: 0.85rem;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.6);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-top: 4px;
}

/* Gradients for KPI Icons */
.stat-total .stat-icon { background: linear-gradient(135deg, #3b82f6, #2dd4bf); }
.stat-approved .stat-icon { background: linear-gradient(135deg, #10b981, #a3e635); }
.stat-pending .stat-icon { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
.stat-psych .stat-icon { background: linear-gradient(135deg, #8b5cf6, #ec4899); }

.analytics-sections {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 30px;
    animation: floatIn 0.8s ease-out 0.5s forwards;
    opacity: 0;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--glass-border);
}
.card-title {
    font-size: 1.25rem;
    font-weight: 800;
    background: linear-gradient(to right, #fff, rgba(255,255,255,0.7));
    -webkit-background-clip: text;
    color: transparent;
    letter-spacing: 0.5px;
}

.data-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 8px;
}
.data-label { 
    font-weight: 600; 
    font-size: 0.95rem; 
    color: rgba(255,255,255,0.9);
}
.data-value { 
    font-weight: 800; 
    font-size: 1.1rem;
    color: #fff; 
}

.filter-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    padding: 24px 30px;
    flex-wrap: wrap;
    gap: 20px;
    animation: floatIn 0.5s ease-out forwards;
}

.filter-title {
    display: flex;
    align-items: center;
    gap: 15px;
    font-weight: 900;
    font-size: 1.5rem;
    color: #fff;
    text-shadow: 0 0 20px rgba(255,255,255,0.1);
}

.filter-form {
    display: flex;
    gap: 15px;
    align-items: flex-end;
}
.input-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.input-group label {
    font-size: 0.75rem;
    font-weight: 800;
    color: rgba(255,255,255,0.5);
    text-transform: uppercase;
    letter-spacing: 1px;
}
.input-group input {
    background: rgba(0, 0, 0, 0.2);
    border: 1px solid var(--glass-border);
    padding: 12px 16px;
    border-radius: 12px;
    color: #fff;
    font-size: 0.95rem;
    transition: all 0.3s;
    color-scheme: dark;
}
.input-group input:focus {
    outline: none;
    border-color: var(--neon-blue);
    box-shadow: 0 0 15px rgba(59, 130, 246, 0.3);
}

.btn-filter {
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
}
.btn-filter:hover { 
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(139, 92, 246, 0.5);
}

.progress-wrap { margin-bottom: 24px; }
.progress-bar-container {
    width: 100%;
    height: 10px;
    background: rgba(0, 0, 0, 0.3);
    border-radius: 5px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.05);
}
.progress-bar {
    height: 100%;
    border-radius: 5px;
    position: relative;
    /* Dynamic width will be set inline, but we animate it from 0 */
    animation: growWidth 1.5s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
}

/* Make progress bars glow */
.progress-bar::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.3) 50%, rgba(255,255,255,0) 100%);
    animation: shimmer 2s infinite linear;
}
@keyframes  shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.clear-btn {
    color: rgba(255,255,255,0.5);
    text-decoration: none;
    padding: 12px;
    font-size: 0.9rem;
    font-weight: 600;
    transition: color 0.2s;
}
.clear-btn:hover { color: #fff; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<main class="dash">
    <div class="admin-body">
        
        
        <div class="glass-card filter-bar">
            <div class="filter-title">
                <div style="padding: 10px; background: rgba(59, 130, 246, 0.2); border-radius: 12px; display: flex;">
                    <i data-lucide="bar-chart-3" style="color: #3b82f6; width: 28px; height: 28px;"></i>
                </div>
                Platform Analytics
            </div>
            <form action="<?php echo e(route('admin.analytics')); ?>" method="GET" class="filter-form">
                <div class="input-group">
                    <label>From Date</label>
                    <input type="date" name="from_date" value="<?php echo e($fromDate ?? ''); ?>" onchange="this.form.submit()">
                </div>
                <div class="input-group">
                    <label>To Date</label>
                    <input type="date" name="to_date" value="<?php echo e($toDate ?? ''); ?>" onchange="this.form.submit()">
                </div>
                <?php if($fromDate || $toDate): ?>
                    <a href="<?php echo e(route('admin.analytics')); ?>" class="clear-btn">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        
        <div class="analytics-grid">
            <div class="glass-card stat-card stat-total">
                <div class="stat-icon"><i data-lucide="file-text"></i></div>
                <div class="stat-info">
                    <span class="stat-value"><?php echo e(number_format($stats['total'])); ?></span>
                    <span class="stat-label">Total Submissions</span>
                </div>
            </div>
            <div class="glass-card stat-card stat-approved">
                <div class="stat-icon"><i data-lucide="check-circle"></i></div>
                <div class="stat-info">
                    <span class="stat-value"><?php echo e(number_format($stats['approved'])); ?></span>
                    <span class="stat-label">Approved Doctors</span>
                </div>
            </div>
            <div class="glass-card stat-card stat-pending">
                <div class="stat-icon"><i data-lucide="clock"></i></div>
                <div class="stat-info">
                    <span class="stat-value"><?php echo e(number_format($stats['pending'])); ?></span>
                    <span class="stat-label">Pending Review</span>
                </div>
            </div>
            <div class="glass-card stat-card stat-psych">
                <div class="stat-icon"><i data-lucide="brain"></i></div>
                <div class="stat-info">
                    <span class="stat-value"><?php echo e(number_format($stats['psych'])); ?></span>
                    <span class="stat-label">Psych Specialists</span>
                </div>
            </div>
        </div>

        
        <div class="analytics-sections">
            
            
            <div class="glass-card">
                <div class="card-header">
                    <div class="card-title">Top Professional Titles</div>
                    <div style="background: rgba(139, 92, 246, 0.2); padding: 8px; border-radius: 10px;">
                        <i data-lucide="briefcase" style="color: #8b5cf6; width: 22px;"></i>
                    </div>
                </div>
                <?php if(count($specialties) === 0): ?>
                    <div style="text-align: center; padding: 30px; color: rgba(255,255,255,0.4); font-weight: 600;">
                        No data available for this period.
                    </div>
                <?php endif; ?>
                <?php $__currentLoopData = $specialties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php 
                        $percent = $stats['total'] > 0 ? ($s->count / $stats['total']) * 100 : 0; 
                        $colors = [
                            'linear-gradient(90deg, #8b5cf6, #d946ef)',
                            'linear-gradient(90deg, #3b82f6, #2dd4bf)',
                            'linear-gradient(90deg, #f59e0b, #fbbf24)',
                            'linear-gradient(90deg, #10b981, #34d399)',
                            'linear-gradient(90deg, #ec4899, #f43f5e)',
                        ];
                        $bg = $colors[$index % count($colors)];
                    ?>
                    <div class="progress-wrap">
                        <div class="data-row">
                            <span class="data-label"><?php echo e($s->professional_titles ?: 'Not Specified'); ?></span>
                            <span class="data-value" style="color: transparent; background: <?php echo e($bg); ?>; -webkit-background-clip: text;"><?php echo e($s->count); ?></span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar" style="width: <?php echo e($percent); ?>%; background: <?php echo e($bg); ?>;"></div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
            <div class="glass-card">
                <div class="card-header">
                    <div class="card-title">Gender Distribution</div>
                    <div style="background: rgba(236, 72, 153, 0.2); padding: 8px; border-radius: 10px;">
                        <i data-lucide="users" style="color: #ec4899; width: 22px;"></i>
                    </div>
                </div>
                <?php if(count($genderData) === 0): ?>
                    <div style="text-align: center; padding: 30px; color: rgba(255,255,255,0.4); font-weight: 600;">
                        No demographic data available.
                    </div>
                <?php endif; ?>
                <?php $__currentLoopData = $genderData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php 
                        $percent = $stats['total'] > 0 ? ($g['count'] / $stats['total']) * 100 : 0; 
                        $bg = $g['label'] == 'Male' ? 'linear-gradient(90deg, #3b82f6, #60a5fa)' : 
                             ($g['label'] == 'Female' ? 'linear-gradient(90deg, #ec4899, #f472b6)' : 'linear-gradient(90deg, #8b5cf6, #a78bfa)');
                    ?>
                    <div class="progress-wrap">
                        <div class="data-row">
                            <span class="data-label"><?php echo e($g['label']); ?></span>
                            <span class="data-value"><?php echo e($g['count']); ?> <span style="font-size: 0.8rem; color: rgba(255,255,255,0.5); font-weight: 600;">APPLICANTS</span></span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar" style="width: <?php echo e($percent); ?>%; background: <?php echo e($bg); ?>;"></div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

        </div>

    </div>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\websystem\resources\views/admin/analytics.blade.php ENDPATH**/ ?>