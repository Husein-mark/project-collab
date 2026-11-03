@extends('layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-xs">
        <h1 class="text-xl font-bold text-slate-900">Halo, {{ auth()->user()->name }} 👋</h1>
        <p class="text-sm text-slate-500 mt-1">Selamat datang di portal peminjaman peralatan studio sekolah.</p>
    </div>
</div>
@endsection
