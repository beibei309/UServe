<x-app-layout>
    <div class="bg-base-100">
        <!-- Top bar with UPSI Blue -->
        <div class="w-full bg-upsi-blue text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                <h1 class="text-xl font-semibold">Discover Services</h1>
                <div class="flex items-center space-x-2">
                    <input type="text" placeholder="Search services, skills, students" class="input input-bordered w-64" />
                    <button class="btn btn-primary">Search</button>
                </div>
            </div>
        </div>

        <!-- Categories -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h2 class="text-2xl font-bold text-upsi-dark mb-4">Categories</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach (['Tutoring','Design','Tech Support','Photography','Music','Events','Writing','Fitness'] as $cat)
                    <div class="card bg-white border border-gray-200">
                        <div class="card-body">
                            <div class="flex items-center justify-between">
                                <span class="font-medium text-upsi-dark">{{ $cat }}</span>
                                <span class="badge badge-outline" style="border-color:#003B73;color:#003B73;">Browse</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Featured Students -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
            <h2 class="text-2xl font-bold text-upsi-dark mb-4">Featured Students</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @for ($i = 1; $i <= 6; $i++)
                    <div class="card bg-white border border-gray-200">
                        <div class="card-body">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <h3 class="font-semibold text-upsi-dark">Student {{ $i }}</h3>
                                    <p class="text-sm text-upsi-text-primary">Offers: Tutoring, Design</p>
                                </div>
                                <span class="badge bg-upsi-gold text-upsi-dark font-semibold">Pelajar UPSI Terkini</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs text-upsi-text-primary">Rating</span>
                                    <div class="rating rating-sm">
                                        <input type="radio" class="mask mask-star-2 bg-upsi-gold" />
                                        <input type="radio" class="mask mask-star-2 bg-upsi-gold" checked />
                                        <input type="radio" class="mask mask-star-2 bg-upsi-gold" checked />
                                        <input type="radio" class="mask mask-star-2 bg-upsi-gold" checked />
                                        <input type="radio" class="mask mask-star-2 bg-upsi-gold" />
                                    </div>
                                </div>
                                <a href="{{ route('students.profile', ['user' => $i]) }}" class="btn" style="background-color:#003B73;color:#FFFFFF;">View Profile</a>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</x-app-layout>