@extends('layouts.app')

@section('title', 'Dashboard Guru / Pengelola')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-xs">
        <h1 class="text-xl font-bold text-slate-900">Dashboard Pengelolaan Studio</h1>
        <p class="text-sm text-slate-500 mt-1">Selamat datang {{ auth()->user()->name }}, Anda memiliki hak akses penuh pengelolaan studio.</p>
    </div>
</div>
@endsection
