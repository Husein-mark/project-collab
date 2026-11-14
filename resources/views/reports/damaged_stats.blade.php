@extends('layouts.app')

@section('title', 'Statistik Kerusakan & Kebijakan Studio')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-1 bg-rose-100 text-rose-800 font-bold text-xs rounded-lg uppercase tracking-wider">Analisis Pengadaan & Servis</span>
                <span class="text-xs text-slate-400">&bull; User Story #15</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">Rekap Barang Paling Sering Rusak & Evaluasi Kebijakan</h1>
            <p class="text-sm text-slate-500">Memberikan wawasan (insight) data kerusakan peralatan untuk dasar kebijakan perawatan preventif dan usulan pengadaan sekolah.</p>
        </div>
        <div>
            <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                &larr; Kembali ke Laporan Utama
            </a>
        </div>
    </div>

    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Total Kasus Kerusakan</span>
            <span class="text-2xl font-black text-rose-600 mt-1 block">{{ $totalDamageCases }} Insiden</span>
            <span class="text-[11px] text-slate-400">Tercatat sepanjang operasional</span>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-amber-500 uppercase tracking-wider block">Sedang Diservis</span>
            <span class="text-2xl font-black text-amber-600 mt-1 block">{{ $inRepairCount }} Unit</span>
            <span class="text-[11px] text-amber-600">Dalam pengerjaan teknisi</span>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider block">Berhasil Diperbaiki</span>
            <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ $completedRepairCount }} Unit</span>
            <span class="text-[11px] text-emerald-600">Kembali ke inventaris aktif</span>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-rose-700 uppercase tracking-wider block">Menunggu Tindakan</span>
            <span class="text-2xl font-black text-rose-700 mt-1 block">{{ $pendingActionCount }} Kasus</span>
            <span class="text-[11px] text-rose-600 font-semibold">Perlu keputusan guru</span>
        </div>
    </div>

    <!-- Rekap Ranking Barang Paling Sering Rusak -->
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs">
        <div class="p-5 border-b border-slate-200/80 bg-slate-50/50 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Peringkat Kerentanan Barang Studio</h2>
                <p class="text-xs text-slate-500">Barang dengan frekuensi kerusakan tertinggi yang perlu perhatian khusus atau diganti.</p>
            </div>
        </div>

        @if($itemsWithDamage->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Nama & Kode Barang</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4 text-center">Total Kerusakan</th>
                        <th class="px-6 py-4 text-center">Rusak Berat</th>
                        <th class="px-6 py-4 text-center">Hilang</th>
                        <th class="px-6 py-4">Rekomendasi Kebijakan Guru</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($itemsWithDamage as $item)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-6 py-4">
                            <span class="text-xs font-mono font-bold text-indigo-600 block">{{ $item->code }}</span>
                            <span class="font-bold text-slate-900">{{ $item->name }}</span>
                            <span class="text-xs text-slate-400 block mt-0.5">Stok Saat Ini: {{ $item->available_stock }}/{{ $item->total_stock }}</span>
                        </td>
                        <td class="px-6 py-4 text-xs font-semibold text-slate-700">
                            {{ $item->category }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-black bg-rose-100 text-rose-800">
                                {{ $item->total_damage_count }}x Insiden
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-xs font-bold text-rose-600">
                            {{ $item->heavy_damage_count }} unit
                        </td>
                        <td class="px-6 py-4 text-center text-xs font-bold text-slate-700">
                            {{ $item->lost_count }} unit
                        </td>
                        <td class="px-6 py-4 text-xs">
                            @if($item->total_damage_count >= 3 || $item->lost_count > 0)
                                <div class="p-2.5 bg-rose-50 border border-rose-200 rounded-xl text-rose-800">
                                    <b class="font-bold">Prioritas Pengadaan Baru:</b> Barang ini memiliki tingkat risiko tinggi. Disarankan menambah hardcase pelindung atau menganggarkan unit pengganti pada RAPBS.
                                </div>
                            @elseif($item->heavy_damage_count > 0)
                                <div class="p-2.5 bg-amber-50 border border-amber-200 rounded-xl text-amber-800">
                                    <b class="font-bold">Servis Berkala / Kalibrasi:</b> Jadwalkan perawatan rutin setiap akhir bulan oleh teknisi resmi.
                                </div>
                            @else
                                <div class="p-2.5 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800">
                                    <b class="font-bold">Kondisi Terkendali:</b> Lakukan briefing cara pemakaian yang benar kepada siswa sebelum dipinjam.
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="py-12 text-center text-slate-400 text-xs">
            Belum ada rekap insiden kerusakan barang yang tercatat di sistem studio.
        </div>
        @endif
    </div>
</div>
@endsection
