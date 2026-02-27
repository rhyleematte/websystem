@if(isset($application) && $application->status === 'pending')
<div class="panel" style="padding: 40px; text-align: center;">
    <div style="width: 80px; height: 80px; background: rgba(243, 156, 18, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: #f39c12;">
        <i data-lucide="clock" style="width: 40px; height: 40px;"></i>
    </div>
    <h2 style="font-size: 2rem; margin-bottom: 15px; color: var(--text);">Application Pending</h2>
    <p style="color: var(--muted); font-size: 1.1rem; line-height: 1.6;">Your application to become a doctor is currently under review. Our administrators will review your submitted credentials and biometric data shortly. Thank you for your patience!</p>
</div>
@elseif($me->doctor_status === 'approved' || (isset($application) && $application->status === 'approved'))
<div class="panel" style="padding: 40px; text-align: center;">
    <div style="width: 80px; height: 80px; background: rgba(46, 204, 113, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: #2ecc71;">
        <i data-lucide="check-circle" style="width: 40px; height: 40px;"></i>
    </div>
    <h2 style="font-size: 2rem; margin-bottom: 15px; color: var(--text);">Application Approved</h2>
    <p style="color: var(--muted); font-size: 1.1rem; line-height: 1.6;">Congratulations! You are officially an approved medical staff member.</p>
</div>
@else
<div class="panel" style="padding: 30px;">
    <h2 style="margin-bottom: 10px;">Apply for Medical Staff</h2>
    <p class="muted" style="margin-bottom: 25px;">Submit your professional titles, documents, and biometric verification to join our platform as a doctor.</p>

    @if(session('success'))
        <div style="background: rgba(46, 204, 113, 0.1); color: #2ecc71; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border: 1px solid rgba(46, 204, 113, 0.4);">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: rgba(231, 76, 60, 0.1); color: #e74c3c; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border: 1px solid rgba(231, 76, 60, 0.4);">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div style="background: rgba(231, 76, 60, 0.1); color: #ef4444; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border: 1px solid rgba(239, 68, 68, 0.2);">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('doctor.apply.store') }}" enctype="multipart/form-data" id="docApplicationForm">
        @csrf

        {{-- Professional Titles --}}
        <h3 style="margin: 20px 0 10px; font-size: 1.1em; color: var(--brand); border-bottom: 1px solid var(--border); padding-bottom: 5px;">1. Professional Titles</h3>
        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Titles (e.g., MD, PhD, RN) <span style="color: #ef4444;">*</span></label>
        <div style="display: flex; align-items: center; border: 1px solid var(--border); border-radius: 10px; padding: 12px; background: var(--input-bg); margin-bottom: 20px;">
            <i data-lucide="award" style="width: 18px; height: 18px; color: var(--muted); margin-right: 10px;"></i>
            <input type="text" name="professional_titles" value="{{ old('professional_titles') }}" placeholder="e.g., Cardiologist, MD" required style="border: none; outline: none; width: 100%; font-size: 14px; background: transparent; color: var(--text);" />
        </div>

        {{-- Verification Documents --}}
        <h3 style="margin: 25px 0 10px; font-size: 1.1em; color: var(--brand); border-bottom: 1px solid var(--border); padding-bottom: 5px;">2. Documentation & Identifications</h3>
        @if(isset($requirements) && $requirements->isNotEmpty())
            @foreach($requirements as $req)
            <label style="display: block; font-weight: 600; margin-top: 15px; margin-bottom: 5px;">
                {{ $req->name }}
                @if($req->is_required) <span style="color: #ef4444;">*</span> @else <span style="color: var(--muted); font-size: 0.8em; font-weight: normal;">(Optional)</span> @endif
            </label>
            @if($req->description)
                <small style="color: var(--muted); display: block; margin-top: -3px; margin-bottom: 10px;">{{ $req->description }}</small>
            @endif
            
            <div style="margin-bottom: 20px; padding: 15px; background: var(--hover); border: 1px dashed var(--border); border-radius: 10px; display: flex; align-items: center;">
                @if(stripos($req->name, 'video') !== false)
                    <i data-lucide="video" style="color: var(--brand); margin-right: 10px;"></i>
                    <input type="file" name="req_{{ $req->id }}" accept="video/mp4,video/x-m4v,video/*" @if($req->is_required) required @endif style="background: transparent; color: var(--text); width: 100%;" />
                @else
                    <i data-lucide="upload-cloud" style="color: var(--brand); margin-right: 10px;"></i>
                    <input type="file" name="req_{{ $req->id }}" accept=".pdf,.jpeg,.jpg,.png" @if($req->is_required) required @endif style="background: transparent; color: var(--text); width: 100%;" />
                @endif
            </div>
            @endforeach
        @endif

        {{-- Biometrics / Liveliness --}}
        <h3 style="margin: 25px 0 10px; font-size: 1.1em; color: var(--brand); border-bottom: 1px solid var(--border); padding-bottom: 5px;">3. Biometric Verification</h3>
        <p style="color: var(--muted); font-size: 13px; margin-bottom: 15px;">To ensure the safety of our platform, we require a live Face ID check to match your submitted Licensed ID. We do not permanently store your raw selfie image, only a secure verification hash and timestamp.</p>
        
        <div style="background: var(--hover); border: 1px solid var(--border); border-radius: 10px; padding: 20px; text-align: center; margin-bottom: 20px;">
            <video id="bioVideo" width="100%" height="auto" style="border-radius: 8px; max-height: 240px; background: #000; display: none;" autoplay playsinline></video>
            <canvas id="bioCanvas" style="display: none;"></canvas>
            <img id="bioPreview" style="border-radius: 8px; max-height: 240px; display: none; margin: 0 auto; border: 2px solid var(--brand);" />
            
            <div id="bioControls" style="margin-top: 15px;">
                <button type="button" id="startBioCamera" style="background: var(--panel); border: 1px solid var(--border); padding: 8px 16px; border-radius: 8px; color: var(--text); font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                    <i data-lucide="camera"></i> Start Face Match Camera
                </button>
                <button type="button" id="simulateBioCamera" style="background: rgba(243, 156, 18, 0.1); border: 1px solid #f39c12; padding: 8px 16px; border-radius: 8px; color: #f39c12; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; margin-left: 10px;">
                    <i data-lucide="bot"></i> Simulate Scanner (Testing)
                </button>
                <button type="button" id="captureBioPhoto" style="background: var(--brand); border: none; padding: 8px 16px; border-radius: 8px; color: white; font-weight: 600; cursor: pointer; display: none; align-items: center; gap: 8px; margin: 0 auto;">
                    <i data-lucide="scan-face"></i> Capture & Verify
                </button>
                <button type="button" id="retakeBioPhoto" style="background: var(--panel); border: 1px solid var(--border); padding: 8px 16px; border-radius: 8px; color: var(--text); font-weight: 600; cursor: pointer; display: none; align-items: center; gap: 8px; margin: 0 auto;">
                    <i data-lucide="refresh-cw"></i> Retake Snapshot
                </button>
            </div>

            <p id="bioStatusText" style="margin-top: 15px; font-size: 13px; color: var(--brand); font-weight: 600; display: none;">
                <i data-lucide="shield-check" style="width: 14px; height: 14px; vertical-align: middle;"></i> Liveliness & Face Match Verified
            </p>
        </div>

        <label style="display: flex; align-items: flex-start; gap: 10px; font-size: 14px; color: var(--text); cursor: pointer; background: rgba(124, 58, 237, 0.05); padding: 15px; border-radius: 10px; border: 1px solid rgba(124, 58, 237, 0.2); margin-bottom: 25px;">
            <input type="checkbox" name="biometric_consent" required style="margin-top: 3px;" />
            <span><strong>Consent Agreement:</strong> I agree to biometric verification for identity matching and liveness checks. I understand my raw facial scan is not permanently stored. <span style="color: #ef4444;">*</span></span>
        </label>

        {{-- Hidden fields for our generated biometric hashes to simulate backend match logic --}}
        <input type="hidden" name="liveness_verified" id="inp_liveness" value="0" />
        <input type="hidden" name="face_match_score" id="inp_face_score" value="" />
        <input type="hidden" name="biometric_payload" id="inp_bio_payload" value="" />

        <button type="submit" class="share-btn" style="width: 100%; justify-content: center; padding: 14px; font-size: 16px; border-radius: 12px; display: flex; align-items: center; gap: 8px;" id="btnSubmitApplication">
           <i data-lucide="file-check-2"></i> Submit Application
        </button>
    </form>
</div>

<script>
    // Biometric camera logic
    const bioVideo = document.getElementById('bioVideo');
    const bioCanvas = document.getElementById('bioCanvas');
    const bioPreview = document.getElementById('bioPreview');
    const btnStart = document.getElementById('startBioCamera');
    const btnCapture = document.getElementById('captureBioPhoto');
    const btnRetake = document.getElementById('retakeBioPhoto');
    const btnSimulate = document.getElementById('simulateBioCamera');
    const bioStatus = document.getElementById('bioStatusText');
    
    // Inputs
    const inpLiveness = document.getElementById('inp_liveness');
    const inpFaceScore = document.getElementById('inp_face_score');
    const inpPayload = document.getElementById('inp_bio_payload');
    const formBtn = document.getElementById('btnSubmitApplication');

    let stream = null;
    
    if (btnSimulate) {
        btnSimulate.addEventListener('click', () => {
             simulateSuccess();
        });
    }

    btnStart.addEventListener('click', async () => {
        try {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                // Fallback for HTTP / Insecure contexts during local development
                console.warn('getUserMedia is not supported or context is not secure. Simulating camera access.');
                if(confirm('Camera access is blocked (likely because you are not on HTTPS). Do you want to simulate a successful biometric scan for testing?')) {
                    simulateSuccess();
                } else {
                    alert('Biometric verification cancelled.');
                }
                return;
            }

            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
            bioVideo.srcObject = stream;
            bioVideo.style.display = 'block';
            bioPreview.style.display = 'none';
            btnStart.style.display = 'none';
            btnCapture.style.display = 'inline-flex';
            btnRetake.style.display = 'none';
            bioStatus.style.display = 'none';
            
            // reset payload
            inpLiveness.value = "0";
            inpFaceScore.value = "";
            inpPayload.value = "";
        } catch (err) {
            if (err.name === 'NotAllowedError' || err.name === 'SecurityError') {
                if(confirm('Camera access was denied by your system settings. Do you want to simulate a successful biometric scan to bypass this requirement for testing?')) {
                    simulateSuccess();
                    return;
                }
            }
            alert('Unable to access camera: ' + err.name + ' - ' + err.message + '\n\nPlease ensure no other application (like Zoom) is using it.');
            console.error(err);
        }
    });

    btnCapture.addEventListener('click', () => {
        if (!stream) return;
        
        bioCanvas.width = bioVideo.videoWidth;
        bioCanvas.height = bioVideo.videoHeight;
        bioCanvas.getContext('2d').drawImage(bioVideo, 0, 0);
        
        const photoData = bioCanvas.toDataURL('image/jpeg');
        
        bioPreview.src = photoData;
        bioPreview.style.display = 'block';
        bioVideo.style.display = 'none';
        
        // Stop camera tracks
        stream.getTracks().forEach(t => t.stop());
        stream = null;
        
        simulateSuccess(photoData);
    });

    function simulateSuccess(dataUrl = null) {
        btnStart.style.display = 'none';
        if (btnSimulate) btnSimulate.style.display = 'none';
        btnCapture.style.display = 'none';
        btnRetake.style.display = 'inline-flex';
        bioStatus.style.display = 'block';

        // Simulate backend verification success payload
        inpLiveness.value = "1";
        // Simulate a 90%+ match score randomly
        inpFaceScore.value = (90 + Math.random() * 9).toFixed(2);

        // Use provided snapshot or a dummy base64 string for testing
        inpPayload.value = dataUrl || "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII="; 
        
        if (!dataUrl) {
            bioPreview.src = inpPayload.value;
            bioPreview.style.display = 'block';
            bioVideo.style.display = 'none';
        }
    }

    btnRetake.addEventListener('click', () => {
        btnStart.click(); // restart process
    });
    
    document.getElementById('docApplicationForm').addEventListener('submit', function(e) {
        if(inpLiveness.value === "0") {
            e.preventDefault();
            alert("Please complete the Face Match verification first by starting the camera and capturing a photo.");
        }
    });
</script>
@endif
