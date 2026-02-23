<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Open Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="space-y-4">
                        @forelse($reports as $report)
                            <div class="border rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="font-semibold">Reporter: {{ optional($report->reporter)->name }} | Target: {{ optional($report->target)->name }}</div>
                                        <div class="text-sm text-gray-600">Reason: {{ $report->reason }}</div>
                                        <div class="text-sm">Details: {{ $report->details }}</div>
                                    </div>
                                    <div class="text-sm">Status: {{ $report->status }}</div>
                                </div>
                                <div class="mt-3">
                                    <form method="POST" action="{{ url('/admin/reports/'.$report->id.'/resolve') }}" class="flex items-center gap-2">
                                        @csrf
                                        <select name="status" class="rounded border-gray-300 text-sm">
                                            <option value="warning">Warning</option>
                                            <option value="banned">Ban</option>
                                            <option value="resolved">Resolve</option>
                                            <option value="rejected">Reject</option>
                                        </select>
                                        <input type="text" name="actions_taken" placeholder="Actions taken" class="rounded border-gray-300 text-sm">
                                        <x-primary-button>Update</x-primary-button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p>No open reports.</p>
                        @endforelse
                    </div>
                    <div class="mt-6">
                        {{ $reports->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>