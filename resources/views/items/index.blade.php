@extends('layouts.app')

@section('title', 'Daftar Barang Studio')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Katalog & Ketersediaan Barang Studio</h1>
            <p class="text-sm text-slate-500 mt-1">Pantau stok peralatan studio secara real-time sebelum mengajukan peminjaman.</p>
        </div>
        @if(auth()->user()->isSiswa())
        <div>
            <a href="{{ route('loans.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Ajukan Peminjaman
            </a>
        </div>
        @endif
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs">
        <form method="GET" action="{{ route('items.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Pencarian</label>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nama barang atau kode (mis: Sony, MIC)..." 
                       class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Kategori</label>
                <select name="category" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="all">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition">
                    Filter
                </button>
                <a href="{{ route('items.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Grid Items -->
    @if($items->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($items as $item)
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs hover:shadow-md transition flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-start gap-2 mb-3">
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700">
                        {{ $item->code }}
                    </span>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-lg {{ $item->available_stock > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ $item->available_stock > 0 ? 'Tersedia: '.$item->available_stock.'/'.$item->total_stock : 'Sedang Dipinjam' }}
                    </span>
                </div>

                <h3 class="text-base font-bold text-slate-900 leading-snug">{{ $item->name }}</h3>
                <p class="text-xs text-indigo-600 font-medium mt-0.5">{{ $item->category }} &bull; Lokasi: {{ $item->location }}</p>
                
                <p class="text-xs text-slate-500 mt-2 line-clamp-2">
                    {{ $item->description ?? 'Tidak ada deskripsi tambahan.' }}
                </p>
            </div>

            <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                <div class="text-xs">
                    <span class="text-slate-400">Kondisi:</span>
                    <span class="font-medium {{ $item->condition === 'baik' ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ ucfirst(str_replace('_', ' ', $item->condition)) }}
                    </span>
                </div>
                @if(auth()->user()->isSiswa() && $item->available_stock > 0)
                <a href="{{ route('loans.create', ['item_id' => $item->id]) }}" 
                   class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-lg transition">
                    Pilih Barang &rarr;
                </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $items->links() }}
    </div>
    @else
    <div class="bg-white rounded-2xl p-12 text-center border border-slate-100">
        <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
        </div>
        <p class="text-slate-700 font-semibold text-sm">Tidak ada barang yang cocok</p>
        <p class="text-slate-400 text-xs mt-1">Coba ubah kata kunci pencarian atau reset filter.</p>
    </div>
    @endif
</div>
@endsection
