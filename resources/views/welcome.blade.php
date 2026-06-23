<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Learning Management System Cahaya Amanah Islamic High School - Sistem Manajemen Pembelajaran.">

        <title>Learning Management System Cahaya Amanah Islamic High School</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans bg-gray-50 text-gray-800 min-h-screen flex flex-col">

        {{-- ===================== NAVBAR ===================== --}}
        <header class="border-b border-gray-200 bg-white">
            <nav class="mx-auto flex h-16 max-w-5xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <a href="/" class="flex items-center gap-2.5">
                    <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo Sekolah" class="h-9 w-auto">
                    <span class="text-base font-bold tracking-tight text-gray-900">Learning Management System</span>
                </a>
            </nav>
        </header>

        {{-- ===================== MAIN ===================== --}}
        <main class="flex flex-1 items-center">
            <div class="mx-auto w-full max-w-5xl px-4 py-16 sm:px-6 lg:px-8">

                {{-- Welcome --}}
                <div class="text-center">
                    <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo Sekolah" class="mx-auto h-20 w-auto">
                    <h1 class="mt-6 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Selamat Datang</h1>
                    <p class="mx-auto mt-3 max-w-xl text-base text-gray-600">
                        Sistem Manajemen Pembelajaran untuk mengakses materi, tugas, ujian, jadwal, dan nilai.
                    </p>

                    <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700 sm:w-auto">
                                Masuk ke Sistem
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </main>

        {{-- ===================== FOOTER ===================== --}}
        <footer class="border-t border-gray-200 bg-white">
            <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 lg:px-8">
                <p class="text-center text-xs text-gray-400">&copy; {{ date('Y') }} Learning Management System Cahaya Amanah Islamic High School</p>
            </div>
        </footer>

    </body>
</html>
