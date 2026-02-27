<x-guest-layout>
    <div class="min-h-screen bg-slate-50 flex items-center justify-center p-4">
        @if(session('info'))
            <div class="fixed top-5 left-1/2 -translate-x-1/2 z-50 max-w-xl w-[95%] p-4 bg-amber-50 border-l-4 border-amber-500 rounded-lg shadow">
                <p class="text-sm font-medium text-amber-800 text-center">{{ session('info') }}</p>
            </div>
        @endif
        
        @if($isEligible)
            <div class="bg-white w-full max-w-5xl rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row min-h-[650px]">
                
                <div class="w-full md:w-1/3 bg-indigo-600 p-8 text-white flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
                        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0 100 C 20 0 50 0 100 100 Z" fill="white" /></svg>
                    </div>

                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-4 bg-indigo-800/50 p-2 rounded-lg w-fit backdrop-blur-sm">
                            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                            <span class="text-xs font-bold uppercase tracking-wider">Status: {{ $statusMessage }}</span>
                        </div>
                        <h2 class="text-3xl font-bold tracking-tight">Seller Verification</h2>
                        <p class="text-indigo-100 mt-2 text-sm">Complete these steps to activate your seller account.</p>
                    </div>

                    <div class="relative z-10 space-y-8 my-8">
                        <div class="step-indicator flex items-center gap-4 opacity-100 transition-opacity duration-300" id="ind-1">
                            <div class="w-10 h-10 rounded-full border-2 border-white flex items-center justify-center font-bold bg-white text-indigo-600 shadow-lg">1</div>
                            <div><h4 class="font-bold text-lg">Location</h4><p class="text-xs text-indigo-200">Verify Muallim District</p></div>
                        </div>
                        <div class="step-indicator flex items-center gap-4 opacity-50 transition-opacity duration-300" id="ind-2">
                            <div class="w-10 h-10 rounded-full border-2 border-indigo-400 flex items-center justify-center font-bold text-indigo-100">2</div>
                            <div><h4 class="font-bold text-lg">Profile Photo</h4><p class="text-xs text-indigo-200">Professional Look</p></div>
                        </div>
                        <div class="step-indicator flex items-center gap-4 opacity-50 transition-opacity duration-300" id="ind-3">
                            <div class="w-10 h-10 rounded-full border-2 border-indigo-400 flex items-center justify-center font-bold text-indigo-100">3</div>
                            <div><h4 class="font-bold text-lg">Live Identity</h4><p class="text-xs text-indigo-200">Selfie Check</p></div>
                        </div>
                    </div>

                    <div class="relative z-10 border-t border-indigo-500/30 pt-4">
                        <div class="text-xs text-indigo-200 mb-1">Student ID</div>
                        <div class="font-mono font-bold">{{ $matricNo }}</div>
                    </div>
                </div>

                <div class="w-full md:w-2/3 bg-white p-8 md:p-12 relative flex flex-col justify-center">
                    
                    <a href="{{ route('dashboard') }}" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600 transition-colors z-20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>

                    <div id="panel-1" class="step-panel w-full animate-fadeIn">
                        <div class="mb-8">
                            <h3 class="text-2xl font-bold text-slate-800">Where are you located?</h3>
                            <p class="text-slate-500 mt-2">Sellers must be located within Muallim District.</p>
                        </div>
                        <div class="space-y-6 max-w-md mx-auto w-full">
                            <button id="detect_location_btn" class="group w-full max-w-xl mx-auto flex items-center justify-center gap-3 bg-indigo-600 hover:bg-indigo-700 text-white py-4 px-6 rounded-xl font-bold shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-1">
                                <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span>Detect My Location</span>
                            </button>
                            <div id="location_status" class="text-center text-sm min-h-[24px] font-medium flex justify-center items-center"></div>
                        </div>
                    </div>

                    <div id="panel-2" class="step-panel hidden w-full animate-fadeIn">
                        <div class="mb-8 text-center">
                            <h3 class="text-2xl font-bold text-slate-800">Upload Profile Photo</h3>
                            <p class="text-slate-500 mt-2">Make a good first impression.</p>
                        </div>
                        <form id="upload_photo_form" enctype="multipart/form-data" class="max-w-md mx-auto w-full space-y-8">
                            @csrf
                            <div class="flex justify-center">
                                <div class="relative group cursor-pointer" onclick="document.getElementById('profile_photo_input').click()">
                                    <div class="w-40 h-40 rounded-full overflow-hidden border-4 border-slate-100 shadow-xl bg-slate-50 relative">
                                        <img id="profile-preview" src="{{ auth()->user()->hu_profile_photo_path ? asset(auth()->user()->hu_profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->hu_name) }}" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"><span class="text-white text-sm font-bold">Change</span></div>
                                    </div>
                                </div>
                            </div>
                            <input type="file" name="profile_photo" id="profile_photo_input" accept="image/*" class="hidden">
                            <div class="text-center space-y-4">
                                <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-4 rounded-xl font-bold shadow-lg transition-all">Save & Continue</button>
                                <button type="button" onclick="goToStep(1)" class="text-slate-400 hover:text-slate-600 text-sm font-medium">Back</button>
                            </div>
                        </form>
                    </div>

                    <div id="panel-3" class="step-panel hidden w-full animate-fadeIn">
                        <div class="mb-6 text-center">
                            <h3 class="text-2xl font-bold text-slate-800">Live Identity Check</h3>
                            <p class="text-slate-500 text-sm mt-1">Please take a live selfie.</p>
                        </div>
                        <div class="relative bg-black rounded-2xl overflow-hidden shadow-2xl mx-auto w-full max-w-[320px] aspect-[3/4] mb-6 border-4 border-slate-900">
                            <video id="camera_preview" autoplay playsinline class="w-full h-full object-cover transform -scale-x-100 hidden"></video>
                            <canvas id="snapshot_canvas" class="w-full h-full object-cover hidden"></canvas>
                            <div id="camera_placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-slate-500 bg-slate-100">
                                <div class="w-20 h-20 rounded-full bg-slate-200 flex items-center justify-center mb-4">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                </div>
                                <span class="font-medium">Camera Inactive</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-3 max-w-sm mx-auto w-full">
                            <button id="start_camera" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-bold shadow-lg">Start Camera</button>
                            <div id="camera_controls" class="hidden grid grid-cols-2 gap-3">
                                <button id="retake_snapshot" class="bg-slate-200 text-slate-700 py-3 rounded-xl font-bold hidden">Retake</button>
                                <button id="take_snapshot" class="col-span-2 bg-indigo-600 text-white py-3 rounded-xl font-bold shadow-lg">Capture</button>
                                <button id="confirm_snapshot" class="bg-green-500 hover:bg-green-600 text-white py-3 rounded-xl font-bold shadow-lg hidden">Confirm & Upload</button>
                            </div>
                        </div>
                    </div>

                    <div id="panel-success" class="step-panel hidden w-full h-full flex flex-col items-center justify-center animate-fadeIn text-center">
                        <div class="w-24 h-24 rounded-full bg-green-100 flex items-center justify-center text-green-600 mb-6 shadow-xl animate-bounce-short">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h2 class="text-3xl font-extrabold text-slate-900 mb-2">You are now registered as a Student Seller!</h2>
                        <p class="text-slate-500 mb-8">Please complete your profile details.</p>
                        <a href="{{ route('students.create') }}" class="bg-slate-900 text-white px-8 py-3 rounded-xl font-bold shadow-lg">Create Profile</a>
                    </div>
                </div>
            </div>

        @else
            <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl p-10 text-center relative overflow-hidden border border-slate-100">
                
                <div class="w-20 h-20 rounded-full {{ $statusColor == 'red' ? 'bg-red-100 text-red-600' : 'bg-orange-100 text-orange-600' }} mx-auto flex items-center justify-center mb-6 shadow-sm">
                    @if($statusColor == 'red')
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    @else
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    @endif
                </div>

                <h2 class="text-2xl font-bold text-slate-900 mb-2">Registration Unavailable</h2>
                <div class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide mb-6 {{ $statusColor == 'red' ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                    Status: {{ $statusMessage }}
                </div>
                
                <p class="text-slate-600 mb-8 leading-relaxed">
                    {{ $reason }}
                </p>

                <div class="bg-slate-50 rounded-xl p-4 text-left text-sm border border-slate-200 mb-8">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="block text-xs text-slate-400 uppercase font-bold">Matric No</span>
                            <span class="font-mono font-bold text-slate-700">{{ $matricNo }}</span>
                        </div>
                        <div>
                            <span class="block text-xs text-slate-400 uppercase font-bold">Graduation</span>
                            <span class="font-bold text-slate-700">{{ $gradDateDisplay }}</span>
                        </div>
                    </div>
                </div>

                <a href="{{ route('dashboard') }}" class="block w-full bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-xl font-bold transition-all">
                    Back to dashboard
                </a>
            </div>
        @endif
    </div>

    @if($isEligible)
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    const UPSI_LAT = 3.7832;
    const UPSI_LNG = 101.5927;
    const RADIUS_KM = 25;
    let stream = null;
    let selfieDataUrl = null;

// Listen for file selection
    document.getElementById('profile_photo_input').addEventListener('change', function(event) {
        const file = event.target.files[0];
        
        if (file) {
            // Create a file reader to read the image
            const reader = new FileReader();
            
            reader.onload = function(e) {
                // Update the image source immediately
                document.getElementById('profile-preview').src = e.target.result;
            }
            
            // Read the file as a Data URL
            reader.readAsDataURL(file);
        }
    });


    function goToStep(stepNumber) {
        document.querySelectorAll('.step-panel').forEach(el => el.classList.add('hidden'));
        if(stepNumber === 'success') {
            document.getElementById('panel-success').classList.remove('hidden');
            updateSidebar(4);
            return;
        }
        document.getElementById(`panel-${stepNumber}`).classList.remove('hidden');
        updateSidebar(stepNumber);
    }

    function updateSidebar(activeStep) {
        for(let i=1; i<=3; i++) {
            const el = document.getElementById(`ind-${i}`);
            const circle = el.querySelector('div:first-child');
            if(activeStep > 3) {
                circle.classList.add('bg-green-500', 'border-green-500', 'text-white');
                circle.innerHTML = '✓';
            } else if(i === activeStep) {
                el.classList.add('opacity-100');
                circle.classList.add('bg-white', 'border-white', 'text-indigo-600');
                circle.innerHTML = i;
            } else if (i < activeStep) {
                circle.classList.add('bg-indigo-800', 'border-indigo-800', 'text-indigo-300');
                circle.innerHTML = '✓';
            } else {
                el.classList.remove('opacity-100');
                circle.classList.remove('bg-white', 'border-white', 'text-indigo-600');
                circle.innerHTML = i;
            }
        }
    }

    function addressVerified(lat, lng, addr){
        const statusEl = document.getElementById('location_status');
        statusEl.innerHTML = `<div class="flex items-center gap-2 text-indigo-600 bg-indigo-50 px-4 py-2 rounded-lg border border-indigo-100"><span class="font-bold">Verifying...</span></div>`;
        if(lat && lng) {
            fetch("{{ route('verification.save_location') }}", {
                method: 'POST', 
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }, 
                body: JSON.stringify({latitude: lat, longitude: lng, address: addr})
            })
            .then(async res => {
                const data = await res.json().catch(() => ({}));
                if (!res.ok || data.success === false) {
                    throw new Error(data.message || 'Location verification failed.');
                }

                statusEl.innerHTML = `<div class="flex items-center gap-2 text-green-600 bg-green-50 px-4 py-2 rounded-lg border border-green-100"><span class="font-bold">Verified!</span></div>`;
                setTimeout(() => goToStep(2), 1000);
            })
            .catch(err => {
                statusEl.innerHTML = '';
                Swal.fire({icon:'error', text: err.message || 'Unable to verify location. Please try again.'});
            });
            return;
        }
        statusEl.innerHTML = '';
    }

    document.getElementById('detect_location_btn').addEventListener('click', function(){
        const btn = this; const original = btn.innerHTML;
        btn.innerHTML = '<span>Detecting...</span>'; btn.disabled = true;
        if(navigator.geolocation){
            navigator.geolocation.getCurrentPosition(pos => {
                const lat = pos.coords.latitude; const lng = pos.coords.longitude;
                const R = 6371; 
                const dLat = (lat-UPSI_LAT) * Math.PI/180;
                const dLon = (lng-UPSI_LNG) * Math.PI/180;
                const a = Math.sin(dLat/2)**2 + Math.cos(UPSI_LAT*Math.PI/180)*Math.cos(lat*Math.PI/180)*Math.sin(dLon/2)**2;
                const c = 2*Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                const dist = R*c;
                btn.innerHTML = original; btn.disabled = false;
                if(dist <= RADIUS_KM) addressVerified(lat, lng, `GPS: ${lat}, ${lng}`);
                else Swal.fire({icon:'error', text:'You must be in Muallim District.'});
            }, err => {
                btn.innerHTML = original; btn.disabled = false;
                Swal.fire({icon:'error', text:'Please allow location access.'});
            });
        }
    });

    document.getElementById('upload_photo_form').addEventListener('submit', function(e){
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Show loading
        Swal.fire({
            title: 'Uploading...', 
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        fetch("{{ route('students_verification.upload') }}", {
            method: 'POST', 
            body: formData, 
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json' // Tell server we want JSON
            }
        })
        .then(response => response.json()) // Parse the JSON from controller
        .then(data => {
            if (data.success) {
                // Success! Close loader and move to next step
                Swal.fire({
                    icon: 'success',
                    title: 'Photo Uploaded!',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500
                });
                
                // MOVE TO NEXT STEP AUTOMATICALLY
                goToStep(3); 
            } else {
                Swal.fire({icon: 'error', title: 'Upload Failed', text: data.message || 'Something went wrong'});
            }
        })
        .catch(error => {
            console.error(error);
            Swal.fire({icon: 'error', title: 'Error', text: 'Network error occurred.'});
        });
    });
    const video = document.getElementById('camera_preview');
    const canvas = document.getElementById('snapshot_canvas');
    const startBtn = document.getElementById('start_camera');
    
    startBtn.addEventListener('click', async () => {
        try {
            stream = await navigator.mediaDevices.getUserMedia({video:true});
            video.srcObject = stream;
            video.classList.remove('hidden');
            document.getElementById('camera_placeholder').classList.add('hidden');
            startBtn.classList.add('hidden');
            document.getElementById('camera_controls').classList.remove('hidden');
        } catch(e) { Swal.fire({icon:'error', text:'Camera error.'}); }
    });

    document.getElementById('take_snapshot').addEventListener('click', () => {
        canvas.width = video.videoWidth; canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.translate(canvas.width, 0); ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        selfieDataUrl = canvas.toDataURL('image/png');
        video.classList.add('hidden'); canvas.classList.remove('hidden');
        document.getElementById('take_snapshot').classList.add('hidden');
        document.getElementById('confirm_snapshot').classList.remove('hidden');
        document.getElementById('retake_snapshot').classList.remove('hidden');
    });

    document.getElementById('retake_snapshot').addEventListener('click', () => {
        canvas.classList.add('hidden'); video.classList.remove('hidden');
        document.getElementById('take_snapshot').classList.remove('hidden');
        document.getElementById('confirm_snapshot').classList.add('hidden');
        document.getElementById('retake_snapshot').classList.add('hidden');
    });

    document.getElementById('confirm_snapshot').addEventListener('click', () => {
        Swal.fire({title:'Verifying...', didOpen:()=>Swal.showLoading()});
        fetch("{{ route('students_verification.upload_selfie') }}", {
            method:'POST', body:JSON.stringify({selfie_image: selfieDataUrl}),
            headers:{'Content-Type':'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}"}
        }).then(r=>{Swal.close(); goToStep('success');})
        .catch(e=>{Swal.close(); goToStep('success');});
    });
    </script>
    <style>.animate-fadeIn{animation:fadeIn 0.5s ease-out forwards}@keyframes fadeIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}.animate-bounce-short{animation:bounceShort 2s infinite}@keyframes bounceShort{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}</style>
    @endif
</x-guest-layout>