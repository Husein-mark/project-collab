@extends('layouts.app')

@section('title', 'Form Pengajuan Peminjaman')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('items.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-indigo-600 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Katalog Barang
        </a>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight mt-2">Form Permohonan Peminjaman Studio</h1>
        <p class="text-sm text-slate-500 mt-1">Isi formulir digital di bawah ini. Pengajuan akan langsung masuk ke sistem monitoring guru.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sm:p-8">
        <form action="{{ route('loans.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Pemohon Info (Auto) -->
            <div class="bg-indigo-50/70 border border-indigo-100 rounded-xl p-4 flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-indigo-700 uppercase tracking-wider block">Data Peminjam</span>
                    <span class="text-sm font-semibold text-slate-900">{{ auth()->user()->name }} (NIS: {{ auth()->user()->nis_nip ?? '-' }})</span>
                </div>
                <span class="px-2.5 py-1 bg-white text-indigo-700 rounded-lg text-xs font-semibold shadow-xs">Siswa Aktif</span>
            </div>

            <!-- Pilih Barang -->
            <div>
                <label for="item_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-2">
                    Pilih Barang Studio <span class="text-rose-500">*</span>
                </label>
                <select name="item_id" id="item_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('item_id') border-rose-500 @enderror">
                    <option value="">-- Pilih Barang yang Tersedia --</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ (old('item_id') == $item->id || (isset($selectedItem) && $selectedItem->id == $item->id)) ? 'selected' : '' }}>
                            {{ $item->code }} - {{ $item->name }} (Sisa Stok: {{ $item->available_stock }})
                        </option>
                    @endforeach
                </select>
                @error('item_id')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jumlah -->
            <div>
                <label for="quantity" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-2">
                    Jumlah Barang <span class="text-rose-500">*</span>
                </label>
                <input type="number" 
                       name="quantity" 
                       id="quantity" 
                       value="{{ old('quantity', 1) }}" 
                       min="1" 
                       required 
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('quantity') border-rose-500 @enderror">
                @error('quantity')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tanggal Pinjam & Kembali -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="borrow_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-2">
                        Tanggal Mulai Pinjam <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" 
                           name="borrow_date" 
                           id="borrow_date" 
                           value="{{ old('borrow_date', date('Y-m-d')) }}" 
                           required 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('borrow_date') border-rose-500 @enderror">
                    @error('borrow_date')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="expected_return_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-2">
                        Tanggal Rencana Pengembalian <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" 
                           name="expected_return_date" 
                           id="expected_return_date" 
                           value="{{ old('expected_return_date', date('Y-m-d', strtotime('+2 days'))) }}" 
                           required 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('expected_return_date') border-rose-500 @enderror">
                    @error('expected_return_date')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Keperluan -->
            <div>
                <label for="purpose" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-2">
                    Keperluan Peminjaman <span class="text-rose-500">*</span>
                </label>
                <textarea name="purpose" 
                          id="purpose" 
                          rows="3" 
                          required 
                          placeholder="Contoh: Praktikum Pembuatan Film Pendek Mapel Seni Budaya / Liputan Kegiatan OSIS"
                          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('purpose') border-rose-500 @enderror">{{ old('purpose') }}</textarea>
                @error('purpose')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-slate-400 mt-1">Harap jelaskan keperluan peminjaman secara jelas untuk pertimbangan guru pembimbing.</p>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('items.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-md shadow-indigo-500/25 transition">
                    Kirim Permohonan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
