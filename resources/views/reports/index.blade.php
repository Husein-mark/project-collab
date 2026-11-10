@extends('layouts.app')

@section('title', 'Laporan Peminjaman & Rekap Studio')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-1 bg-indigo-100 text-indigo-800 font-bold text-xs rounded-lg uppercase tracking-wider">Rekap Otomatis Digital</span>
                <span class="text-xs text-slate-400">&bull; User Story #10</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">Laporan Peminjaman & Rekap Keterlambatan</h1>
            <p class="text-sm text-slate-500">Menggantikan rekapan spreadsheet manual. Keterlambatan dan status pengembalian tercatat otomatis secara real-time.</p>
        </div>

        @if(Route::has('reports.export'))
        <div class="flex items-center space-x-2">
            <a href="{{ route('reports.export', request()->all()) }}" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Ekspor Laporan (CSV / Excel)
            </a>
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-sm rounded-xl transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Cetak
            </button>
        </div>
        @endif
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('reports.index') }}" class="bg-white border {{ !request('type') ? 'border-indigo-500 ring-2 ring-indigo-100' : 'border-slate-200' }} rounded-2xl p-4 transition shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Total Transaksi</span>
            <span class="text-2xl font-bold text-slate-900 mt-1 block">{{ $totalLoans }}</span>
            <span class="text-[11px] text-slate-500">Seluruh Peminjaman</span>
        </a>

        <a href="{{ route('reports.index', ['type' => 'overdue']) }}" class="bg-white border {{ request('type') === 'overdue' ? 'border-rose-500 ring-2 ring-rose-100' : 'border-slate-200' }} rounded-2xl p-4 transition shadow-xs">
            <span class="text-xs font-semibold text-rose-500 uppercase tracking-wider block">Pernah Terlambat</span>
            <span class="text-2xl font-bold text-rose-600 mt-1 block">{{ $totalOverdue }}</span>
            <span class="text-[11px] text-rose-500">Auto Terdeteksi Sistem</span>
        </a>

        <a href="{{ route('reports.index', ['type' => 'damaged']) }}" class="bg-white border {{ request('type') === 'damaged' ? 'border-amber-500 ring-2 ring-amber-100' : 'border-slate-200' }} rounded-2xl p-4 transition shadow-xs">
            <span class="text-xs font-semibold text-amber-500 uppercase tracking-wider block">Kerusakan Terdeteksi</span>
            <span class="text-2xl font-bold text-amber-600 mt-1 block">{{ $totalDamaged }}</span>
            <span class="text-[11px] text-amber-600">Notice Masuk Laporan</span>
        </a>

        <a href="{{ route('reports.index', ['type' => 'returned']) }}" class="bg-white border {{ request('type') === 'returned' ? 'border-emerald-500 ring-2 ring-emerald-100' : 'border-slate-200' }} rounded-2xl p-4 transition shadow-xs">
            <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider block">Selesai Kembali</span>
            <span class="text-2xl font-bold text-emerald-700 mt-1 block">{{ $totalReturned }}</span>
            <span class="text-[11px] text-emerald-600">Selesai & Tertib</span>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Kategori Laporan</label>
                <select name="type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Data Peminjaman</option>
                    <option value="overdue" {{ request('type') === 'overdue' ? 'selected' : '' }}>Hanya Data Keterlambatan</option>
                    <option value="damaged" {{ request('type') === 'damaged' ? 'selected' : '' }}>Hanya Data Kerusakan</option>
                    <option value="returned" {{ request('type') === 'returned' ? 'selected' : '' }}>Hanya Yang Sudah Selesai</option>
                    <option value="active" {{ request('type') === 'active' ? 'selected' : '' }}>Hanya Sedang Berjalan</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Mulai Tanggal Pinjam</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none">
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition">
                    Terapkan
                </button>
                <a href="{{ route('reports.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Report Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Kode Transaksi</th>
                        <th class="px-6 py-4">Peminjam (Siswa)</th>
                        <th class="px-6 py-4">Barang Studio</th>
                        <th class="px-6 py-4">Tgl Pinjam - Target</th>
                        <th class="px-6 py-4">Tgl Kembali Aktual</th>
                        <th class="px-6 py-4">Keterlambatan (Otomatis)</th>
                        <th class="px-6 py-4">Kondisi Pengembalian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($loans as $loan)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-6 py-4 font-mono text-xs font-bold text-indigo-600">
                            {{ $loan->loan_code }}
                        </td>
                        <td class="px-6 py-4 font-semibold text-slate-900">
                            {{ $loan->user->name }}
                            <span class="block text-xs font-normal text-slate-400">NIS: {{ $loan->user->nis_nip ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-slate-900 block">{{ $loan->item->name }}</span>
                            <span class="text-xs text-slate-400">{{ $loan->item->code }} &bull; Qty: {{ $loan->quantity }}</span>
                        </td>
                        <td class="px-6 py-4 text-xs whitespace-nowrap">
                            {{ $loan->borrow_date->format('d/m/Y') }} &rarr; <span class="font-bold text-slate-800">{{ $loan->expected_return_date->format('d/m/Y') }}</span>
                        </td>
                        <td class="px-6 py-4 text-xs whitespace-nowrap">
                            @if($loan->actual_return_date)
                                <span class="font-semibold text-emerald-600">{{ $loan->actual_return_date->format('d/m/Y') }}</span>
                            @else
                                <span class="text-slate-400 italic">Belum kembali</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($loan->is_overdue || $loan->status === 'overdue')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                    ⚠️ Terlambat +{{ $loan->overdue_days }} Hari
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                    ✓ Tepat Waktu
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs">
                            @if($loan->damage_reported)
                                <span class="font-bold text-rose-600 block">⚠️ {{ ucfirst(str_replace('_', ' ', $loan->return_condition)) }}</span>
                                <span class="text-[11px] text-slate-500 block truncate max-w-xs">{{ $loan->damage_notes }}</span>
                            @elseif($loan->return_condition)
                                <span class="font-medium text-emerald-600">✓ {{ ucfirst(str_replace('_', ' ', $loan->return_condition)) }}</span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">
                            Tidak ada data laporan yang sesuai kriteria filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $loans->links() }}
        </div>
    </div>
</div>
@endsection
