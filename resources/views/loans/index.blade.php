@extends('layouts.app')

@section('title', 'Persetujuan & Monitoring Peminjaman')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Kelola Peminjaman & Persetujuan Guru</h1>
            <p class="text-sm text-slate-500 mt-1">Setujui permohonan siswa, pantau pengembalian, dan kelola sirkulasi barang studio.</p>
        </div>
    </div>

    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-amber-50 border border-amber-200/60 rounded-2xl p-5 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-amber-700 uppercase tracking-wider">Menunggu Persetujuan</span>
                <span class="text-3xl font-extrabold text-amber-900 block mt-1">{{ $pendingCount }}</span>
            </div>
            <div class="w-12 h-12 bg-amber-500/10 text-amber-600 rounded-xl flex items-center justify-center font-bold">
                ⏳
            </div>
        </div>

        <div class="bg-indigo-50 border border-indigo-200/60 rounded-2xl p-5 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-indigo-700 uppercase tracking-wider">Peminjaman Aktif</span>
                <span class="text-3xl font-extrabold text-indigo-900 block mt-1">{{ $activeCount }}</span>
            </div>
            <div class="w-12 h-12 bg-indigo-500/10 text-indigo-600 rounded-xl flex items-center justify-center font-bold">
                📦
            </div>
        </div>

        <div class="bg-rose-50 border border-rose-200/60 rounded-2xl p-5 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-rose-700 uppercase tracking-wider">Terlambat Kembali</span>
                <span class="text-3xl font-extrabold text-rose-900 block mt-1">{{ $overdueCount }}</span>
            </div>
            <div class="w-12 h-12 bg-rose-500/10 text-rose-600 rounded-xl flex items-center justify-center font-bold">
                ⚠️
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('loans.index') }}" class="flex flex-col md:flex-row gap-3 items-center justify-between">
            <div class="flex-1 w-full">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari kode peminjaman, nama siswa, atau barang..." 
                       class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="flex items-center space-x-2 w-full md:w-auto">
                <select name="status" class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="all">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending (Menunggu Persetujuan)</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved (Disetujui)</option>
                    <option value="borrowed" {{ request('status') === 'borrowed' ? 'selected' : '' }}>Sedang Dipinjam</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Terlambat</option>
                    <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Selesai / Dikembalikan</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition">
                    Cari
                </button>
                <a href="{{ route('loans.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table of Loans -->
    @if($loans->count() > 0)
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Kode & Siswa</th>
                        <th class="px-6 py-4">Barang Studio</th>
                        <th class="px-6 py-4">Jadwal Pinjam</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi Guru</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($loans as $loan)
                    @php $badge = $loan->status_badge; @endphp
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-indigo-600 block">{{ $loan->loan_code }}</span>
                            <span class="font-semibold text-slate-900 block mt-0.5">{{ $loan->user->name }}</span>
                            <span class="text-xs text-slate-400">NIS: {{ $loan->user->nis_nip ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-slate-900 block">{{ $loan->item->name }}</span>
                            <span class="text-xs text-indigo-600">{{ $loan->item->category }} &bull; Qty: {{ $loan->quantity }} unit</span>
                            <p class="text-xs text-slate-500 mt-1 italic">"{{ $loan->purpose }}"</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs">
                            <div>Mulai: <span class="font-semibold text-slate-700">{{ $loan->borrow_date->format('d M Y') }}</span></div>
                            <div class="mt-0.5">Batas: <span class="font-semibold text-slate-900">{{ $loan->expected_return_date->format('d M Y') }}</span></div>
                            @if($loan->actual_return_date)
                            <div class="text-emerald-600 mt-0.5">Kembali: {{ $loan->actual_return_date->format('d M Y') }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge['bg'] }} {{ $badge['text'] }}">
                                {{ $badge['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($loan->status === 'pending')
                            <div class="flex items-center justify-center space-x-2">
                                <form action="{{ route('loans.approve', $loan) }}" method="POST" onsubmit="return confirm('Setujui permohonan peminjaman ini?')">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-xs transition">
                                        Setujui (Approve)
                                    </button>
                                </form>

                                <button type="button" 
                                        onclick="openRejectModal('{{ $loan->id }}', '{{ $loan->loan_code }}', '{{ $loan->user->name }}')"
                                        class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold rounded-lg transition border border-rose-200">
                                    Tolak
                                </button>
                            </div>
                            @elseif(in_array($loan->status, ['approved', 'borrowed', 'overdue']))
                            <a href="{{ route('loans.inspect', $loan) }}" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-lg transition">
                                Kelola / Pengembalian &rarr;
                            </a>
                            @else
                            <span class="text-xs text-slate-400">Selesai</span>
                            @endif
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
        <p class="text-slate-700 font-semibold text-sm">Tidak ada data peminjaman ditemukan.</p>
    </div>
    @endif
</div>

<!-- Modal Reject -->
<div id="rejectModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900 mb-1">Tolak Permohonan Peminjaman</h3>
        <p id="rejectModalSub" class="text-xs text-slate-500 mb-4"></p>

        <form id="rejectForm" method="POST" action="">
            @csrf
            <div class="mb-4">
                <label for="rejection_reason" class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">Alasan Penolakan <span class="text-rose-500">*</span></label>
                <textarea name="rejection_reason" 
                          id="rejection_reason" 
                          required 
                          rows="3" 
                          placeholder="Contoh: Barang sedang dalam jadwal maintenance studio / Jadwal bentrok"
                          class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-semibold rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-xl shadow-xs transition">
                    Konfirmasi Tolak
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal(id, code, studentName) {
        document.getElementById('rejectModalSub').innerText = 'Kode: ' + code + ' | Siswa: ' + studentName;
        document.getElementById('rejectForm').action = '/loans/' + id + '/reject';
        document.getElementById('rejectModal').classList.remove('hidden');
        document.getElementById('rejectModal').classList.add('flex');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('rejectModal').classList.remove('flex');
    }
</script>
@endsection
