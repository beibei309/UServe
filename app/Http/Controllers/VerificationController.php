<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VerificationController extends Controller
{
    private const MUALLIM_CENTER_LAT = 3.7832;
    private const MUALLIM_CENTER_LNG = 101.5927;
    private const MUALLIM_RADIUS_KM = 25;

   public function index() {
    $user = Auth::user();
    
    // 1. Redirect if already helper
    if ($user->hu_role === 'helper') {
        return redirect()->route('dashboard')->with('info', 'You are already a verified helper!');
    }
    
    // 2. Fetch Real Student Status from DB
    $studentStatus = DB::table('h2u_student_statuses')
                        ->where('hss_student_id', $user->hu_id)
                        ->latest()
                        ->first();

    // 3. Initialize Status Variables
    $isEligible = false;
    $statusMessage = 'Checking...';
    $statusColor = 'gray';
    $reason = '';
    $matricNo = $user->hu_student_id ?? 'N/A'; // Fallback if no matric no
    $gradDateDisplay = 'N/A';

    // 4. Run Eligibility Logic
    if ($studentStatus) {
        $matricNo = $studentStatus->hss_matric_no;
        $today = \Carbon\Carbon::now();
        $gradDate = $studentStatus->hss_graduation_date ? \Carbon\Carbon::parse($studentStatus->hss_graduation_date) : null;
        $gradDateDisplay = $gradDate ? $gradDate->format('d M Y') : 'Not Set';
        
        // Rule A: Status must be Active (case-insensitive)
        $isActive = strtolower($studentStatus->hss_status) === 'active';
        
        // Rule B: Graduation must be > 3 months away
        $isNotGraduatingSoon = true;
        if ($gradDate) {
            // Returns float (e.g., 2.5 months)
            $monthsUntilGrad = $today->floatDiffInMonths($gradDate, false);
            
            // IF date is in past OR less than 3 months in future -> Block
            if ($monthsUntilGrad < 3) {
                $isNotGraduatingSoon = false;
            }
        }

        if (!$isActive) {
            $isEligible = false;
            $statusMessage = 'Inactive (' . $studentStatus->hss_status . ')';
            $statusColor = 'red';
            $reason = 'Your student status is currently set to ' . $studentStatus->hss_status . '. Please contact admin.';
        } elseif (!$isNotGraduatingSoon) {
            $isEligible = false;
            $statusMessage = 'Graduating Soon';
            $statusColor = 'orange';
            $reason = "You are too close to graduation ($gradDateDisplay). You must have at least 3 months remaining to register as a helper.";
        } else {
            // PASS
            $isEligible = true;
            $statusMessage = 'Active Student';
            $statusColor = 'green';
        }
    } else {
        // --- NO RECORD FOUND SCENARIO ---
        // For development/testing, you might want to allow this.
        // Change $isEligible to true if you want to allow users without status records.
        $isEligible = true; 
        $statusMessage = 'Active (No Record)'; 
        $statusColor = 'green';
        $reason = 'No specific status record found, assuming active.';
    }

    // 5. Pass variables to the view
    return view('onboarding.students_verification', compact(
        'isEligible', 
        'statusMessage', 
        'statusColor', 
        'reason', 
        'matricNo',
        'gradDateDisplay'
    )); 
}

   public function uploadPhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|max:4096'
        ]);

        $user = Auth::user();

        if ($request->hasFile('profile_photo')) {

            // 1. Delete old image if it exists
            if ($user->hu_profile_photo_path && file_exists(public_path($user->hu_profile_photo_path))) {
                unlink(public_path($user->hu_profile_photo_path));
            }

            $file = $request->file('profile_photo');
            $filename = $file->hashName();

            // 2. Make sure folder exists
            if (!file_exists(public_path('profile-photos'))) {
                mkdir(public_path('profile-photos'), 0755, true);
            }

            // 3. Move file directly to public/profile-photos
            $file->move(public_path('profile-photos'), $filename);

            // 4. Save relative path to DB
            $user->hu_profile_photo_path = 'profile-photos/' . $filename;
            
            // Only save if the move was successful
            $user->save();
        }

        // Return JSON for AJAX requests
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Profile photo uploaded!']);
        }

        return redirect()->back()->with('status', 'Profile photo uploaded!');
    }
    // --- STUDENT HELPER VERIFICATION METHOD ---
    
    public function uploadSelfie(Request $request)
    {
        $request->validate([
            'selfie_image' => 'required'
        ]);

        ini_set('memory_limit', '256M');
        ini_set('max_execution_time', 300);

        \Illuminate\Support\Facades\Log::info('Student Helper Selfie Upload Started for user: ' . Auth::id());

        $user = Auth::user();
        
        try {
            $image = $request->input('selfie_image'); // Base64 string
            \Illuminate\Support\Facades\Log::info('Selfie payload received. Length: ' . strlen($image));

            // Convert base64 to image file
            $image = preg_replace('/^data:image\/\w+;base64,/', '', $image);
            $image = str_replace(' ', '+', $image);
            
            $decodedImage = base64_decode($image);
            if ($decodedImage === false) {
                 throw new \Exception('Base64 decode failed');
            }

            $imageName = 'helper_selfie_' . $user->hu_id . '_' . time() . '.jpg';
            
            \Illuminate\Support\Facades\Log::info('Saving helper selfie to: uploads/verification/' . $imageName);
            
            // Store in 'local' disk (private storage)
            Storage::disk('local')->put('uploads/verification/' . $imageName, $decodedImage);
            
            if (!Storage::disk('local')->exists('uploads/verification/' . $imageName)) {
                throw new \Exception('Failed to verify file existence after write.');
            }

            // Update user with selfie path
            $user->hu_selfie_media_path = 'uploads/verification/' . $imageName;
            
            // Convert student to helper role
            $user->hu_role = 'helper';
            $user->hu_helper_verified_at = now();
            
            $user->save();

            \Illuminate\Support\Facades\Log::info('Student converted to helper. User ID: ' . $user->hu_id);

            return response()->json([
                'success' => true, 
                'message' => 'Verification complete! You are now a helper.',
                'redirect' => route('dashboard') . '?mode=seller'
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Helper Selfie Upload Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()], 500);
        }
    }

    // Save verified location data
    public function saveLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'nullable|string|max:500'
        ]);

        $distanceKm = $this->distanceKm(
            (float) $request->latitude,
            (float) $request->longitude,
            self::MUALLIM_CENTER_LAT,
            self::MUALLIM_CENTER_LNG
        );

        if ($distanceKm > self::MUALLIM_RADIUS_KM) {
            \Illuminate\Support\Facades\Log::warning('Location verification rejected (outside Muallim District)', [
                'user_id' => Auth::id(),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'distance_km' => round($distanceKm, 2),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Location must be inside Muallim District.'
            ], 422);
        }

        $user = Auth::user();
        
        $user->update([
            'hu_latitude' => $request->latitude,
            'hu_longitude' => $request->longitude,
            'address' => $request->address,
            'hu_location_verified_at' => now()
        ]);

        \Illuminate\Support\Facades\Log::info('Auto-detected location verified and saved (Muallim District)', [
            'user_id' => $user->hu_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'distance_km' => round($distanceKm, 2),
        ]);

        return response()->json(['success' => true, 'message' => 'Location verified and saved!']);
    }

    private function distanceKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadiusKm = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }

    // --- COMMUNITY VERIFICATION METHODS ---

    public function uploadCommunitySelfie(Request $request)
    {
        $request->validate([
            'selfie_image' => 'required'
        ]);

        // PREVENT TIMEOUTS/MEMORY ISSUES
        ini_set('memory_limit', '256M');
        ini_set('max_execution_time', 300);

        \Illuminate\Support\Facades\Log::info('Community Selfie Upload Started for user: ' . Auth::id());

        $user = Auth::user();
        try {
            $image = $request->input('selfie_image'); // Base64 string
            \Illuminate\Support\Facades\Log::info('Selfie payload received. Length: ' . strlen($image));

            // Convert base64 to image file
            $image = preg_replace('/^data:image\/\w+;base64,/', '', $image);
            $image = str_replace(' ', '+', $image);
            
            $decodedImage = base64_decode($image);
            if ($decodedImage === false) {
                 throw new \Exception('Base64 decode failed');
            }

            $imageName = 'selfie_' . $user->hu_id . '_' . time() . '.jpg';
            
            \Illuminate\Support\Facades\Log::info('Saving selfie to: uploads/verification/' . $imageName);
            
            // PRIVACY: Store in 'local' disk (private) instead of public
            Storage::disk('local')->put('uploads/verification/' . $imageName, $decodedImage);
            
            if (!Storage::disk('local')->exists('uploads/verification/' . $imageName)) {
                throw new \Exception('Failed to verify file existence after write.');
            }

            $user->hu_selfie_media_path = 'uploads/verification/' . $imageName;
            // Save the challenge note (e.g. "Peace Sign")
            $user->hu_verification_note = $request->input('verification_note');
            $user->save();

            return response()->json(['success' => true, 'message' => 'Selfie verified & uploaded!']);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Selfie Upload Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()], 500);
        }
    }

    

    public function submitDoc(Request $request)
    {
        $request->validate([
            'verification_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // Max 5MB
        ]);

        $user = Auth::user();

        if ($request->hasFile('verification_document')) {
            $file = $request->file('verification_document');
            
            // Check if file is valid
            if ($file && $file->isValid()) {
                $filename = 'verify_' . $user->hu_id . '_' . $file->hashName();
                
                // Store in storage/app/private/verification_docs (local disk)
                $path = $file->storeAs('verification_docs', $filename, 'local');

                if (!$path || !Storage::disk('local')->exists($path)) {
                    return redirect()->back()->withErrors(['verification_document' => 'Upload failed. Please try again.']);
                }

                // Update User
                $user->hu_verification_document_path = $path;
                $user->hu_verification_status = 'approved'; 
                $user->save();

                return redirect()->back()->with('success', 'Verification Successfully!');
            } else {
                return redirect()->back()->withErrors(['verification_document' => 'Invalid file uploaded.']);
            }
        }

        return redirect()->back()->withErrors(['verification_document' => 'Upload failed. Please try again.']);
    }

    // Keep original submit for backward compatibility if needed, but we use submitDoc now
    public function submit(Request $request) { return $this->submitDoc($request); }
}