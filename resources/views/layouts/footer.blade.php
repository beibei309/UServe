<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">  
</head>
<footer class="bg-slate-950 text-white pt-24 pb-12 border-t border-slate-900 relative overflow-hidden">
    <div class="absolute bottom-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-blue-500/50 to-transparent"></div>
    
    <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
            
            <div class="col-span-1 lg:col-span-1">
                <div class="flex items-center gap-2 mb-6">
                    <span class="text-3xl font-black tracking-tighter bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-400">
                       U - Serve 
                    </span>
                </div>
                <p class="text-slate-400 text-sm leading-relaxed mb-8 max-w-xs">
                    The leading peer-to-peer service platform for the UPSI community. We empower student talents and facilitate trusted connections.
                </p>
                
                <div class="flex space-x-3">
                    <a href="https://www.facebook.com/UPSIMalaysia/" class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-blue-600 hover:border-blue-500 transition-all duration-300">
                        <i class="fa-brands fa-facebook"></i>
                        
                    </a>
                    <a href="https://x.com/UPSI_Malaysia" class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-sky-500 hover:border-sky-400 transition-all duration-300">
                        <i class="fa-brands fa-twitter"></i>
                    </a>
                    <a href="https://www.instagram.com/upsi_malaysia" class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-gradient-to-tr from-yellow-500 via-red-500 to-purple-500 hover:border-transparent transition-all duration-300">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                </div>
            </div>

            <div class="hidden lg:block"></div>

            <div>
                <h4 class="text-xs font-bold uppercase tracking-[0.2em] text-blue-500 mb-8">Platform</h4>
                <ul class="space-y-4">
                    <li>
                        <a href="{{ route('services.index') }}" class="group text-slate-400 hover:text-white transition-colors text-sm flex items-center">
                            <span class="w-0 group-hover:w-2 h-px bg-blue-500 mr-0 group-hover:mr-2 transition-all"></span>
                            Find Services
                        </a>
                    </li>
                    @auth
                                               <li>
                            <a href="{{ route('profile.edit') }}" class="group text-slate-400 hover:text-white transition-colors text-sm flex items-center">
                                <span class="w-0 group-hover:w-2 h-px bg-blue-500 mr-0 group-hover:mr-2 transition-all"></span>
                                My Account
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('register') }}" class="group text-slate-400 hover:text-white transition-colors text-sm flex items-center">
                                <span class="w-0 group-hover:w-2 h-px bg-blue-500 mr-0 group-hover:mr-2 transition-all"></span>
                                Become a Seller
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('login') }}" class="group text-slate-400 hover:text-white transition-colors text-sm flex items-center">
                                <span class="w-0 group-hover:w-2 h-px bg-blue-500 mr-0 group-hover:mr-2 transition-all"></span>
                                Log In
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>

            <div>
                <h4 class="text-xs font-bold uppercase tracking-[0.2em] text-purple-500 mb-8">Support & Help</h4>
                <ul class="space-y-4">
                    <li><a href="{{ route('help') }}" class="text-slate-400 hover:text-white transition-colors text-sm">Help Center</a></li>
                           <li><a href="https://d09103.schtg.com/UServe/public/privacy" class="text-slate-400 hover:text-white transition-colors text-sm">Privacy Policy</a></li>
                    <li><a href="https://d09103.schtg.com/UServe/public/terms" class="text-slate-400 hover:text-white transition-colors text-sm">Terms of Service</a></li>
                </ul>
            </div>
        </div>

        <div class="pt-8 border-t border-slate-900 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="text-slate-500 text-xs tracking-wide">
                &copy; {{ date('Y') }} <span class="text-slate-300 font-semibold">U-Serve | UPSI Service Circle</span>. All rights reserved.
            </div>
            
            <div class="flex items-center gap-6">
                <div class="flex items-center text-xs text-slate-500">
                    Built with <span class="text-red-500 mx-1.5 animate-pulse">❤</span> for UPSI Students
                </div>
                <div class="flex items-center gap-2 px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 rounded-full">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-ping"></span>
                    <span class="text-[10px] font-bold text-emerald-500 uppercase">System Operational</span>
                </div>
            </div>
        </div>
    </div>
</footer>
