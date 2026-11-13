@extends('layouts.app')

@section('title', 'Evaluasi Kedisiplinan Siswa')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-1 bg-indigo-100 text-indigo-800 font-bold text-xs rounded-lg uppercase tracking-wider">Evaluasi Kedisiplinan</span>
                <span class="text-xs text-slate-400">&bull; User Story #12</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">Riwayat & Kedisiplinan Peminjaman Tiap Siswa</h1>
            <p class="text-sm text-slate-500">Evaluasi kedisiplinan peminjam berdasarkan kepatuhan tenggat waktu dan perlakuan terhadap fisik barang.</p>
        </div>
        <div>
            <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                &larr; Kembali ke Laporan Utama
            </a>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('reports.students') }}" class="flex gap-3">
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Cari nama siswa atau NIS..." 
                   class="flex-1 px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition">
                Cari Siswa
            </button>
        </form>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Data Siswa</th>
                        <th class="px-6 py-4 text-center">Total Peminjaman</th>
                        <th class="px-6 py-4 text-center">Sedang Membawa</th>
                        <th class="px-6 py-4 text-center">Riwayat Terlambat</th>
                        <th class="px-6 py-4 text-center">Kasus Kerusakan</th>
                        <th class="px-6 py-4 text-center">Status Kedisiplinan</th>
                        <th class="px-6 py-4 text-center">Aksi Guru</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($students as $stu)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-6 py-4">
                            <span class="font-bold text-slate-900 block">{{ $stu->name }}</span>
                            <span class="text-xs text-slate-400">NIS: {{ $stu->nis_nip ?? '-' }} &bull; {{ $stu->email }}</span>
                        </td>
                        <td class="px-6 py-4 text-center font-bold text-slate-800">
                            {{ $stu->total_loans }}x
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($stu->active_loans > 0)
                                <span class="px-2.5 py-1 bg-indigo-100 text-indigo-700 font-bold text-xs rounded-full">
                                    {{ $stu->active_loans }} unit
                                </span>
                            @else
                                <span class="text-xs text-slate-400">0</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($stu->overdue_loans > 0)
                                <span class="px-2.5 py-1 bg-rose-100 text-rose-700 font-bold text-xs rounded-full">
                                    {{ $stu->overdue_loans }}x terlambat
                                </span>
                            @else
                                <span class="text-xs text-emerald-600 font-medium">0 (Tertib)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($stu->damaged_loans > 0)
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-800 font-bold text-xs rounded-full">
                                    {{ $stu->damaged_loans }}x kasus
                                </span>
                            @else
                                <span class="text-xs text-emerald-600 font-medium">0 (Aman)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($stu->total_loans == 0)
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-600 text-xs font-medium rounded-full">Belum Ada Catatan</span>
                            @elseif($stu->overdue_loans == 0 && $stu->damaged_loans == 0)
                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full">Sangat Disiplin ⭐</span>
                            @elseif($stu->overdue_loans > 2 || $stu->damaged_loans > 1)
                                <span class="px-2.5 py-1 bg-rose-100 text-rose-800 text-xs font-bold rounded-full">Perlu Pembinaan ⚠️</span>
                            @else
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-800 text-xs font-bold rounded-full">Cukup Tertib</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <a href="{{ route('reports.students.detail', $stu) }}" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-lg transition">
                                Lihat Riwayat &rarr;
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection
