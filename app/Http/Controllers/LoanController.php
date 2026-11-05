<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LoanController extends Controller
{
    /**
     * Tampilan form pengajuan peminjaman oleh siswa (US #2)
     */
    public function create(Request $request)
    {
        $items = Item::where('available_stock', '>', 0)
            ->where('condition', '!=', 'rusak_berat')
            ->orderBy('name')
            ->get();

        $selectedItem = null;
        if ($request->filled('item_id')) {
            $selectedItem = Item::find($request->item_id);
        }

        return view('loans.create', compact('items', 'selectedItem'));
    }

    /**
     * Simpan pengajuan peminjaman baru oleh siswa (US #2)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'borrow_date' => ['required', 'date', 'after_or_equal:today'],
            'expected_return_date' => ['required', 'date', 'after_or_equal:borrow_date'],
            'purpose' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'item_id.required' => 'Silakan pilih barang yang ingin dipinjam.',
            'borrow_date.after_or_equal' => 'Tanggal pinjam tidak boleh di masa lampau.',
            'expected_return_date.after_or_equal' => 'Tanggal pengembalian harus sama atau setelah tanggal pinjam.',
            'purpose.required' => 'Tuliskan keperluan penggunaan barang studio.',
        ]);

        $item = Item::findOrFail($validated['item_id']);

        if ($item->available_stock < $validated['quantity']) {
            return back()->withInput()->with('error', "Stok barang '{$item->name}' tidak mencukupi (Tersedia: {$item->available_stock}).");
        }

        // Generate kode peminjaman unik
        $loanCode = 'PJ-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        Loan::create([
            'loan_code' => $loanCode,
            'user_id' => auth()->id(),
            'item_id' => $item->id,
            'quantity' => $validated['quantity'],
            'purpose' => $validated['purpose'],
            'borrow_date' => $validated['borrow_date'],
            'expected_return_date' => $validated['expected_return_date'],
            'status' => 'pending',
            'initial_condition' => 'baik',
        ]);

        return redirect()->route('loans.my')
            ->with('success', "Permohonan peminjaman berhasil diajukan dengan kode: {$loanCode}. Menunggu approval dari guru/pengelola studio.");
    }

    /**
     * Daftar peminjaman saya (siswa) (US #8 & Sprint 1 dasar)
     */
    public function myLoans(Request $request)
    {
        $query = Loan::with('item')
            ->where('user_id', auth()->id())
            ->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $loans = $query->paginate(10)->withQueryString();

        // Check overdue for active loans
        foreach ($loans as $loan) {
            $loan->checkOverdue();
        }

        return view('loans.my', compact('loans'));
    }
}
