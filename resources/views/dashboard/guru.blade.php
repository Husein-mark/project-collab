@extends('layouts.app')

@section('title', 'Dashboard Pengelola Studio')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-1 bg-indigo-100 text-indigo-800 font-bold text-xs rounded-lg uppercase tracking-wider">Dashboard Guru</span>
                <span class="text-xs text-slate-400">&bull; User Story #11</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">Monitoring Peminjaman Studio Terpusat</h1>
            <p class="text-sm text-slate-500">Pantau seluruh sirkulasi peralatan, status pengembalian, keterlambatan, dan approval dalam satu dashboard.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('items.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-xs transition">
                + Tambah Barang
            </a>
            <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                Buka Laporan Rekap
            </a>
        </div>
    </div>

    <!-- 5 Monitoring KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Total Inventaris</span>
            <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $totalItems }} Barang</span>
            <span class="text-[11px] text-emerald-600 font-medium">{{ $availableItems }} unit tersedia</span>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-amber-500 uppercase tracking-wider block">Perlu Approval</span>
            <span class="text-2xl font-black text-amber-600 mt-1 block">{{ $pendingLoans->count() }} Pengajuan</span>
            <span class="text-[11px] text-amber-600">Menunggu verifikasi guru</span>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-indigo-500 uppercase tracking-wider block">Sedang Dipinjam</span>
            <span class="text-2xl font-black text-indigo-600 mt-1 block">{{ $activeLoansCount }} Transaksi</span>
            <span class="text-[11px] text-indigo-500">Di tangan siswa</span>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-rose-500 uppercase tracking-wider block">Terlambat Kembali</span>
            <span class="text-2xl font-black text-rose-600 mt-1 block">{{ $overdueLoansCount }} Kasus</span>
            <span class="text-[11px] text-rose-600 font-semibold">Perlu tindak lanjut</span>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs col-span-2 lg:col-span-1">
            <span class="text-xs font-semibold text-rose-600 uppercase tracking-wider block">Notice Kerusakan</span>
            <span class="text-2xl font-black text-rose-700 mt-1 block">{{ $unhandledDamageCount }} Notice</span>
            <span class="text-[11px] text-rose-600 font-medium">Butuh inspeksi/servis</span>
        </div>
    </div>

    <!-- Quick Approval Queue for Guru -->
    @if($pendingLoans->count() > 0)
    <div class="bg-amber-50/60 border border-amber-200 rounded-2xl p-5 shadow-xs">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-full bg-amber-500 animate-pulse"></span>
                <h3 class="text-sm font-bold text-amber-950 uppercase tracking-wider">Antrean Permohonan Masuk (Perlu Tindakan Guru Segera)</h3>
            </div>
            <a href="{{ route('loans.index', ['status' => 'pending']) }}" class="text-xs font-semibold text-amber-800 hover:text-amber-950">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($pendingLoans as $pending)
            <div class="bg-white border border-amber-200/80 rounded-xl p-4 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start">
                        <span class="text-xs font-bold text-indigo-600 font-mono">{{ $pending->loan_code }}</span>
                        <span class="text-[11px] text-slate-400">{{ $pending->borrow_date->format('d M') }} s/d {{ $pending->expected_return_date->format('d M') }}</span>
                    </div>
                    <div class="font-bold text-slate-900 mt-1">{{ $pending->user->name }}</div>
                    <div class="text-xs text-indigo-700 font-semibold mt-0.5">{{ $pending->item->name }} (Qty: {{ $pending->quantity }})</div>
                    <p class="text-xs text-slate-500 mt-1 italic">"{{ $pending->purpose }}"</p>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                    <form action="{{ route('loans.approve', $pending) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition">
                            Approve
                        </button>
                    </form>
                    <a href="{{ route('loans.index') }}" class="px-3 py-1.5 bg-slate-100 text-slate-700 text-xs font-semibold rounded-lg hover:bg-slate-200">
                        Detail
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Live Studio Equipment Circulation Monitoring -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Live Status Sirkulasi & Peminjaman Barang</h2>
                <p class="text-xs text-slate-500">Semua aktivitas sirkulasi studio termonitor secara real-time.</p>
            </div>
            <a href="{{ route('loans.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                Buka Menu Peminjaman Lengkap &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider block mb-2">Peralatan Studio Tersedia</span>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Siswa dapat langsung mengajukan peminjaman digital melalui aplikasi tanpa perlu datang mencatat manual di buku / spreadsheet.
                </p>
                <a href="{{ route('items.index') }}" class="mt-3 inline-block text-xs font-bold text-indigo-600 hover:underline">
                    Periksa Stok & Katalog &rarr;
                </a>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider block mb-2">Pencegahan Kerusakan</span>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Kondisi fisik diperiksa pada saat serah terima dan pengembalian. Setiap indikasi cacat fisik otomatis menghasilkan notice.
                </p>
                <a href="{{ route('notices.index') }}" class="mt-3 inline-block text-xs font-bold text-indigo-600 hover:underline">
                    Buka Notice Center &rarr;
                </a>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider block mb-2">Pencatatan Keterlambatan Otomatis</span>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Sistem mendeteksi keterlambatan tanpa rekap manual dan langsung memperbarui status serta masuk ke laporan rekap.
                </p>
                <a href="{{ route('reports.index') }}" class="mt-3 inline-block text-xs font-bold text-indigo-600 hover:underline">
                    Lihat Laporan Digital &rarr;
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
