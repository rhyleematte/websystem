@extends('layouts.dashboard')

@section('title', 'Appointment Details | AskDocPH')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/appointments_ios.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .creation-page-wrapper {
            padding-top: 80px;
            padding-bottom: 50px;
            {{ $isDeleted ? 'filter: grayscale(1); pointer-events: none;' : '' }}
            background: var(--bg);
            min-height: 100vh;
        }

        .appointment-deleted-alert {
            background: #fff5f5;
            border: 1px solid #feb2b2;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            color: #c53030;
            {{ $isDeleted ? 'filter: none !important; pointer-events: auto !important;' : '' }}
        }

        .alert-icon i {
            width: 32px;
            height: 32px;
        }

        .alert-content h3 {
            margin: 0 0 4px 0;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .alert-content p {
            margin: 0;
            font-size: 0.95rem;
            opacity: 0.8;
        }

        .deleted-opaque {
            opacity: 0.5;
        }

        .apt-view-container {
            height: auto !important;
            min-height: calc(100vh - 80px);
        }

        .read-only-pill {
            cursor: default !important;
        }

        .status-badge-ios {
            font-size: 0.7rem;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-left: 10px;
        }

        .status-pending {
            background: rgba(246, 194, 62, 0.1);
            color: #f6c23e;
        }

        .status-accepted {
            background: rgba(28, 200, 138, 0.1);
            color: #1cc88a;
        }

        .status-declined {
            background: rgba(231, 74, 59, 0.1);
            color: #e74a3b;
        }

        .btn-accept-ios {
            background: #1cc88a !important;
            color: white;
            border: none;
            width: 100%;
            padding: 1.2rem;
            border-radius: 16px;
            font-size: 1.05rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(28, 200, 138, 0.3) !important;
            margin-bottom: 12px;
            transition: transform 0.2s;
        }

        .btn-decline-ios {
            background: #f8f9fa !important;
            color: #495057;
            border: 1px solid #dee2e6;
            width: 100%;
            padding: 1.2rem;
            border-radius: 16px;
            font-size: 1.05rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-decline-ios:hover {
            background: #e9ecef !important;
        }

        .btn-accept-ios:active,
        .btn-decline-ios:active {
            transform: scale(0.97);
        }
    </style>
@endpush

@section('content')
    <div class="creation-page-wrapper">
        <div id="aptViewShow" class="apt-view-container">
            <div class="create-body">
                <!-- Main Content -->
                <div class="create-main">
                    <div class="discard-draft" onclick="window.history.back()">
                        <i data-lucide="chevron-left"></i> BACK TO DASHBOARD
                    </div>

                    @if($isDeleted)
                        <div class="appointment-deleted-alert">
                            <div class="alert-icon">
                                <i data-lucide="alert-triangle"></i>
                            </div>
                            <div class="alert-content">
                                <h3>Appointment Canceled</h3>
                                <p>This appointment has been removed from the schedule by the organizer.</p>
                            </div>
                        </div>
                    @endif

                    <div class="title-section">
                        <h1 id="aptShowSubject" class="massive-title-input" style="color: var(--text); border: none; min-height: auto;">{{ $appointment->subject }}</h1>
                        <div class="title-underline"></div>
                    </div>

                    <div class="hero-inputs-grid">
                        <div class="hero-pill-wrap">
                            <label class="section-label">DATE</label>
                            <div class="hero-pill read-only-pill">
                                <i data-lucide="calendar" class="pill-icon-red"></i>
                                <div class="hero-pill-content">
                                    <div class="hero-pill-value">{{ $appointment->start_at->format('F j, Y') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="hero-pill-wrap">
                            <label class="section-label">TIME</label>
                            <div class="hero-pill read-only-pill">
                                <i data-lucide="clock" class="pill-icon-red"></i>
                                <div class="hero-pill-content">
                                    <span class="hero-pill-value">
                                        {{ $appointment->start_at->format('h:i A') }} —
                                        {{ $appointment->end_at->format('h:i A') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="hero-pill-wrap" style="margin-top: 1.5rem;">
                        <label class="section-label">LOCATION</label>
                        <div class="hero-pill read-only-pill">
                            <i data-lucide="map-pin" class="pill-icon-red"></i>
                            <div class="hero-pill-value">{{ $appointment->location ?? 'No location specified' }}</div>
                        </div>
                    </div>

                    <div class="visual-placeholder-ios"
                        style="height: 320px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                        <img src="{{ $appointment->cover_image ? asset($appointment->cover_image) : asset('assets/img/appointment_default.jpg') }}"
                            alt="Studio Visual" class="ios-visual-img" style="opacity: 1;">
                    </div>
                    <div class="editorial-notes-wrap {{ $isDeleted ? 'deleted-opaque' : '' }}">
                        <div class="section-label">Editorial Notes</div>
                        <div class="editorial-textarea-ios" style="background: #f8f9fa; min-height: 150px;">
                            {!! nl2br(e($appointment->description)) !!}
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="create-sidebar">
                    <div id="aptActionError" class="ios-btn-link-red"
                        style="display: none; background: #fff5f5; padding: 1rem; border-radius: 12px; margin-bottom: 1rem; text-align: center; border: 1px solid #ff000020; font-weight: 600; font-size: 0.9rem;">
                        <!-- Error messages appear here -->
                    </div>

                    <div class="action-card-ios">
                        @if($isCreator)
                            <button type="button" onclick="alert('Inline editing coming soon!')"
                                class="btn-create-apt-red">Update Appointment</button>
                            <button type="button" onclick="deleteApt()" class="btn-decline-ios"
                                style="margin-top: 1rem; width: 100%;">Delete Appointment</button>
                            <div class="workspace-label-mini">YOU ARE THE ORGANIZER</div>
                        @elseif($invitation)
                            @if($invitation->status === 'pending')
                                <button type="button" onclick="respondToInvite('accepted')" class="btn-accept-ios">Accept
                                    Invitation</button>
                                <button type="button" onclick="respondToInvite('declined')" class="btn-decline-ios">Decline</button>
                            @else
                                <div class="status-badge-ios status-{{ $invitation->status }}"
                                    style="display: block; width: 100%; text-align: center; padding: 1.2rem; font-size: 1rem;">
                                    Invitation {{ ucfirst($invitation->status) }}
                                </div>
                                @if($invitation->status === 'accepted')
                                    <button type="button" onclick="respondToInvite('declined')" class="ios-btn-link-red"
                                        style="margin-top: 1rem; display: block; width: 100%; text-align: center;">Cancel
                                        Participation</button>
                                    <div class="workspace-label-mini" style="margin-top: 1rem;">SCHEDULED ON YOUR CALENDAR</div>
                                @else
                                    <div class="workspace-label-mini" style="margin-top: 1rem;">NOT ON YOUR CALENDAR</div>
                                @endif
                            @endif
                        @endif
                    </div>

                    <div class="participants-section-ios">
                        <label class="section-label">PARTICIPANTS</label>
                        <div class="participants-stack">
                            <!-- Show Creator First -->
                            <div class="participant-card-ios">
                                <div class="p-card-avatar">
                                    <img src="{{ $appointment->creator->avatar_url }}" alt="Avatar">
                                </div>
                                <div class="p-card-info">
                                    <div class="p-card-name">{{ $appointment->creator->full_name }}</div>
                                    <div class="p-card-title">ORGANIZER</div>
                                </div>
                            </div>

                            <!-- Show Other Invitees -->
                            @foreach($appointment->invitations as $invite)
                                @if($invite->user_id !== $appointment->creator_id)
                                    <div class="participant-card-ios">
                                        <div class="p-card-avatar">
                                            <img src="{{ $invite->user->avatar_url }}" alt="Avatar">
                                        </div>
                                        <div class="p-card-info">
                                            <div class="p-card-name">
                                                {{ $invite->user->full_name }}
                                                <span
                                                    class="status-badge-ios status-{{ $invite->status }}">{{ $invite->status }}</span>
                                            </div>
                                            <div class="p-card-title">GUEST</div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        async function respondToInvite(status) {
            if (!confirm(`Are you sure you want to ${status} this invitation?`)) return;

            const errDisp = document.getElementById('aptActionError');
            if (errDisp) errDisp.style.display = 'none';

            try {
                const response = await fetch("{{ route('appointments.respond', $invitation->id ?? 0) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status })
                });

                const result = await response.json();
                if (result.ok) {
                    location.reload();
                } else {
                    if (errDisp) {
                        errDisp.innerText = result.message || 'Failed to respond to invitation.';
                        errDisp.style.display = 'block';
                    } else {
                        alert(result.message || 'Failed to respond to invitation.');
                    }
                }
            } catch (error) {
                console.error('Error responding to invitation:', error);
                if (errDisp) {
                    errDisp.innerText = 'An error occurred. Please try again.';
                    errDisp.style.display = 'block';
                } else {
                    alert('An error occurred. Please try again.');
                }
            }
        }

        async function deleteApt() {
            if (!confirm('Are you sure you want to DELETE this appointment? This action cannot be undone.')) return;

            const errDisp = document.getElementById('aptActionError');
            if (errDisp) errDisp.style.display = 'none';

            try {
                const response = await fetch("{{ route('appointments.destroy', $appointment->id) }}", {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();
                if (result.ok) {
                    location.reload();
                } else {
                    if (errDisp) {
                        errDisp.innerText = result.message || 'Failed to delete appointment.';
                        errDisp.style.display = 'block';
                    } else {
                        alert(result.message || 'Failed to delete appointment.');
                    }
                }
            } catch (error) {
                console.error('Error deleting appointment:', error);
                if (errDisp) {
                    errDisp.innerText = 'An error occurred. Please try again.';
                    errDisp.style.display = 'block';
                } else {
                    alert('An error occurred. Please try again.');
                }
            }
        }
    </script>
@endpush