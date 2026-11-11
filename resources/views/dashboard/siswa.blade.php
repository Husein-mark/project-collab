@extends('layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-indigo-700 to-brand-600 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-indigo-500/20 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <span class="px-3 py-1 bg-white/20 backdrop-blur-xs text-xs font-semibold rounded-full uppercase tracking-wider">
                Portal Siswa
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight mt-2">Halo, {{ auth()->user()->name }}! 👋</h1>
            <p class="text-indigo-100 text-sm mt-1 max-w-xl">
                Pantau permohonan peminjaman studio, periksa batas waktu pengembalian, dan jaga kondisi peralatan studio bersama.
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('loans.create') }}" class="px-5 py-3 bg-white text-indigo-700 hover:bg-indigo-50 font-bold text-sm rounded-xl shadow-sm transition inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Ajukan Pinjam Baru
            </a>
        </div>
    </div>

    <!-- PENGINGAT JATUH TEMPO (US #9: Pengingat H-1 & Terlambat) -->
    @if($dueSoonLoans->count() > 0)
    <div class="bg-amber-50 border-2 border-amber-300 rounded-2xl p-5 shadow-xs">
        <div class="flex items-start space-x-3">
            <div class="p-2 bg-amber-500 text-white rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-base font-bold text-amber-900">🔔 Pengingat Jatuh Tempo Pengembalian Barang (User Story #9)</h3>
                <p class="text-xs text-amber-800 mt-0.5">Harap segera kembalikan peralatan studio berikut sebelum atau pada tanggal jatuh tempo untuk menghindari denda/sanksi keterlambatan:</p>
                
                <div class="mt-3 space-y-2">
                    @foreach($dueSoonLoans as $due)
                    <div class="bg-white/80 border border-amber-200 rounded-xl p-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                        <div>
                            <span class="font-bold text-slate-900">{{ $due->item->name }}</span>
                            <span class="text-slate-500">({{ $due->loan_code }})</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-slate-600">Batas Pengembalian:</span>
                            <span class="font-extrabold text-amber-700 bg-amber-100 px-2.5 py-1 rounded-lg">
                                {{ $due->expected_return_date->format('d M Y') }}
                                @if($due->expected_return_date->isToday())
                                    (HARI INI)
                                @elseif($due->expected_return_date->isTomorrow())
                                    (BESOK / H-1)
                                @elseif($due->is_overdue)
                                    (TELAH LEWAT +{{ $due->overdue_days }} Hari)
                                @endif
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Stats Summary -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Menunggu Approval</span>
            <span class="text-2xl font-bold text-amber-600 mt-1 block">{{ $pendingCount }}</span>
            <span class="text-[11px] text-slate-400">Dalam proses review guru</span>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Siap Diambil</span>
            <span class="text-2xl font-bold text-blue-600 mt-1 block">{{ $approvedCount }}</span>
            <span class="text-[11px] text-blue-600 font-medium">Bawa NIS ke studio</span>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Sedang Anda Bawa</span>
            <span class="text-2xl font-bold text-indigo-600 mt-1 block">{{ $borrowedCount }}</span>
            <span class="text-[11px] text-slate-400">Peralatan aktif dipinjam</span>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Terlambat</span>
            <span class="text-2xl font-bold text-rose-600 mt-1 block">{{ $overdueCount }}</span>
            <span class="text-[11px] text-rose-500 font-medium">Harap segera kembalikan</span>
        </div>
    </div>

    <!-- STATUS PENGAJUAN TERBARU (US #8: Melacak Approval Siswa) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Status Permohonan Peminjaman Terbaru (US #8)</h2>
                <p class="text-xs text-slate-500 mt-0.5">Ketahui apakah pengajuan Anda sudah disetujui atau ditolak tanpa perlu bolak-balik menemui guru.</p>
            </div>
            <a href="{{ route('loans.my') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                Lihat Semua &rarr;
            </a>
        </div>

        @if($recentLoans->count() > 0)
        <div class="space-y-3">
            @foreach($recentLoans as $loan)
            @php $badge = $loan->status_badge; @endphp
            <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-bold text-indigo-600 font-mono">{{ $loan->loan_code }}</span>
                        <span class="text-xs text-slate-400">&bull; {{ $loan->borrow_date->format('d M') }} s/d {{ $loan->expected_return_date->format('d M Y') }}</span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 mt-0.5">{{ $loan->item->name }} ({{ $loan->quantity }} Unit)</h3>
                    <p class="text-xs text-slate-500 mt-0.5 italic">"{{ $loan->purpose }}"</p>
                    
                    @if($loan->status === 'approved')
                    <div class="mt-2 inline-flex items-center text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                        ✓ Disetujui oleh guru. Silakan temui pengelola studio di ruang studio untuk serah terima barang.
                    </div>
                    @elseif($loan->status === 'rejected')
                    <div class="mt-2 inline-flex items-center text-xs font-semibold text-rose-700 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200">
                        ✕ Pengajuan ditolak: {{ $loan->rejection_reason }}
                    </div>
                    @endif
                </div>

                <div class="text-right flex sm:flex-col items-center sm:items-end justify-between">
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $badge['bg'] }} {{ $badge['text'] }}">
                        {{ $badge['label'] }}
                    </span>
                    <span class="text-[11px] text-slate-400 mt-1 sm:block hidden">
                        Diajukan {{ $loan->created_at->diffForHumans() }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="py-8 text-center text-slate-400 text-xs">
            Anda belum pernah mengajukan peminjaman barang studio.
        </div>
        @endif
    </div>
</div>
@endsection
