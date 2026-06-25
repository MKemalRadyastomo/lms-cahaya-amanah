<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Dashboard Siswa') - LMS Cahaya Amanah Islamic High School</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-800">
        <div class="min-h-screen" x-data="{ sidebarOpen: false, sidebarCollapsed: false }">
            {{-- ===================== SIDEBAR ===================== --}}
            <aside
                class="fixed inset-y-0 left-0 z-40 w-64 transform bg-emerald-700 text-emerald-50 transition-all duration-200 ease-in-out lg:translate-x-0"
                :class="[
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full',
                    sidebarCollapsed ? 'lg:w-20' : 'lg:w-64'
                ]"
            >
                <div class="flex h-16 items-center gap-2.5 border-b border-emerald-600/50 px-5">
                    <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo Cahaya Amanah" class="size-8 rounded-lg object-contain">
                    <span x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="text-sm font-bold leading-tight whitespace-nowrap">Cahaya Amanah<br>Islamic High School</span>
                </div>

                <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
                    @php
                        $navItems = [
                            ['route' => 'siswa.dashboard', 'label' => 'Dashboard', 'icon' => 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.5a.75.75 0 00.75.75h4.5a.75.75 0 00.75-.75V15a.75.75 0 01.75-.75h3a.75.75 0 01.75.75v5.25a.75.75 0 00.75.75h4.5a.75.75 0 00.75-.75V9.75M8.25 21h8.25'],
                            ['route' => 'siswa.materi.index', 'label' => 'Materi', 'icon' => 'M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25'],
                            ['route' => 'siswa.tugas.index', 'label' => 'Tugas', 'icon' => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z'],
                            ['route' => 'siswa.ujian.index', 'label' => 'Ujian', 'icon' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ['route' => 'siswa.jadwal.index', 'label' => 'Jadwal', 'icon' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0V11.25A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5'],
                            ['route' => 'siswa.pengumuman.index', 'label' => 'Pengumuman', 'icon' => 'M10.34 3.94c.09-.542.56-.94 1.11-.94h1.1c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z'],
                            ['route' => 'siswa.nilai.index', 'label' => 'Nilai', 'icon' => 'M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0'],
                        ];
                    @endphp

                    @foreach ($navItems as $item)
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                           {{ request()->routeIs($item['route']) ? 'bg-white/15 text-white' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}"
                           :class="sidebarCollapsed && 'lg:justify-center lg:px-0'">
                            <svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                            </svg>
                            <span x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="whitespace-nowrap">{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </nav>

                {{-- Footer pengguna --}}
                <div class="border-t border-emerald-600/50 p-3">
                    <div class="flex items-center gap-3 rounded-lg px-2 py-2" :class="sidebarCollapsed && 'lg:justify-center lg:px-0'">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=047857&color=fff&size=64" alt="" class="size-9 rounded-full shrink-0">
                        <div x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                            <p class="truncate text-xs text-emerald-200">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- Overlay mobile --}}
            <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-black/40 lg:hidden"></div>

            {{-- ===================== KONTEN ===================== --}}
            <div class="lg:pl-64">
                {{-- Topbar --}}
                <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-gray-200 bg-white">
                    <div class="flex items-center gap-3 px-4 sm:px-6 lg:px-8">
                        {{-- Hamburger mobile --}}
                        <button @click="sidebarOpen = true" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:hidden">
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                        </button>
                        {{-- Hamburger desktop (collapse toggle) --}}
                        <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:block">
                            <svg x-show="!sidebarCollapsed" class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-15 5.25l7.5 7.5-7.5 7.5" /></svg>
                            <svg x-show="sidebarCollapsed" class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 4.5l7.5 7.5-7.5 7.5m-6-15l7.5 7.5-7.5 7.5" /></svg>
                        </button>
                        <span class="text-sm font-bold text-gray-900 lg:hidden">Cahaya Amanah Islamic High School</span>
                    </div>

                    {{-- User dropdown --}}
                    <div class="relative px-4 sm:px-6 lg:px-8" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
                        <button @click="open = ! open" class="rounded-full focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=047857&color=fff&size=64" alt="" class="size-9 rounded-full">
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute end-0 z-50 mt-2 w-48 rounded-md shadow-lg"
                             style="display: none;"
                             @click="open = false">
                            <div class="rounded-md bg-white py-1 ring-1 ring-black ring-opacity-5">
                                <a href="{{ route('profile') }}" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:bg-gray-100 focus:outline-none">
                                    Profil
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:bg-gray-100 focus:outline-none">
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="mx-auto max-w-6xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                    @if ($errors->any())
                        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            <ul class="list-inside list-disc">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>

        <style>[x-cloak]{display:none!important}</style>
    </body>
</html>
