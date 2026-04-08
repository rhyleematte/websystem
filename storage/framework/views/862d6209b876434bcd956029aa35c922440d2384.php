<link rel="stylesheet" href="<?php echo e(asset('assets/css/appointments_ios.css')); ?>">

<div class="schedule-modal" id="appointmentsModal" style="display:none; position:fixed; z-index:9999; inset:0;">
    <div class="schedule-backdrop" id="appointmentsBackdrop" style="position:absolute; inset:0; background:rgba(0,0,0,0.5);"></div>
    <div class="schedule-content" style="position:relative; width:95%; max-width:1100px; height:90vh; border-radius:14px; margin:2% auto; display:flex; flex-direction:column; overflow:hidden;">
        
        <!-- Apple-Style Header -->
        <div class="schedule-header">
            <!-- Search Header -->
            <div id="iosSearchHeader" style="display:none; align-items:center; gap:10px; padding:5px 0;">
                <div style="flex:1; background:var(--ios-secondary); border-radius:10px; display:flex; align-items:center; padding:5px 10px;">
                    <i data-lucide="search" style="width:16px; color:var(--ios-text-muted);"></i>
                    <input type="text" id="iosSearchInput" placeholder="Search" style="background:none; border:none; color:var(--ios-text); padding:5px; width:100%; outline:none; font-size:0.9rem;">
                </div>
                <button id="iosSearchCancel" style="background:none; border:none; color:var(--ios-accent); font-size:0.9rem; cursor:pointer;">Cancel</button>
            </div>

            <!-- Default Calendar Header -->
            <div class="ios-header-nav" id="iosDefaultHeader">
                <div class="ios-header-left">
                    <div id="iosBackLink" style="display:none; align-items:center; gap:3px;">
                        <i data-lucide="chevron-left" style="width:24px; height:24px;"></i>
                        <span id="iosBackLabel" style="font-size:1.1rem; font-weight:700;">2026</span>
                    </div>
                    <div id="iosYearNav" style="color:var(--ios-accent); font-size:1.1rem; font-weight:700; cursor:pointer; display:none;">2026</div>
                </div>

                <div class="ios-header-center">
                    <div id="iosCurrentMonth" style="font-size:1.3rem; font-weight:800; color:var(--ios-text);">April</div>
                </div>

                <div class="ios-header-right">
                    <div class="ios-header-icons-group">
                        <button class="ios-icon-btn" id="iosSearchToggle" title="Search">
                            <i data-lucide="search"></i>
                        </button>
                        <button class="ios-icon-btn" id="headerAddAptBtn" title="Add Appointment">
                            <i data-lucide="plus"></i>
                        </button>
                        <button class="ios-icon-btn" id="appointmentsCloseBtn" title="Close" onclick="closeModal()">
                            <i data-lucide="x"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Create Appointment Header -->
            <div class="ios-header-nav" id="iosCreateHeader" style="display:none;">
                <div class="ios-header-left">
                    <button class="ios-btn-link" onclick="switchAptView('calendar')">Cancel</button>
                </div>
                <div class="ios-header-center">
                    <div style="font-size:1.2rem; font-weight:700; color:var(--ios-text);">New Appointment</div>
                </div>
                <div class="ios-header-right">
                    <button class="ios-btn-link" form="aptStoreForm" type="submit" style="font-weight:700;">Add</button>
                </div>
            </div>
        </div>

        <div class="schedule-body">
            <!-- Calendar View -->
            <div id="aptViewCalendar" class="apt-view-container">
                <div class="schedule-split-container">
                    <!-- Left: Calendar -->
                    <div class="calendar-col">
                        <div id="appointmentsCalendar"></div>
                    </div>
                    
                    <!-- Right: Daily Digest -->
                    <div id="aptDailyAgenda" class="digest-col">
                        <!-- Content will be injected by JS -->
                        <div class="agenda-empty-state">
                            Select a date to view your daily digest
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Appointment View Sub-Modal -->
<div id="aptViewModal" class="apt-sub-modal">
    <div class="apt-sub-modal-content">
        <div class="apt-sub-modal-header">
            <div style="width:60px;"></div> <!-- Spacer to match layout -->
            <h3 id="viewAptTitle">Appointment</h3>
            <button class="ios-btn-link" onclick="closeAptSubModal('aptViewModal')">Done</button>
        </div>
        <div class="apt-form-body">
            <div class="ios-row">
                <label class="ios-input-label">Time</label>
                <div id="viewAptTime" style="font-size:1rem;"></div>
            </div>
            <div class="ios-row">
                <label class="ios-input-label">Organizer</label>
                <div id="viewAptCreator" style="font-size:1rem;"></div>
            </div>
            <div id="viewAptDescContainer" class="ios-row" style="display:none;">
                <label class="ios-input-label">Notes</label>
                <div id="viewAptDesc" style="font-size:0.95rem; white-space:pre-wrap;"></div>
            </div>
            
            <p id="viewAptStatus" style="font-weight:700; text-transform:uppercase; font-size:0.75rem; margin-top:1rem; padding:0 0.5rem;"></p>

            <div id="aptInviteActions" style="display:none; gap:10px; margin-top:1rem;">
                <button onclick="respondApt('accepted')" class="ios-btn-primary" style="flex:1; background:#1cc88a;">Accept</button>
                <button onclick="respondApt('declined')" class="ios-btn-primary" style="flex:1; background:#e74a3b;">Decline</button>
            </div>
            <div id="aptOwnerActions" style="display:none; margin-top:1rem;">
                <button onclick="deleteApt()" class="ios-btn-primary" style="background:rgba(255,59,48,0.15); color:var(--ios-accent);">Delete Appointment</button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\websystem\resources\views/partials/appointments_modal.blade.php ENDPATH**/ ?>