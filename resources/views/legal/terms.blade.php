<x-guest-layout>
    <div class="max-w-4xl mx-auto py-8 px-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Terms of Service</h1>
            
            <div class="prose max-w-none">
                <h2 class="text-xl font-semibold text-gray-900 mt-6 mb-4">1. Acceptance of Terms</h2>
                <p class="text-gray-700 mb-4">
                    By accessing and using UpsiConnect, you accept and agree to be bound by the terms and provision of this agreement.
                </p>

                <h2 class="text-xl font-semibold text-gray-900 mt-6 mb-4">2. Use License</h2>
                <p class="text-gray-700 mb-4">
                    Permission is granted to temporarily use UpsiConnect for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title.
                </p>

                <h2 class="text-xl font-semibold text-gray-900 mt-6 mb-4">3. User Accounts</h2>
                <p class="text-gray-700 mb-4">
                    Users are responsible for maintaining the confidentiality of their account information and for all activities that occur under their account.
                </p>

                <h2 class="text-xl font-semibold text-gray-900 mt-6 mb-4">4. Student Services</h2>
                <p class="text-gray-700 mb-4">
                    UpsiConnect facilitates connections between UPSI students and community members. We do not guarantee the quality of services provided by students.
                </p>

                <h2 class="text-xl font-semibold text-gray-900 mt-6 mb-4">5. Community Guidelines</h2>
                <p class="text-gray-700 mb-4">
                    All users must maintain respectful communication and follow community standards. Inappropriate behavior may result in account suspension.
                </p>

                <h2 class="text-xl font-semibold text-gray-900 mt-6 mb-4">6. Contact Information</h2>
                <p class="text-gray-700 mb-4">
                    For questions about these Terms of Service, please contact us through the platform's support system.
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