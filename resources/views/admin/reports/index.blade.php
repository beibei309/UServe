@extends('admin.layout')

@section('content')
    <div class="px-4 md:px-0">

        <h1 class="text-2xl sm:text-3xl font-bold transition-colors duration-300" style="color: var(--text-primary);">
            User Reports
        </h1>
        <p class="mt-1 font-medium transition-colors duration-300" style="color: var(--text-secondary);">
            Review and action complaints submitted by platform users.
        </p>

        <div class="mt-8 space-y-4">
            @forelse($reports as $report)
                <div id="report-{{ $report->hrp_id }}" class="report-card p-5 rounded-2xl border shadow-sm transition-all duration-300"
                     style="background-color: var(--bg-secondary); border-color: var(--border-color);">

                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                        <div class="space-y-1">
                            <div class="font-semibold transition-colors duration-300" style="color: var(--text-primary);">
                                Reporter: <span class="text-cyan-400">{{ optional($report->reporter)->hu_name ?? '—' }}</span>
                                <span class="mx-2 opacity-40">→</span>
                                Target: <span class="text-red-400">{{ optional($report->target)->hu_name ?? '—' }}</span>
                            </div>
                            <div class="text-sm transition-colors duration-300" style="color: var(--text-secondary);">
                                <span class="font-medium">Reason:</span> {{ $report->hrp_reason }}
                            </div>
                            @if($report->hrp_details)
                                <div class="text-sm transition-colors duration-300" style="color: var(--text-muted);">
                                    <span class="font-medium">Details:</span> {{ $report->hrp_details }}
                                </div>
                            @endif
                            <div class="text-xs transition-colors duration-300" style="color: var(--text-muted);">
                                Reported {{ $report->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full badge-yellow self-start">
                            Open
                        </span>
                    </div>

                    <form class="report-resolve-form mt-4 flex flex-col sm:flex-row sm:items-center gap-3"
                          data-report-id="{{ $report->hrp_id }}"
                          data-url="{{ route('admin.reports.resolve', $report->hrp_id) }}">
                        @csrf

                        <select name="status" required
                                class="w-full sm:w-auto px-3 py-2 rounded-lg border text-sm transition-colors duration-300"
                                style="background-color: var(--input-bg); color: var(--text-primary); border-color: var(--border-color);">
                            <option value="warning">Warning</option>
                            <option value="banned">Banned</option>
                            <option value="resolved">Resolved</option>
                            <option value="rejected">Rejected</option>
                        </select>

                        <input type="text" name="action_taken" placeholder="Action taken (optional)"
                               class="w-full sm:flex-1 px-3 py-2 rounded-lg border text-sm transition-colors duration-300"
                               style="background-color: var(--input-bg); color: var(--text-primary); border-color: var(--border-color);">

                        <button type="submit"
                                class="px-4 py-2 rounded-lg font-semibold bg-indigo-600 hover:bg-indigo-700 text-white transition">
                            Update Report
                        </button>
                    </form>
                </div>
            @empty
                <div class="p-6 rounded-2xl border text-center" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                    <p class="font-medium" style="color: var(--text-secondary);">No open reports right now.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@section('scripts')
<script src="{{ asset('js/admin-reports-index.js') }}"></script>
@endsection
