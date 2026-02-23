<x-guest-layout>
    <div class="bg-base-100 p-6 sm:p-8">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-upsi-dark">Create your account</h1>
                <p class="text-upsi-text-primary mt-2">Choose your path to get started</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- UPSI Student Path -->
                <div class="card bg-white border border-gray-200 shadow-sm">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <h2 class="card-title text-upsi-blue">I am a UPSI Student</h2>
                            <span class="badge bg-upsi-gold text-upsi-dark font-semibold">Pelajar UPSI Terkini</span>
                        </div>
                        <p class="text-upsi-text-primary">Register with your UPSI identity for trusted student services.</p>
                        <div class="space-y-2 text-sm text-upsi-text-primary">
                            <div class="flex items-center"><span class="badge badge-sm bg-upsi-gold text-upsi-dark font-semibold">Gold</span><span class="ml-2">Verified student badge</span></div>
                            <div class="flex items-center"><span class="badge badge-sm badge-outline">Secure account</span><span class="ml-2">Strong verification and privacy</span></div>
                        </div>
                        <div class="card-actions justify-end mt-4">
                            <a href="{{ route('register') }}?role=student" class="btn btn-primary">Continue</a>
                            <a href="{{ route('login') }}" class="btn btn-outline" style="border-color:#003B73;color:#003B73;">Sign In</a>
                        </div>
                    </div>
                </div>

                <!-- Community Path -->
                <div class="card bg-white border border-gray-200 shadow-sm">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <h2 class="card-title text-upsi-blue">I am Community</h2>
                            <span class="badge" style="background-color:#D4AF37;color:#1F2937;">Pengguna Disahkan</span>
                        </div>
                        <p class="text-upsi-text-primary">Register as a community user to request and review student services.</p>
                        <div class="space-y-2 text-sm text-upsi-text-primary">
                            <div class="flex items-center"><span class="badge badge-sm" style="background-color:#D4AF37;color:#1F2937;">Gold</span><span class="ml-2">Verified community badge</span></div>
                            <div class="flex items-center"><span class="badge badge-sm badge-outline">Secure verification</span><span class="ml-2">Phone OTP & selfie check</span></div>
                        </div>
                        <div class="card-actions justify-end mt-4">
                            <a href="{{ route('onboarding.community.verify') }}" class="btn btn-primary">Continue</a>
                            <a href="{{ route('login') }}" class="btn btn-outline" style="border-color:#003B73;color:#003B73;">Sign In</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-10">
                <div class="alert" style="background-color:#F3F4F6;color:#1F2937;">
                    <div>
                    <span class="badge mr-2 bg-upsi-gold text-upsi-dark font-semibold">Trust</span>
                        UpsiConnect uses UPSI Corporate Trust standards to keep accounts safe.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>