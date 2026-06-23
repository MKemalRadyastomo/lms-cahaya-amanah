@extends('layouts.siswa')

@section('title', 'Dashboard')

@section('content')
<div class="mx-auto max-w-md py-16 text-center">
    <span class="inline-flex size-16 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
        <svg class="size-8" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
    </span>
    <h1 class="mt-5 text-xl font-bold text-gray-900">Belum Terdaftar di Kelas</h1>
    <p class="mt-2 text-sm text-gray-500">
        Akun Anda belum terdaftar pada kelas mana pun di tahun ajaran aktif.
        Silakan hubungi guru atau admin untuk didaftarkan ke kelas.
    </p>
    <a href="{{ route('profile') }}" class="mt-6 inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
        Lihat Profil
    </a>
</div>
@endsection
