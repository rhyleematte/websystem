@php
    $application = \App\Models\DoctorApplication::where('user_id', $me->id)->latest()->first();
@endphp

@if($application && $application->status === 'pending')
<div class="panel" style="padding: 40px; text-align: center;">
    <div style="width: 80px; height: 80px; background: rgba(243, 156, 18, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: #f39c12;">
        <i data-lucide="clock" style="width: 40px; height: 40px;"></i>
    </div>
    <h2 style="font-size: 2rem; margin-bottom: 15px; color: var(--text);">Application Pending</h2>
    <p style="color: var(--muted); font-size: 1.1rem; line-height: 1.6;">Your application to become a doctor is currently under review. Our administrators will review your submitted credentials and biometric data shortly. Thank you for your patience!</p>
</div>
@elseif($me->doctor_status === 'approved' || ($application && $application->status === 'approved'))
<div class="panel" style="padding: 40px; text-align: center;">
    <div style="width: 80px; height: 80px; background: rgba(46, 204, 113, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: #2ecc71;">
        <i data-lucide="check-circle" style="width: 40px; height: 40px;"></i>
    </div>
    <h2 style="font-size: 2rem; margin-bottom: 15px; color: var(--text);">Application Approved</h2>
    <p style="color: var(--muted); font-size: 1.1rem; line-height: 1.6;">Congratulations! You are officially an approved medical staff member.</p>
</div>
@elseif($me->doctor_status === 'rejected' || ($application && $application->status === 'rejected'))
<div id="rejectionFeedback">
    <div class="panel" style="padding: 40px; text-align: center; border-left: 6px solid #ef4444; background: rgba(239, 68, 68, 0.02);">
        <div style="width: 80px; height: 80px; background: rgba(239, 68, 68, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: #ef4444;">
            <i data-lucide="x-circle" style="width: 40px; height: 40px;"></i>
        </div>
        <h2 style="font-size: 2rem; margin-bottom: 15px; color: var(--text);">Application Update</h2>
        
        @if($application && $application->admin_notes)
            <div style="background: var(--panel); border: 1px solid var(--border); padding: 20px; border-radius: 12px; margin: 25px auto; max-width: 500px; text-align: left;">
                <strong style="display: block; font-size: 0.8rem; text-transform: uppercase; color: var(--muted); margin-bottom: 8px;">Administrator Feedback:</strong>
                <p style="font-style: italic; color: var(--text); line-height: 1.5;">"{{ $application->admin_notes }}"</p>
            </div>
        @endif

        <p style="color: var(--muted); font-size: 1.1rem; line-height: 1.6; margin-bottom: 30px;">
            Unfortunately, your application was not approved at this time. Please address the feedback above and submit a new petition to re-apply.
        </p>

        <button type="button" onclick="showReapplyForm()" class="share-btn" style="margin: 0 auto; display: inline-flex; align-items: center; gap: 10px; padding: 14px 28px; font-size: 1rem; border-radius: 10px; cursor: pointer;">
            <i data-lucide="refresh-ccw" style="width: 20px; height: 20px;"></i>
            Petition to Re-apply Now
        </button>
    </div>
</div>

<div id="reapplicationForm" style="display: none;">
    <div class="panel" style="padding: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
            <div>
                <h2 style="margin-bottom: 5px;">Re-apply for Medical Staff</h2>
                <p class="muted">Update your credentials based on the feedback provided.</p>
            </div>
            <button type="button" onclick="hideReapplyForm()" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); padding: 8px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                <i data-lucide="x" style="width: 16px; height: 16px;"></i> Cancel
            </button>
        </div>
        
        @include('profile._application_form')
    </div>
</div>
@else
<div class="panel" style="padding: 30px;">
    <h2 style="margin-bottom: 10px;">Apply for Medical Staff</h2>
    <p class="muted" style="margin-bottom: 25px;">Submit your professional titles, documents, and biometric verification to join our platform as a doctor.</p>
    
    @include('profile._application_form')
</div>
@endif

<script>
    function showReapplyForm() {
        const feedback = document.getElementById('rejectionFeedback');
        const formArea = document.getElementById('reapplicationForm');
        if(feedback && formArea) {
            feedback.style.display = 'none';
            formArea.style.display = 'block';
            window.scrollTo({ top: formArea.offsetTop - 100, behavior: 'smooth' });
        }
    }
    
    function hideReapplyForm() {
        const feedback = document.getElementById('rejectionFeedback');
        const formArea = document.getElementById('reapplicationForm');
        if(feedback && formArea) {
            feedback.style.display = 'block';
            formArea.style.display = 'none';
            window.scrollTo({ top: feedback.offsetTop - 100, behavior: 'smooth' });
        }
    }
</script>
