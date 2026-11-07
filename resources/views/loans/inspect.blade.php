@extends('layouts.app')

@section('title', 'Pemeriksaan Kondisi Barang - ' . $loan->loan_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="mb-4">
        <a href="{{ route('loans.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-indigo-600 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar Peminjaman
        </a>
        <div class="flex items-center justify-between mt-2">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Inspeksi Kondisi Fisik Barang</h1>
                <p class="text-sm text-slate-500 mt-0.5">Pencatatan kondisi saat serah terima dan pengembalian barang studio.</p>
            </div>
            @php $badge = $loan->status_badge; @endphp
            <span class="px-3 py-1.5 rounded-full text-xs font-bold {{ $badge['bg'] }} {{ $badge['text'] }}">
                {{ $badge['label'] }}
            </span>
        </div>
    </div>

    <!-- Loan & Equipment Summary Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Peminjam</span>
            <span class="text-base font-bold text-slate-900 block mt-1">{{ $loan->user->name }}</span>
            <span class="text-xs text-slate-500">NIS: {{ $loan->user->nis_nip ?? '-' }} &bull; {{ $loan->user->phone ?? 'Tanpa No. HP' }}</span>
        </div>

        <div>
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Barang Dipinjam</span>
            <span class="text-base font-bold text-slate-900 block mt-1">{{ $loan->item->name }}</span>
            <span class="text-xs text-indigo-600 font-semibold">{{ $loan->item->code }} (Qty: {{ $loan->quantity }} unit)</span>
        </div>

        <div>
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Jadwal</span>
            <span class="text-xs text-slate-700 block mt-1">Mulai: <b class="font-semibold">{{ $loan->borrow_date->format('d M Y') }}</b></span>
            <span class="text-xs text-slate-700 block">Target Kembali: <b class="font-semibold text-indigo-700">{{ $loan->expected_return_date->format('d M Y') }}</b></span>
        </div>
    </div>

    @if($loan->status === 'approved')
    <!-- Form Serah Terima (Handover) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
        <h2 class="text-lg font-bold text-slate-900 mb-1">Tahap 1: Serah Terima Barang ke Siswa</h2>
        <p class="text-xs text-slate-500 mb-6">Periksa kelengkapan dan kondisi fisik barang sebelum diserahkan kepada peminjam.</p>

        <form action="{{ route('loans.handover', $loan) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="initial_condition" class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">
                    Kondisi Fisik Saat Diberikan <span class="text-rose-500">*</span>
                </label>
                <select name="initial_condition" id="initial_condition" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="baik">Sangat Baik / Lengkap Normal</option>
                    <option value="lecet_pemakaian">Lecet Pemakaian Wajar (Fungsi Normal)</option>
                </select>
            </div>

            <div>
                <label for="initial_notes" class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">
                    Catatan Kelengkapan Awal (Opsional)
                </label>
                <input type="text" 
                       name="initial_notes" 
                       id="initial_notes" 
                       placeholder="Misal: Termasuk 1 baterai cadangan + charger dan tas kamera"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-md shadow-indigo-500/25 transition">
                    Konfirmasi Serahkan Barang &rarr;
                </button>
            </div>
        </form>
    </div>
    @endif

    @if(in_array($loan->status, ['borrowed', 'overdue']))
    <!-- Form Pengembalian & Kondisi Akhir -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
        <h2 class="text-lg font-bold text-slate-900 mb-1">Tahap 2: Pengembalian Barang & Deteksi Kerusakan</h2>
        <p class="text-xs text-slate-500 mb-6">Pencatatan ini menggantikan spreadsheet manual. Jika terjadi kerusakan, sistem otomatis membuat notice peringatan.</p>

        <form action="{{ route('loans.processReturn', $loan) }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="actual_return_date" class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">
                        Tanggal Pengembalian Aktual <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" 
                           name="actual_return_date" 
                           id="actual_return_date" 
                           value="{{ date('Y-m-d') }}" 
                           required 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="return_condition" class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">
                        Kondisi Barang Saat Dikembalikan <span class="text-rose-500">*</span>
                    </label>
                    <select name="return_condition" id="return_condition" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="baik">Kondisi Baik & Normal</option>
                        <option value="rusak_ringan">Rusak Ringan (Lecet parah / port kendur)</option>
                        <option value="rusak_berat">Rusak Berat (Mati total / lensa retak / pecah)</option>
                        <option value="hilang">Barang Hilang / Belum Ditemukan</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="damage_notes" class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">
                    Catatan Kondisi / Kerusakan (Notice Guru)
                </label>
                <textarea name="damage_notes" 
                          id="damage_notes" 
                          rows="3" 
                          placeholder="Jelaskan detail kondisi barang atau kerusakan yang ditemukan jika ada..."
                          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end space-x-3">
                <a href="{{ route('loans.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl shadow-md shadow-emerald-500/25 transition">
                    Simpan Pengembalian & Perbarui Laporan
                </button>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection
