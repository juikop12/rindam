<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pengolahan Data Siswa per Satdik') — SIPANDU-WBK</title>

    <!-- PWA / Android Mobile Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0F172A">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SIPANDU">
    <link rel="icon" type="image/png" sizes="192x192" href="/img/icons/icon-192x192.png">
    <link rel="apple-touch-icon" href="/img/icons/icon-192x192.png">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&family=Inter:wght@400;500;600;700;800&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,500,1,0" rel="stylesheet">

</head><body class="bg-slate-50 text-slate-800 font-sans antialiased overflow-hidden" 
      x-data="{ 
          isSidebarExpanded: localStorage.getItem('sipandu_sidebar_expanded') !== 'false',
          mobileSidebarOpen: false,
          
          toggleSidebar() {
              this.isSidebarExpanded = !this.isSidebarExpanded;
              localStorage.setItem('sipandu_sidebar_expanded', this.isSidebarExpanded);
          }
      }">

<div class="flex h-screen w-full">
    
    <!-- BACKDROP MOBILE -->
    <div x-show="mobileSidebarOpen" 
         x-transition.opacity.duration.300ms
         @click="mobileSidebarOpen = false"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 lg:hidden" style="display: none;"></div>

    <!-- SIDEBAR -->
    <aside :class="{ 
               'w-72 lg:w-72': isSidebarExpanded, 
               'w-72 lg:w-20': !isSidebarExpanded,
               'translate-x-0': mobileSidebarOpen,
               '-translate-x-full lg:translate-x-0': !mobileSidebarOpen
           }"
           class="fixed inset-y-0 left-0 z-50 flex flex-col bg-white border-r border-slate-200 transition-all duration-300 ease-in-out lg:static lg:h-screen lg:shrink-0 shadow-[4px_0_24px_rgba(0,0,0,0.02)]">
        
        <!-- Toggle Button (Floating Edge) -->
        <button @click="toggleSidebar()" 
                class="hidden lg:flex items-center justify-center w-7 h-7 rounded-full bg-white border border-slate-200 text-slate-400 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 absolute -right-3.5 top-6 z-50 transition-colors shadow-sm"
                title="Toggle Sidebar">
            <span class="ms text-[18px] transition-transform duration-300" :class="!(isSidebarExpanded || mobileSidebarOpen) ? 'rotate-180' : ''">chevron_left</span>
        </button>

        <!-- BRAND HEADER -->
        <div class="flex items-center justify-between h-20 px-4 border-b border-slate-100 shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 overflow-hidden whitespace-nowrap outline-none w-full">
                <div class="flex items-center justify-center w-12 h-12 shrink-0 transition-transform duration-300" :class="!(isSidebarExpanded || mobileSidebarOpen) ? 'mx-auto' : ''">
                    <!-- USING THE NEW LOGO -->
                    <img src="{{ asset('img/rindam-logo.png') }}" alt="Logo" class="w-11 h-11 object-contain drop-shadow-sm" onerror="this.outerHTML='<span class=\'ms text-[28px] text-slate-800\'>shield_person</span>'">
                </div>
                <div class="flex flex-col transition-opacity duration-300" :class="isSidebarExpanded ? 'opacity-100 w-auto' : 'opacity-0 w-0'">
                    <div class="font-black text-lg text-slate-900 tracking-tight leading-tight font-['Montserrat']">
                        SIPANDU<span class="text-blue-600">-WBK</span>
                    </div>
                    <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-0.5">
                        Rindam III/Slw
                    </div>
                </div>
            </a>
        </div>

        <!-- NAVIGATION MENU -->
        <nav class="flex-1 overflow-y-auto overflow-x-hidden p-3 flex flex-col gap-1 scrollbar-hide">
            
            <!-- SECTION TITLE -->
            <div class="mt-4 mb-2 first:mt-0 transition-all duration-300" :class="isSidebarExpanded ? 'px-3' : 'px-0 text-center'">
                <div x-show="!isSidebarExpanded" class="h-0.5 w-8 mx-auto bg-slate-200 rounded-full"></div>
                <span x-show="isSidebarExpanded || mobileSidebarOpen" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pusat Komando</span>
            </div>

            <!-- MENU ITEM 1 -->
            <a href="{{ route('dashboard') }}" 
               class="group relative flex items-center p-3 rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-slate-600 font-medium hover:bg-blue-50 hover:text-blue-700' }}"
               :class="!(isSidebarExpanded || mobileSidebarOpen) ? 'justify-center' : 'justify-between'">
                
                <div class="flex items-center gap-3">
                    <span class="ms text-[24px] transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' }}">dashboard</span>
                    <span x-show="isSidebarExpanded || mobileSidebarOpen" class="font-semibold text-[13.5px] whitespace-nowrap">Dashboard Utama</span>
                </div>
                
                <span x-show="isSidebarExpanded || mobileSidebarOpen" class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-blue-100 text-blue-700">Live</span>
                
                <!-- TOOLTIP FOR COLLAPSED -->
                <div x-show="!isSidebarExpanded" class="absolute left-full ml-3 px-3 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50">
                    Dashboard Utama
                </div>
            </a>

            <!-- SECTION TITLE -->
            <div class="mt-4 mb-2 transition-all duration-300" :class="isSidebarExpanded ? 'px-3' : 'px-0 text-center'">
                <div x-show="!isSidebarExpanded" class="h-0.5 w-8 mx-auto bg-slate-200 rounded-full"></div>
                <span x-show="isSidebarExpanded || mobileSidebarOpen" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Data & Kesehatan</span>
            </div>
            
            <a href="{{ route('students.index') }}" 
               class="group relative flex items-center p-3 rounded-xl transition-all duration-200 {{ request()->routeIs('students.index') || request()->routeIs('students.show') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-slate-600 font-medium hover:bg-blue-50 hover:text-blue-700' }}"
               :class="!(isSidebarExpanded || mobileSidebarOpen) ? 'justify-center' : 'justify-between'">
                <div class="flex items-center gap-3">
                    <span class="ms text-[24px] transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('students.index') || request()->routeIs('students.show') ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' }}">groups</span>
                    <span x-show="isSidebarExpanded || mobileSidebarOpen" class="font-semibold text-[13.5px] whitespace-nowrap">Data Serdik</span>
                </div>
                <span x-show="isSidebarExpanded || mobileSidebarOpen" class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-slate-100 text-slate-600 {{ request()->routeIs('students.index') || request()->routeIs('students.show') ? 'bg-blue-100 text-blue-700' : '' }}">{{ \App\Models\Student::count() }}</span>
                <div x-show="!isSidebarExpanded" class="absolute left-full ml-3 px-3 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50">Data Serdik</div>
            </a>

            <a href="{{ route('programs.index') }}" 
               class="group relative flex items-center p-3 rounded-xl transition-all duration-200 {{ request()->routeIs('programs.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-slate-600 font-medium hover:bg-blue-50 hover:text-blue-700' }}"
               :class="!(isSidebarExpanded || mobileSidebarOpen) ? 'justify-center' : 'justify-between'">
                <div class="flex items-center gap-3">
                    <span class="ms text-[24px] transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('programs.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' }}">school</span>
                    <span x-show="isSidebarExpanded || mobileSidebarOpen" class="font-semibold text-[13.5px] whitespace-nowrap">Program Satdik</span>
                </div>
                <div x-show="!isSidebarExpanded" class="absolute left-full ml-3 px-3 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50">Program Satdik</div>
            </a>

            <a href="{{ route('health.index') }}" 
               class="group relative flex items-center p-3 rounded-xl transition-all duration-200 {{ request()->routeIs('health.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-slate-600 font-medium hover:bg-blue-50 hover:text-blue-700' }}"
               :class="!(isSidebarExpanded || mobileSidebarOpen) ? 'justify-center' : 'justify-between'">
                <div class="flex items-center gap-3">
                    <span class="ms text-[24px] transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('health.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' }}">medical_services</span>
                    <span x-show="isSidebarExpanded || mobileSidebarOpen" class="font-semibold text-[13.5px] whitespace-nowrap">Kesehatan Serdik</span>
                </div>
                <span x-show="isSidebarExpanded || mobileSidebarOpen" class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-emerald-100 text-emerald-700">Medis</span>
                <div x-show="!isSidebarExpanded" class="absolute left-full ml-3 px-3 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50">Kesehatan Serdik</div>
            </a>

            <!-- SECTION TITLE -->
            <div class="mt-4 mb-2 transition-all duration-300" :class="isSidebarExpanded ? 'px-3' : 'px-0 text-center'">
                <div x-show="!isSidebarExpanded" class="h-0.5 w-8 mx-auto bg-slate-200 rounded-full"></div>
                <span x-show="isSidebarExpanded || mobileSidebarOpen" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sistem</span>
            </div>
            
            @if(auth()->user()?->canModifyData())
            <a href="{{ route('students.import') }}" 
               class="group relative flex items-center p-3 rounded-xl transition-all duration-200 {{ request()->routeIs('students.import') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-slate-600 font-medium hover:bg-blue-50 hover:text-blue-700' }}"
               :class="!(isSidebarExpanded || mobileSidebarOpen) ? 'justify-center' : 'justify-between'">
                <div class="flex items-center gap-3">
                    <span class="ms text-[24px] transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('students.import') ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' }}">upload_file</span>
                    <span x-show="isSidebarExpanded || mobileSidebarOpen" class="font-semibold text-[13.5px] whitespace-nowrap">Input Data Excel</span>
                </div>
                <div x-show="!isSidebarExpanded" class="absolute left-full ml-3 px-3 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50">Input Data Excel</div>
            </a>
            @endif

            @if(auth()->user()?->isSuperAdmin())
            <a href="{{ route('settings.index') }}" 
               class="group relative flex items-center p-3 rounded-xl transition-all duration-200 {{ request()->routeIs('settings.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-slate-600 font-medium hover:bg-blue-50 hover:text-blue-700' }}"
               :class="!(isSidebarExpanded || mobileSidebarOpen) ? 'justify-center' : 'justify-between'">
                <div class="flex items-center gap-3">
                    <span class="ms text-[24px] transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('settings.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' }}">settings</span>
                    <span x-show="isSidebarExpanded || mobileSidebarOpen" class="font-semibold text-[13.5px] whitespace-nowrap">Pengaturan</span>
                </div>
                <span x-show="isSidebarExpanded || mobileSidebarOpen" class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-rose-100 text-rose-800">Admin</span>
                <div x-show="!isSidebarExpanded" class="absolute left-full ml-3 px-3 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50">Pengaturan</div>
            </a>

            <a href="{{ route('users.index') }}" 
               class="group relative flex items-center p-3 rounded-xl transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-slate-600 font-medium hover:bg-blue-50 hover:text-blue-700' }}"
               :class="!(isSidebarExpanded || mobileSidebarOpen) ? 'justify-center' : 'justify-between'">
                <div class="flex items-center gap-3">
                    <span class="ms text-[24px] transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('users.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' }}">manage_accounts</span>
                    <span x-show="isSidebarExpanded || mobileSidebarOpen" class="font-semibold text-[13.5px] whitespace-nowrap">Manajemen Akun</span>
                </div>
                <span x-show="isSidebarExpanded || mobileSidebarOpen" class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-rose-100 text-rose-800">Admin</span>
                <div x-show="!isSidebarExpanded" class="absolute left-full ml-3 px-3 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50">Manajemen Akun</div>
            </a>
            <a href="{{ route('students.audit-logs') }}" 
               class="group relative flex items-center p-3 rounded-xl transition-all duration-200 {{ request()->routeIs('students.audit-logs') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-slate-600 font-medium hover:bg-blue-50 hover:text-blue-700' }}"
               :class="!(isSidebarExpanded || mobileSidebarOpen) ? 'justify-center' : 'justify-between'">
                <div class="flex items-center gap-3">
                    <span class="ms text-[24px] transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('students.audit-logs') ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' }}">policy</span>
                    <span x-show="isSidebarExpanded || mobileSidebarOpen" class="font-semibold text-[13.5px] whitespace-nowrap">Audit Log</span>
                </div>
                <span x-show="isSidebarExpanded || mobileSidebarOpen" class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-rose-100 text-rose-800">Admin</span>
                <div x-show="!isSidebarExpanded" class="absolute left-full ml-3 px-3 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50">Audit Log</div>
            </a>
            @endif

        </nav>

        <!-- USER FOOTER -->
        <div class="p-3 border-t border-slate-100 bg-slate-50 shrink-0">
            <div class="flex items-center justify-between" :class="!(isSidebarExpanded || mobileSidebarOpen) ? 'justify-center' : ''">
                <div class="flex items-center gap-2.5 overflow-hidden">
                    <div class="w-10 h-10 rounded-xl {{ auth()->user()?->isSuperAdmin() ? 'bg-rose-100 text-rose-800' : (auth()->user()?->isDanrindam() ? 'bg-amber-100 text-amber-800' : (auth()->user()?->isOperatorDanrindam() ? 'bg-indigo-100 text-indigo-800' : (auth()->user()?->isOperatorSatdik() ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700'))) }} border border-white shadow-sm flex items-center justify-center shrink-0">
                        <span class="ms text-[20px]">{{ auth()->user()?->isSuperAdmin() ? 'admin_panel_settings' : (auth()->user()?->isDanrindam() ? 'military_tech' : (auth()->user()?->isOperatorDanrindam() ? 'stars' : (auth()->user()?->isOperatorSatdik() ? 'support_agent' : 'shield_person'))) }}</span>
                    </div>
                    <div class="flex flex-col overflow-hidden" x-show="isSidebarExpanded || mobileSidebarOpen" x-transition.opacity.duration.300ms>
                        <span class="text-xs font-bold text-slate-800 truncate" title="{{ auth()->user()->name ?? 'Prajurit Rindam' }}">{{ auth()->user()->name ?? 'Prajurit Rindam' }}</span>
                        <span class="text-[10px] font-semibold text-slate-500 truncate">
                            @if(auth()->user()?->isSuperAdmin())
                                <span class="text-rose-700 font-bold">👑 SUPER ADMIN</span>
                            @elseif(auth()->user()?->isDanrindam())
                                <span class="text-amber-700 font-bold">⭐ DANRINDAM (VIEW ONLY)</span>
                            @elseif(auth()->user()?->isOperatorDanrindam())
                                <span class="text-indigo-700 font-bold">🎖️ OPERATOR PUSAT (5 SATDIK)</span>
                            @elseif(auth()->user()?->isOperatorSatdik() && auth()->user()?->satdik)
                                <span class="text-emerald-700 font-bold">🔒 {{ auth()->user()->satdik->code }}</span>
                            @else
                                {{ strtoupper(auth()->user()?->role_code ?? 'PENGGUNA') }}
                            @endif
                        </span>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}" x-show="isSidebarExpanded || mobileSidebarOpen" x-transition.opacity.duration.300ms class="shrink-0">
                    @csrf
                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Keluar dari Sistem (Logout)">
                        <span class="ms text-[20px]">logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50">
        
        <!-- TOPBAR -->
        <header class="h-[72px] bg-white border-b border-slate-200 flex items-center justify-between px-4 lg:px-6 shrink-0 z-30 shadow-sm sticky top-0">
            <div class="flex items-center gap-3 lg:gap-4">
                <!-- Mobile Menu Button -->
                <button @click="mobileSidebarOpen = true" class="lg:hidden flex items-center justify-center w-10 h-10 rounded-xl text-slate-500 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                    <span class="ms text-[24px]">menu_open</span>
                </button>
                
                <!-- TITLE & BREADCRUMB -->
                <div class="flex items-center gap-2">
                    <div class="hidden sm:flex items-center gap-2 text-sm font-medium">
                        <span class="text-slate-400">SIPANDU-WBK</span>
                        <span class="ms text-slate-300 text-[18px]">chevron_right</span>
                    </div>
                    <h1 class="text-slate-800 font-bold text-lg sm:text-sm">
                        @if(request()->routeIs('dashboard'))
                            Dashboard Eksekutif
                        @elseif(request()->routeIs('settings.*'))
                            Pengaturan Sistem
                        @elseif(request()->routeIs('users.*'))
                            Manajemen Akun Pengguna
                        @elseif(request()->routeIs('health.*'))
                            Kesehatan Serdik
                        @elseif(request()->routeIs('students.import'))
                            Impor Data Serdik
                        @elseif(request()->routeIs('students.audit-logs'))
                            Audit Log
                        @else
                            Buku Induk Data Serdik
                        @endif
                    </h1>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-4">
                <!-- PWA Install Button (Android) -->
                <button type="button" id="pwa-install-btn" onclick="installPwaApp()" style="display:none;" 
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-slate-900 transition-colors shadow-sm">
                    <span class="ms text-[18px]">install_mobile</span>
                    <span class="hidden sm:inline">Pasang Aplikasi</span>
                </button>

                <!-- User Scope Badge -->
                @if(auth()->check())
                    <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-xl border text-xs font-bold {{ auth()->user()->isSuperAdmin() ? 'bg-rose-50 border-rose-200 text-rose-800' : (auth()->user()->isDanrindam() ? 'bg-amber-50 border-amber-200 text-amber-800' : (auth()->user()->isOperatorDanrindam() ? 'bg-indigo-50 border-indigo-200 text-indigo-800' : (auth()->user()->isOperatorSatdik() ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-slate-100 border-slate-200 text-slate-700'))) }}">
                        <span class="ms text-[16px]">{{ auth()->user()->isSuperAdmin() ? 'admin_panel_settings' : (auth()->user()->isDanrindam() ? 'visibility' : (auth()->user()->isOperatorDanrindam() ? 'stars' : (auth()->user()->isOperatorSatdik() ? 'lock' : 'verified_user'))) }}</span>
                        <span>{{ auth()->user()->isSuperAdmin() ? 'Super Admin (Sistem)' : (auth()->user()->isDanrindam() ? 'Danrindam (Monitor)' : (auth()->user()->isOperatorDanrindam() ? 'Operator Pusat' : ('Satdik ' . (auth()->user()->satdik?->code ?? 'Terkunci')))) }}</span>
                    </div>
                @endif


                <!-- Action Button -->
                @if(auth()->user()?->canModifyData())
                <button onclick="if(typeof openCreateStudentModal === 'function') { openCreateStudentModal(); } else { window.location='{{ route('students.index', ['action' => 'create']) }}'; }" 
                        class="flex items-center gap-2 px-3 sm:px-4 py-2 sm:py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold text-sm shadow-sm shadow-blue-200 transition-colors duration-200">
                    <span class="ms text-[20px]">add</span>
                    <span class="hidden sm:block">Tambah Serdik</span>
                </button>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="flex items-center justify-center w-10 h-10 text-slate-400 hover:text-rose-600 hover:bg-rose-50 border border-transparent hover:border-rose-100 rounded-xl transition-all" title="Keluar / Logout">
                        <span class="ms text-[22px]">logout</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- MAIN SCROLLABLE AREA -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 lg:p-8">
            
            <!-- ALERTS -->
            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800">
                    <span class="ms text-[24px] text-emerald-500">check_circle</span>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800">
                    <span class="ms text-[24px] text-red-500">error</span>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <!-- YIELD CONTENT -->
            @yield('content')
            
        </main>
    </div>
</div>

<script src="{{ asset('js/chart.umd.min.js') }}"></script>
@yield('scripts')

<!-- PWA Service Worker & Install Prompt Handler -->
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then((reg) => console.log('SIPANDU PWA Service Worker aktif:', reg.scope))
                .catch((err) => console.error('SIPANDU PWA gagal:', err));
        });
    }

    // Tangani event install PWA di browser Android
    let deferredPrompt;
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        const installBtn = document.getElementById('pwa-install-btn');
        if (installBtn) {
            installBtn.style.display = 'inline-flex';
        }
    });

    function installPwaApp() {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('Pengguna menginstal SIPANDU PWA');
                }
                deferredPrompt = null;
                const installBtn = document.getElementById('pwa-install-btn');
                if (installBtn) installBtn.style.display = 'none';
            });
        }
    }
</script>

</body>
</html>
