<x-guest-layout>
    <div class="max-w-4xl mx-auto py-8 px-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Privacy Policy</h1>
            
            <div class="prose max-w-none">
                <h2 class="text-xl font-semibold text-gray-900 mt-6 mb-4">1. Information We Collect</h2>
                <p class="text-gray-700 mb-4">
                    We collect information you provide directly to us, such as when you create an account, update your profile, or communicate with other users.
                </p>

                <h2 class="text-xl font-semibold text-gray-900 mt-6 mb-4">2. How We Use Your Information</h2>
                <p class="text-gray-700 mb-4">
                    We use the information we collect to provide, maintain, and improve our services, facilitate connections between students and community members, and ensure platform safety.
                </p>

                <h2 class="text-xl font-semibold text-gray-900 mt-6 mb-4">3. Information Sharing</h2>
                <p class="text-gray-700 mb-4">
                    We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except as described in this policy.
                </p>

                <h2 class="text-xl font-semibold text-gray-900 mt-6 mb-4">4. Data Security</h2>
                <p class="text-gray-700 mb-4">
                    We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.
                </p>

                <h2 class="text-xl font-semibold text-gray-900 mt-6 mb-4">5. Student Verification</h2>
                <p class="text-gray-700 mb-4">
                    For UPSI students, we may verify your student status using institutional data. This information is used solely for verification purposes.
                </p>

                <h2 class="text-xl font-semibold text-gray-900 mt-6 mb-4">6. Community Verification</h2>
                <p class="text-gray-700 mb-4">
                    Community members undergo phone verification and photo verification to ensure platform safety and trust.
                </p>

                <h2 class="text-xl font-semibold text-gray-900 mt-6 mb-4">7. Your Rights</h2>
                <p class="text-gray-700 mb-4">
                    You have the right to access, update, or delete your personal information. You may also opt out of certain communications from us.
                </p>

                <h2 class="text-xl font-semibold text-gray-900 mt-6 mb-4">8. Contact Us</h2>
                <p class="text-gray-700 mb-4">
                    If you have questions about this Privacy Policy, please contact us through the platform's support system.
                </p>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-500">Last updated: {{ date('F j, Y') }}</p>
                <div class="mt-4">
                    <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        ← Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>