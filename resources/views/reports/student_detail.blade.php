@extends('layouts.app')

@section('title', 'Riwayat Siswa - ' . $user->name)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('reports.students') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-indigo-600 transition mb-2">
                &larr; Kembali ke Daftar Siswa
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Evaluasi Kedisiplinan: {{ $user->name }}</h1>
            <p class="text-sm text-slate-500">NIS: {{ $user->nis_nip ?? '-' }} &bull; Email: {{ $user->email }} &bull; No. HP: {{ $user->phone ?? '-' }}</p>
        </div>
    </div>

    <!-- Discipline Metric Box -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Skor Kedisiplinan</span>
            <div class="flex items-baseline space-x-2 mt-1">
                <span class="text-3xl font-black {{ $disciplineScore >= 80 ? 'text-emerald-600' : ($disciplineScore >= 60 ? 'text-amber-600' : 'text-rose-600') }}">
                    {{ $disciplineScore }}%
                </span>
                <span class="text-xs text-slate-400">/ 100%</span>
            </div>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Total Transaksi</span>
            <span class="text-2xl font-bold text-slate-900 mt-1 block">{{ $totalLoans }} Kali</span>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-rose-500 uppercase tracking-wider block">Pernah Terlambat</span>
            <span class="text-2xl font-bold text-rose-600 mt-1 block">{{ $overdueCount }} Kali</span>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-amber-500 uppercase tracking-wider block">Kasus Kerusakan</span>
            <span class="text-2xl font-bold text-amber-600 mt-1 block">{{ $damageCount }} Kali</span>
        </div>
    </div>

    <!-- Riwayat Transaksi Siswa -->
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs">
        <div class="p-4 bg-slate-50 border-b border-slate-200">
            <h3 class="text-sm font-bold text-slate-800">Riwayat Seluruh Peminjaman Oleh {{ $user->name }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase">
                    <tr>
                        <th class="px-6 py-3">Kode & Barang</th>
                        <th class="px-6 py-3">Tanggal Pinjam - Target</th>
                        <th class="px-6 py-3">Tanggal Kembali</th>
                        <th class="px-6 py-3">Ketepatan Waktu</th>
                        <th class="px-6 py-3">Kondisi Pengembalian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($loans as $loan)
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-6 py-4">
                            <span class="text-xs font-mono font-bold text-indigo-600 block">{{ $loan->loan_code }}</span>
                            <span class="font-bold text-slate-900">{{ $loan->item->name }}</span>
                            <span class="text-xs text-slate-400 block">Qty: {{ $loan->quantity }} &bull; "{{ $loan->purpose }}"</span>
                        </td>
                        <td class="px-6 py-4 text-xs whitespace-nowrap">
                            {{ $loan->borrow_date->format('d/m/Y') }} &rarr; <span class="font-bold">{{ $loan->expected_return_date->format('d/m/Y') }}</span>
                        </td>
                        <td class="px-6 py-4 text-xs whitespace-nowrap">
                            {{ $loan->actual_return_date ? $loan->actual_return_date->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs">
                            @if($loan->is_overdue || $loan->status === 'overdue')
                                <span class="px-2 py-0.5 rounded-full font-bold bg-rose-100 text-rose-800">
                                    Terlambat +{{ $loan->overdue_days }} Hari
                                </span>
                            @elseif($loan->status === 'returned')
                                <span class="px-2 py-0.5 rounded-full font-medium bg-emerald-100 text-emerald-800">
                                    Tepat Waktu
                                </span>
                            @else
                                <span class="text-slate-400">{{ $loan->status_badge['label'] }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs">
                            @if($loan->damage_reported)
                                <span class="font-bold text-rose-600 block">⚠️ {{ ucfirst(str_replace('_', ' ', $loan->return_condition)) }}</span>
                                <span class="text-slate-500 block truncate max-w-xs">{{ $loan->damage_notes }}</span>
                            @elseif($loan->return_condition)
                                <span class="text-emerald-600 font-medium">✓ {{ ucfirst(str_replace('_', ' ', $loan->return_condition)) }}</span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-xs text-slate-400">
                            Siswa ini belum pernah meminjam barang studio.
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
