<x-guest-layout>
    <div class="fixed inset-0 w-full h-full overflow-hidden z-0">
        <video autoplay muted loop playsinline class="absolute min-w-full min-h-full object-cover">
            <source src="{{ asset('videos/background-myupsi-small.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        {{-- Dark Overlay --}}
        <div class="absolute inset-0 bg-black/60"></div>
    </div>

    <div class="relative z-10 min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        
        <div class="mb-6 mt-16">
            <a href="/" class="flex items-center gap-2 group">
                <img src="{{ asset('images/logo.png') }}" alt="Logo"
                    class="w-25 h-20 rounded-xl shadow-lg group-hover:opacity-90 transition">
            </a>
        </div>

        <div class="w-full sm:max-w-xl mt-6 px-6 py-8 bg-white shadow-xl rounded-2xl border border-gray-100">
            
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Create an Account</h2>
                <p class="mt-2 text-sm text-gray-500">Join the community to connect, learn, and earn.</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-5" x-data="{ role: '{{ old('role', 'student') }}', communityType: '{{ old('community_type', 'public') }}' }">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">I am registering as:</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="relative flex flex-col p-4 bg-white border rounded-xl cursor-pointer transition-all hover:shadow-md"
                            :class="role === 'student' ? 'border-indigo-600 ring-1 ring-indigo-600 bg-indigo-50/50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" name="role" value="student" x-model="role" class="sr-only">
                            <span class="flex items-center gap-2 mb-1">
                                <span class="font-semibold text-gray-900">Student</span>
                                <span class="bg-indigo-100 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded-full">UPSI</span>
                            </span>
                            <span class="text-xs text-gray-500">I want to offer services or find help.</span>
                            <div x-show="role === 'student'" class="absolute top-4 right-4 text-indigo-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            </div>
                        </label>

                        <label class="relative flex flex-col p-4 bg-white border rounded-xl cursor-pointer transition-all hover:shadow-md"
                            :class="role === 'community' ? 'border-indigo-600 ring-1 ring-indigo-600 bg-indigo-50/50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" name="role" value="community" x-model="role" class="sr-only">
                            <span class="flex items-center gap-2 mb-1">
                                <span class="font-semibold text-gray-900">Community</span>
                                <span class="bg-yellow-100 text-yellow-700 text-[10px] font-bold px-2 py-0.5 rounded-full">Public/Staff</span>
                            </span>
                            <span class="text-xs text-gray-500">I want to hire students for tasks.</span>
                            <div x-show="role === 'community'" class="absolute top-4 right-4 text-indigo-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            </div>
                        </label>
                    </div>
                    <x-input-error :messages="$errors->get('role')" class="mt-2 text-red-500 text-xs" />
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors"
                            placeholder="John Doe">
                        <x-input-error :messages="$errors->get('name')" class="mt-1 text-red-500 text-xs" />
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input id="email" type="email" name="email" :value="old('email')" required autocomplete="username"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors"
                            placeholder="email@mail.com">
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-red-500 text-xs" />
                    </div>

                    
                </div>

                <div x-show="role === 'student'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                     class="bg-indigo-50 p-4 rounded-xl border border-indigo-100 space-y-4">
                    
                    <h3 class="text-xs font-bold text-indigo-800 uppercase tracking-wider mb-2">Student Verification</h3>
                    
                    <div>
                        <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">Student ID</label>
                        <input id="student_id" type="text" name="student_id" :value="old('student_id')" 
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                            placeholder="D202XXXXXX">
                        <x-input-error :messages="$errors->get('student_id')" class="mt-1 text-red-500 text-xs" />
                    </div>
                    
                    <div class="flex items-start gap-3 bg-white/60 p-3 rounded-lg border border-indigo-200">
                         <svg class="w-5 h-5 text-indigo-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                         <p class="text-xs text-indigo-800 leading-snug">
                             You must use your <b>@siswa.upsi.edu.my</b> email in the "Email Address" field above for instant processing.
                         </p>
                    </div>
                </div>
                <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input id="phone" type="tel" name="phone" :value="old('phone')" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors"
                            placeholder="0123456789">
                        <x-input-error :messages="$errors->get('phone')" class="mt-1 text-red-500 text-xs" />
                    </div>

                <div x-show="role === 'community'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                     class="bg-yellow-50 p-4 rounded-xl border border-yellow-100 space-y-4">
                    
                    <h3 class="text-xs font-bold text-yellow-800 uppercase tracking-wider mb-2">Community Details</h3>

                    <div>
                        <span class="block text-sm font-medium text-gray-700 mb-2">I am a:</span>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="community_type" value="public" x-model="communityType" class="text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">Public User</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="community_type" value="staff" x-model="communityType" class="text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">UPSI Staff</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 bg-white/60 p-3 rounded-lg border border-yellow-200">
                        <svg class="w-5 h-5 text-yellow-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                        <p class="text-xs text-yellow-800 leading-snug">
                            <span x-show="communityType === 'staff'">Staff: Use your <b>@upsi.edu.my</b> email above for auto-verification.</span>
                            <span x-show="communityType === 'public'">Public: You will need to upload proof of ID/Residence.</span>
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                            placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-red-500 text-xs" />
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                            placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-red-500 text-xs" />
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="terms" type="checkbox" required class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer">
                    <label for="terms" class="ml-2 text-sm text-gray-600">
                        I agree to the <a href="{{ route('terms') }}" class="text-indigo-600 hover:underline">Terms</a> and <a href="{{ route('privacy') }}" class="text-indigo-600 hover:underline">Privacy Policy</a>.
                    </label>
                </div>

                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
                    Create Account
                </button>

                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-3 bg-white text-gray-500 font-medium">Already have an account?</span>
                    </div>
                </div>

                <a href="{{ route('login') }}" class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                    Sign In
                </a>
            </form>
        </div>

        <div class="mt-8 mb-8 text-center">
            {{-- Updated text color to gray-200 for better visibility over dark video --}}
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-200 hover:text-white transition-colors group">
                <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Home
            </a>
        </div>
    </div>
</x-guest-layout>