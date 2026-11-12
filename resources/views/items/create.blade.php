@extends('layouts.app')

@section('title', 'Tambah Barang Studio Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('items.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-indigo-600 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Katalog
        </a>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight mt-2">Tambah Peralatan Studio Baru</h1>
        <p class="text-sm text-slate-500 mt-1">Masukkan spesifikasi dan data inventaris barang studio sekolah.</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
        <form action="{{ route('items.store') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="code" class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">
                        Kode Barang <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="code" 
                           id="code" 
                           value="{{ old('code') }}" 
                           placeholder="Misal: CAM-003, MIC-005"
                           required 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 uppercase @error('code') border-rose-500 @enderror">
                    @error('code') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="category" class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">
                        Kategori <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="category" 
                           id="category" 
                           list="category-list" 
                           value="{{ old('category') }}" 
                           placeholder="Kamera, Audio, Lighting, Tripod, dll"
                           required 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('category') border-rose-500 @enderror">
                    <datalist id="category-list">
                        <option value="Kamera">
                        <option value="Lensa">
                        <option value="Audio">
                        <option value="Lighting">
                        <option value="Tripod & Grip">
                        <option value="Aksesoris & Kabel">
                    </datalist>
                    @error('category') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="name" class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">
                    Nama Peralatan / Barang <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="{{ old('name') }}" 
                       placeholder="Misal: Sony FX3 Cinema Line Camera"
                       required 
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-rose-500 @enderror">
                @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="total_stock" class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">
                        Jumlah Total Stok <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" 
                           name="total_stock" 
                           id="total_stock" 
                           value="{{ old('total_stock', 1) }}" 
                           min="1" 
                           required 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="condition" class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">
                        Kondisi Awal <span class="text-rose-500">*</span>
                    </label>
                    <select name="condition" id="condition" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none">
                        <option value="baik">Baik (Siap Pakai)</option>
                        <option value="rusak_ringan">Rusak Ringan</option>
                        <option value="rusak_berat">Rusak Berat</option>
                    </select>
                </div>

                <div>
                    <label for="location" class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">
                        Lokasi Simpan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="location" 
                           id="location" 
                           value="{{ old('location', 'Rak Studio Utama') }}" 
                           required 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none">
                </div>
            </div>

            <div>
                <label for="description" class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">
                    Deskripsi / Kelengkapan
                </label>
                <textarea name="description" 
                          id="description" 
                          rows="3" 
                          placeholder="Kelengkapan unit, nomor seri tambahan, atau catatan operasional..."
                          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end space-x-3">
                <a href="{{ route('items.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-md shadow-indigo-500/25 transition">
                    Simpan Barang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
