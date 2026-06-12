<x-guest-layout>
    @php
        $supportEmail = \App\Models\PageContent::get('settings.support_email', 'support@upsi2u.upsi.edu.my');
        $supportHours = \App\Models\PageContent::get('settings.support_hours', 'Mon-Fri, 8AM-5PM');
    @endphp
    <div x-data="{
        selected: null,
        search: '',
        toggle(id) {
            this.selected = this.selected === id ? null : id
        }
    }"
        class="min-h-screen bg-slate-50/50 py-8 sm:py-12 px-4 sm:px-6 lg:px-8 font-sans text-slate-800 relative overflow-hidden">

        <div class="max-w-4xl mx-auto relative z-10">

            {{-- HEADER & SEARCH --}}
            <div class="text-center mb-8 sm:mb-12">
                <nav class="flex justify-center mb-4">
                    <span
                        class="px-3 py-1 text-xs font-bold tracking-widest uppercase bg-indigo-50 text-indigo-600 rounded-full border border-indigo-100">
                        UPSI2u Help Center
                    </span>
                </nav>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 tracking-tight mb-4">
                    How can we <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">help
                        you?</span>
                </h1>


            </div>

            {{-- FAQ CONTENT --}}
            <div class="space-y-8 sm:space-y-10">
                @foreach ($faqs as $category => $items)
                    <section>
                        {{-- Category Title with Icon --}}
                        <div class="flex items-center gap-3 mb-4 px-1 sm:px-2">
                            <div class="w-8 h-8 rounded-lg bg-slate-900 flex items-center justify-center text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <h2 class="text-sm sm:text-base font-bold text-slate-800 uppercase tracking-wider">{{ $category }}
                            </h2>
                        </div>

                        <div class="grid grid-cols-1 gap-3">
                            @foreach ($items as $faq)
                                <div class="group"
                                    x-show="search === '' || '{{ strtolower($faq->hfq_question) }}'.includes(search.toLowerCase())">
                                    <div class="upsi-card upsi-card-hover overflow-hidden"
                                        :class="selected === {{ $faq->hfq_id }} ?
                                            'ring-2 ring-indigo-500/10 border-indigo-200 shadow-lg' : ''">

                                        <button @click="toggle({{ $faq->hfq_id }})"
                                            class="w-full px-4 sm:px-5 py-4 text-left flex justify-between items-center gap-3 focus:outline-none">
                                            <span class="font-bold text-sm sm:text-base text-slate-700 transition-colors"
                                                :class="selected === {{ $faq->hfq_id }} ? 'text-indigo-600' :
                                                    'group-hover:text-slate-900'">
                                                {{ $faq->hfq_question }}
                                            </span>
                                            <span
                                                class="flex-shrink-0 w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center group-hover:bg-indigo-50 transition-colors">
                                                <svg class="w-4 h-4 text-slate-400 transition-all duration-300"
                                                    :class="selected === {{ $faq->hfq_id }} ? 'rotate-180 text-indigo-600' : ''"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </span>
                                        </button>

                                        <div x-show="selected === {{ $faq->hfq_id }}" x-transition.origin.top.duration.200ms
                                            class="bg-slate-50/50">
                                            <div class="px-4 sm:px-5 pb-5 text-slate-600 leading-relaxed text-sm pt-1">
                                                <div class="prose prose-slate prose-sm max-w-none">
                                                    {!! nl2br(e($faq->hfq_answer)) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>

            {{-- SUPPORT CALLOUT --}}
            <div class="mt-10 sm:mt-14 relative">
                <div
                    class="upsi-card relative p-6 sm:p-8 md:p-10 text-center overflow-hidden">

                    <div class="relative z-10">
                        <div
                            class="inline-flex items-center justify-center w-12 h-12 sm:w-14 sm:h-14 bg-indigo-600 text-white rounded-2xl shadow-sm mb-5">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-black text-slate-900 mb-3">Still need a hand?</h3>
                        <p class="text-sm sm:text-base text-slate-500 mb-6 max-w-sm mx-auto">Our support team is online {{ $supportHours }} to
                            help you with anything you need.</p>

                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="mailto:{{ $supportEmail }}"
                                class="upsi-primary-action w-full sm:w-auto">
                                Send an Email
                            </a>
                            <a href="mailto:{{ $supportEmail }}?subject=UPSI2u%20Support%20Request"
                                class="upsi-secondary-action w-full sm:w-auto">
                                Contact
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-guest-layout>
