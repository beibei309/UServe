@extends('admin.layout')

@section('content')

<a href="{{ route('admin.student_status.index') }}" 
   class="text-blue-600 hover:underline mb-4 inline-block">← Back</a>

<div class="bg-white shadow rounded-lg p-6 max-w-3xl">

    <h1 class="text-3xl font-bold mb-6">Student Status Details</h1>

    <div class="space-y-4">

        <p><strong>Name:</strong> {{ $status->student->name }}</p>
        <p><strong>Matric No:</strong> {{ $status->student->student_id }}</p>
        <p><strong>Semester:</strong> {{ $status->semester }}</p>

        <p>
            <strong>Status:</strong>
            @if($status->status == 'active')
                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full">Active</span>
            @else
                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full">Inactive</span>
            @endif
        </p>

    </div>

</div>

@endsection
