@extends('admin.layout')

@section('content')
<div class="px-4 sm:px-6">

    <div class="max-w-4xl mx-auto">
        
        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h1 class="text-3xl font-bold transition-colors duration-300" style="color: var(--text-primary);">Student Status Details</h1>
            <a href="{{ route('admin.student_status.index') }}"
               class="transition-colors duration-300 text-sm hover:text-cyan-400"
               style="color: var(--text-secondary);">
                ← Back to List
            </a>
        </div>

        {{-- STUDENT INFO CARD --}}
        <div class="rounded-lg shadow border p-6 mb-6 transition-all duration-300"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- STUDENT DETAILS --}}
                <div>
                    <h3 class="text-lg font-semibold mb-4 transition-colors duration-300" style="color: var(--text-primary);">Student Information</h3>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="font-medium transition-colors duration-300" style="color: var(--text-primary);">Name:</span>
                            <span class="transition-colors duration-300" style="color: var(--text-secondary);">{{ $status->student->hu_name }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="font-medium transition-colors duration-300" style="color: var(--text-primary);">Matric No:</span>
                            <span class="transition-colors duration-300" style="color: var(--text-secondary);">{{ $status->student->hu_student_id ?? '-' }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="font-medium transition-colors duration-300" style="color: var(--text-primary);">Faculty:</span>
                            <span class="transition-colors duration-300" style="color: var(--text-secondary);">{{ $status->student->hu_faculty ?? '-' }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="font-medium transition-colors duration-300" style="color: var(--text-primary);">Course:</span>
                            <span class="transition-colors duration-300" style="color: var(--text-secondary);">{{ $status->student->hu_course ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- STATUS DETAILS --}}
                <div>
                    <h3 class="text-lg font-semibold mb-4 transition-colors duration-300" style="color: var(--text-primary);">Academic Status</h3>
                    
                    <div class="space-y-4">
                        
                        {{-- STATUS BADGE --}}
                        <div>
                            <span class="text-sm font-medium transition-colors duration-300" style="color: var(--text-primary);">Status:</span>
                            @php $statusLower = strtolower($status->hss_status); @endphp
                            
                            @if($statusLower == 'active')
                                <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            @elseif($statusLower == 'probation')
                                <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Probation
                                </span>
                            @elseif($statusLower == 'graduated')
                                <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Graduated
                                </span>
                            @elseif($statusLower == 'deferred')
                                <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Deferred
                                </span>
                            @else
                                <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst($status->hss_status) }}
                                </span>
                            @endif
                        </div>
                        
                        {{-- SEMESTER --}}
                        <div class="text-sm">
                            <span class="font-medium transition-colors duration-300" style="color: var(--text-primary);">Semester:</span>
                            <span class="transition-colors duration-300" style="color: var(--text-secondary);">{{ $status->hss_semester ?? '-' }}</span>
                        </div>
                        
                        {{-- GRADUATION DATE --}}
                        <div class="text-sm">
                            <span class="font-medium transition-colors duration-300" style="color: var(--text-primary);">Graduation Date:</span>
                            <span class="transition-colors duration-300" style="color: var(--text-secondary);">{{ $status->hss_graduation_date ? \Carbon\Carbon::parse($status->hss_graduation_date)->format('d M Y') : '-' }}</span>
                        </div>
                        
                        {{-- TIMESTAMPS --}}
                        <div class="text-xs pt-3 border-t transition-colors duration-300" style="border-color: var(--border-color);">
                            <div class="mb-1">
                                <span class="font-medium transition-colors duration-300" style="color: var(--text-primary);">Created:</span>
                                <span class="transition-colors duration-300" style="color: var(--text-muted);">{{ $status->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            <div>
                                <span class="font-medium transition-colors duration-300" style="color: var(--text-primary);">Updated:</span>
                                <span class="transition-colors duration-300" style="color: var(--text-muted);">{{ $status->updated_at->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- ACTION BUTTONS --}}
            <div class="mt-6 pt-6 border-t flex flex-col sm:flex-row gap-3 transition-colors duration-300"
                 style="border-color: var(--border-color);">
                <a href="{{ route('admin.student_status.edit', $status->hss_id) }}"
                   class="inline-flex items-center justify-center px-4 py-2 bg-cyan-500 text-white font-medium rounded hover:bg-cyan-600 transition-colors duration-300">
                    <i class="fa-solid fa-edit mr-2"></i>
                    Edit Status
                </a>
                
                <form action="{{ route('admin.student_status.delete', $status->hss_id) }}" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            onclick="return confirm('Are you sure you want to delete this status record?')"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border text-red-500 font-medium rounded hover:text-red-400 transition-colors duration-300"
                            style="background-color: var(--bg-primary); border-color: var(--border-color);">
                        <i class="fa-solid fa-trash mr-2"></i>
                        Delete Record
                    </button>
                </form>
            </div>
        </div>
        
    </div>

</div>
@endsection
