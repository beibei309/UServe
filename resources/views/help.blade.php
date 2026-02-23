<x-guest-layout>
    <div x-data="{
        selected: null,
        search: '',
        toggle(id) {
            this.selected = this.selected === id ? null : id
        }
    }"
        class="min-h-screen bg-white py-16 px-4 sm:px-6 lg:px-8 font-sans text-slate-800 relative overflow-hidden">

        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full opacity-50 pointer-events-none">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-blue-50 blur-[120px]"></div>
            <div class="absolute bottom-[10%] right-[-10%] w-[30%] h-[30%] rounded-full bg-indigo-50 blur-[120px]"></div>
        </div>

        <div class="max-w-4xl mx-auto relative z-10">

            {{-- HEADER & SEARCH --}}
            <div class="text-center mb-16">
                <nav class="flex justify-center mb-6">
                    <span
                        class="px-3 py-1 text-xs font-bold tracking-widest uppercase bg-indigo-50 text-indigo-600 rounded-full border border-indigo-100">
                        U-Serve Help Center
                    </span>
                </nav>
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight mb-6">
                    How can we <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">help
                        you?</span>
                </h1>


            </div>

            {{-- FAQ CONTENT --}}
            <div class="space-y-12">
                @foreach ($faqs as $category => $items)
                    <section>
                        {{-- Category Title with Icon --}}
                        <div class="flex items-center gap-3 mb-6 px-2">
                            <div class="w-8 h-8 rounded-lg bg-slate-900 flex items-center justify-center text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-bold text-slate-800 uppercase tracking-wider">{{ $category }}
                            </h2>
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            @foreach ($items as $faq)
                                <div class="group"
                                    x-show="search === '' || '{{ strtolower($faq->question) }}'.includes(search.toLowerCase())">
                                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm transition-all duration-300 group-hover:border-indigo-200 group-hover:shadow-md overflow-hidden"
                                        :class="selected === {{ $faq->id }} ?
                                            'ring-2 ring-indigo-500/10 border-indigo-200 shadow-lg' : ''">

                                        <button @click="toggle({{ $faq->id }})"
                                            class="w-full px-6 py-5 text-left flex justify-between items-center focus:outline-none">
                                            <span class="font-bold text-slate-700 transition-colors"
                                                :class="selected === {{ $faq->id }} ? 'text-indigo-600' :
                                                    'group-hover:text-slate-900'">
                                                {{ $faq->question }}
                                            </span>
                                            <span
                                                class="ml-6 flex-shrink-0 w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center group-hover:bg-indigo-50 transition-colors">
                                                <svg class="w-4 h-4 text-slate-400 transition-all duration-300"
                                                    :class="selected === {{ $faq->id }} ? 'rotate-180 text-indigo-600' : ''"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </span>
                                        </button>

                                        <div x-show="selected === {{ $faq->id }}" x-collapse
                                            class="bg-slate-50/50">
                                            <div class="px-6 pb-6 text-slate-600 leading-relaxed text-sm pt-2">
                                                <div class="prose prose-slate prose-sm max-w-none">
                                                    {!! nl2br(e($faq->answer)) !!}
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
            <div class="mt-20 relative">
                <div class="absolute inset-0 bg-indigo-600 rounded-[3rem] rotate-1 opacity-5"></div>
                <div
                    class="relative bg-white border border-slate-100 rounded-[2.5rem] p-10 md:p-12 shadow-xl shadow-slate-200/50 text-center overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-50 rounded-full blur-3xl"></div>

                    <div class="relative z-10">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 bg-indigo-600 text-white rounded-2xl shadow-lg shadow-indigo-200 mb-6 transform -rotate-3 hover:rotate-0 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900 mb-3">Still need a hand?</h3>
                        <p class="text-slate-500 mb-8 max-w-sm mx-auto">Our support team is online from 8AM - 5PM to
                            help you with anything you need.</p>

                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="mailto:support@U-Serve.upsi.edu.my"
                                class="inline-flex items-center justify-center px-8 py-4 bg-slate-900 text-white font-bold rounded-2xl hover:bg-indigo-600 transition-all shadow-lg hover:shadow-indigo-200 active:scale-95">
                                Send an Email
                            </a>
                            <a href="#"
                                class="inline-flex items-center justify-center px-8 py-4 bg-white text-slate-700 font-bold rounded-2xl border border-slate-200 hover:border-indigo-600 hover:text-indigo-600 transition-all">
                                Contact
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </section>
</x-guest-layout>
