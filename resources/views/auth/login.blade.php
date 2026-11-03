@extends('layouts.app')

@section('title', 'Masuk - Studio Sekolah')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-6 px-4">
    <div class="max-w-md w-full">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8">
            <div class="text-center mb-8">
                <div class="w-14 h-14 rounded-2xl bg-indigo-600 text-white flex items-center justify-center mx-auto mb-4 shadow-lg shadow-indigo-500/30">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Studio Sekolah</h1>
                <p class="text-sm text-slate-500 mt-1">Silakan masuk untuk mengajukan atau mengelola peminjaman</p>
            </div>

            <!-- Form -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">
                        Alamat Email
                    </label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           value="{{ old('email') }}" 
                           required 
                           placeholder="nama@sekolah.sch.id"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('email') border-rose-500 @enderror">
                    @error('email')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-600">
                            Password
                        </label>
                    </div>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           required 
                           placeholder="••••••••"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('password') border-rose-500 @enderror">
                    @error('password')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center text-slate-600">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                        <span class="ml-2 text-xs">Ingat saya</span>
                    </label>
                </div>

                <button type="submit" 
                        class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-md shadow-indigo-500/25 transition">
                    Masuk ke Sistem
                </button>
            </form>

            <!-- Quick Demo Login Helpers for Presentation -->
            <div class="mt-8 pt-6 border-t border-slate-100">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider text-center mb-3">Akun Demo Pengujian (1-Klik)</p>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <button type="button" 
                            onclick="fillCredentials('guru@studio.sch.id', 'password')"
                            class="p-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium rounded-lg text-left transition border border-indigo-200/50">
                        <div class="font-bold">Akun Guru</div>
                        <div class="text-[11px] text-indigo-500">Pak Fajar (Pengelola)</div>
                    </button>
                    <button type="button" 
                            onclick="fillCredentials('alwi@siswa.sch.id', 'password')"
                            class="p-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-medium rounded-lg text-left transition border border-emerald-200/50">
                        <div class="font-bold">Akun Siswa</div>
                        <div class="text-[11px] text-emerald-500">Alwi (Peminjam)</div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function fillCredentials(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endsection
