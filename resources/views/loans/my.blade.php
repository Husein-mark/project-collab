@extends('layouts.app')

@section('title', 'Peminjaman Saya')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Daftar Peminjaman Saya</h1>
            <p class="text-sm text-slate-500 mt-1">Pantau status approval, batas waktu pengembalian, dan riwayat peminjaman Anda.</p>
        </div>
        <div>
            <a href="{{ route('loans.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Ajukan Pinjam Baru
            </a>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex flex-wrap gap-2 border-b border-slate-200 pb-3">
        <a href="{{ route('loans.my') }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold {{ !request('status') || request('status') === 'all' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Semua
        </a>
        <a href="{{ route('loans.my', ['status' => 'pending']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold {{ request('status') === 'pending' ? 'bg-amber-500 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Menunggu Approval
        </a>
        <a href="{{ route('loans.my', ['status' => 'approved']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold {{ request('status') === 'approved' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Disetujui
        </a>
        <a href="{{ route('loans.my', ['status' => 'borrowed']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold {{ request('status') === 'borrowed' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Sedang Dipinjam
        </a>
        <a href="{{ route('loans.my', ['status' => 'overdue']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold {{ request('status') === 'overdue' ? 'bg-rose-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Terlambat
        </a>
        <a href="{{ route('loans.my', ['status' => 'returned']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold {{ request('status') === 'returned' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Dikembalikan
        </a>
    </div>

    <!-- Loan List Cards / Table -->
    @if($loans->count() > 0)
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-xs font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Kode & Barang</th>
                        <th class="px-6 py-4">Tgl Pinjam - Kembali</th>
                        <th class="px-6 py-4">Keperluan</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Kondisi / Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($loans as $loan)
                    @php $badge = $loan->status_badge; @endphp
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-indigo-600 block">{{ $loan->loan_code }}</span>
                            <span class="font-semibold text-slate-900 block mt-0.5">{{ $loan->item->name }}</span>
                            <span class="text-xs text-slate-400">Qty: {{ $loan->quantity }} unit</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-xs font-medium text-slate-700">
                                <div>Pinjam: <span class="font-semibold">{{ $loan->borrow_date->format('d M Y') }}</span></div>
                                <div class="mt-0.5">Batas: <span class="font-semibold text-slate-900">{{ $loan->expected_return_date->format('d M Y') }}</span></div>
                                @if($loan->actual_return_date)
                                <div class="text-emerald-600 mt-0.5">Kembali: {{ $loan->actual_return_date->format('d M Y') }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs text-slate-600 max-w-xs line-clamp-2">{{ $loan->purpose }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge['bg'] }} {{ $badge['text'] }}">
                                {{ $badge['label'] }}
                            </span>
                            @if($loan->status === 'rejected' && $loan->rejection_reason)
                                <p class="text-[11px] text-rose-600 mt-1 max-w-xs">Alasan: {{ $loan->rejection_reason }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs">
                                <div>Kondisi Awal: <span class="font-medium text-slate-700">{{ ucfirst(str_replace('_', ' ', $loan->initial_condition)) }}</span></div>
                                @if($loan->return_condition)
                                <div class="mt-0.5">Kondisi Balik: <span class="font-semibold {{ $loan->return_condition === 'baik' ? 'text-emerald-600' : 'text-rose-600' }}">{{ ucfirst(str_replace('_', ' ', $loan->return_condition)) }}</span></div>
                                @endif
                                @if($loan->damage_notes)
                                <div class="text-[11px] text-rose-600 mt-0.5">Catatan: {{ $loan->damage_notes }}</div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $loans->links() }}
        </div>
    </div>
    @else
    <div class="bg-white rounded-2xl p-12 text-center border border-slate-100">
        <div class="w-12 h-12 bg-indigo-50 text-indigo-400 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        </div>
        <p class="text-slate-700 font-semibold text-sm">Belum ada pengajuan peminjaman</p>
        <p class="text-slate-400 text-xs mt-1">Gunakan tombol 'Ajukan Pinjam Baru' untuk meminjam peralatan studio.</p>
    </div>
    @endif
</div>
@endsection
