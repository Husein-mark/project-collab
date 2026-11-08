@extends('layouts.app')

@section('title', 'Notifikasi & Notice Kerusakan Barang')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-1 bg-rose-100 text-rose-800 font-bold text-xs rounded-lg uppercase tracking-wider">Notice Kerusakan</span>
                <span class="text-xs text-slate-400">&bull; User Story #6</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">Pemberitahuan & Tindak Lanjut Kerusakan Barang</h1>
            <p class="text-sm text-slate-500">Notifikasi otomatis yang dihasilkan saat pengembalian barang terdeteksi mengalami kerusakan.</p>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex gap-2 border-b border-slate-200 pb-3">
        <a href="{{ route('notices.index') }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold {{ !request('status') || request('status') === 'all' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Semua Notice
        </a>
        <a href="{{ route('notices.index', ['status' => 'menunggu_tindakan']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold {{ request('status') === 'menunggu_tindakan' ? 'bg-rose-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Perlu Tindakan
        </a>
        <a href="{{ route('notices.index', ['status' => 'dalam_perbaikan']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold {{ request('status') === 'dalam_perbaikan' ? 'bg-amber-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Dalam Perbaikan (Servis)
        </a>
        <a href="{{ route('notices.index', ['status' => 'selesai']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold {{ request('status') === 'selesai' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Selesai Diperbaiki
        </a>
    </div>

    <!-- Notice List -->
    @if($notices->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($notices as $notice)
        @php $badge = $notice->level_badge; @endphp
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $badge['bg'] }} {{ $badge['text'] }}">
                        {{ $badge['label'] }}
                    </span>
                    <span class="text-xs text-slate-400">
                        {{ $notice->created_at->format('d M Y, H:i') }}
                    </span>
                </div>

                <h3 class="text-base font-bold text-slate-900 leading-snug">{{ $notice->item->name }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">Kode: <b class="font-semibold text-slate-700">{{ $notice->item->code }}</b> &bull; Peminjaman: <b class="text-indigo-600">{{ $notice->loan->loan_code }}</b></p>

                <div class="bg-slate-50 border border-slate-100 rounded-xl p-3.5 my-3 text-xs">
                    <div class="font-semibold text-slate-700">Peminjam Terakhir:</div>
                    <div class="text-slate-900 mt-0.5">{{ $notice->user->name }} (NIS: {{ $notice->user->nis_nip ?? '-' }})</div>
                    <div class="font-semibold text-slate-700 mt-2">Detail Kerusakan / Catatan:</div>
                    <p class="text-slate-600 mt-0.5 italic">"{{ $notice->notes ?? 'Tidak ada catatan rinci.' }}"</p>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Status Penanganan</span>
                    <span class="text-xs font-bold text-slate-800">{{ ucfirst(str_replace('_', ' ', $notice->status)) }}</span>
                </div>

                <form action="{{ route('notices.updateStatus', $notice) }}" method="POST" class="flex items-center space-x-2">
                    @csrf
                    <select name="status" onchange="this.form.submit()" class="text-xs bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 focus:ring-2 focus:ring-indigo-500">
                        <option value="menunggu_tindakan" {{ $notice->status === 'menunggu_tindakan' ? 'selected' : '' }}>Tindakan</option>
                        <option value="dalam_perbaikan" {{ $notice->status === 'dalam_perbaikan' ? 'selected' : '' }}>Kirim Servis</option>
                        <option value="selesai" {{ $notice->status === 'selesai' ? 'selected' : '' }}>Selesai Diperbaiki</option>
                    </select>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $notices->links() }}
    </div>
    @else
    <div class="bg-white rounded-2xl p-12 text-center border border-slate-100">
        <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <p class="text-slate-700 font-semibold text-sm">Tidak ada notice kerusakan aktif</p>
        <p class="text-slate-400 text-xs mt-1">Seluruh peralatan studio dalam kondisi terpantau baik.</p>
    </div>
    @endif
</div>
@endsection
