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
     * Tampilan daftar seluruh peminjaman untuk guru (US #3 & Monitoring)
     */
    public function index(Request $request)
    {
        $query = Loan::with(['item', 'user', 'approver'])->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('loan_code', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('item', fn ($i) => $i->where('name', 'like', "%{$search}%"));
            });
        }

        $loans = $query->paginate(15)->withQueryString();

        // Otomatis cek keterlambatan
        foreach ($loans as $loan) {
            $loan->checkOverdue();
        }

        $pendingCount = Loan::where('status', 'pending')->count();
        $activeCount = Loan::whereIn('status', ['approved', 'borrowed'])->count();
        $overdueCount = Loan::where('status', 'overdue')->orWhere('is_overdue', true)->count();

        return view('loans.index', compact('loans', 'pendingCount', 'activeCount', 'overdueCount'));
    }

    /**
     * Approve permohonan peminjaman oleh guru (US #3)
     */
    public function approve(Request $request, Loan $loan)
    {
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Hanya permohonan dengan status pending yang dapat disetujui.');
        }

        $item = $loan->item;
        if ($item->available_stock < $loan->quantity) {
            return back()->with('error', "Gagal menyetujui: Stok barang '{$item->name}' tidak mencukupi saat ini.");
        }

        // Kurangi stok barang
        $item->decrement('available_stock', $loan->quantity);

        $loan->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', "Permohonan {$loan->loan_code} ({$loan->item->name}) berhasil disetujui! Stok barang telah diperbarui.");
    }

    /**
     * Reject permohonan peminjaman oleh guru (US #3)
     */
    public function reject(Request $request, Loan $loan)
    {
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Hanya permohonan dengan status pending yang dapat ditolak.');
        }

        $request->validate([
            'rejection_reason' => ['required', 'string', 'min:3', 'max:255'],
        ], [
            'rejection_reason.required' => 'Wajib menyertakan alasan penolakan permohonan.',
        ]);

        $loan->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', "Permohonan {$loan->loan_code} telah ditolak dengan alasan: {$request->rejection_reason}.");
    }

    /**
     * Halaman inspeksi serah terima & pengembalian kondisi barang (US #5)
     */
    public function showInspect(Loan $loan)
    {
        $loan->load(['user', 'item', 'approver']);
        $loan->checkOverdue();

        return view('loans.inspect', compact('loan'));
    }

    /**
     * Catat kondisi fisik saat barang diserahkan ke siswa (Handover) (US #5)
     */
    public function updateHandover(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'initial_condition' => ['required', 'string'],
            'initial_notes' => ['nullable', 'string', 'max:255'],
        ]);

        $loan->update([
            'status' => 'borrowed',
            'initial_condition' => $validated['initial_condition'],
            'initial_notes' => $validated['initial_notes'],
        ]);

        return back()->with('success', "Barang telah diserahkan kepada {$loan->user->name}. Status berubah menjadi 'Sedang Dipinjam'.");
    }

    /**
     * Proses pengembalian barang & pencatatan kondisi akhir (US #5, US #7)
     */
    public function processReturn(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'actual_return_date' => ['required', 'date'],
            'return_condition' => ['required', 'in:baik,rusak_ringan,rusak_berat,hilang'],
            'damage_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $item = $loan->item;
        $returnDate = Carbon::parse($validated['actual_return_date']);
        $expectedDate = $loan->expected_return_date;

        $isOverdue = false;
        $overdueDays = 0;

        if ($returnDate->gt($expectedDate)) {
            $isOverdue = true;
            $overdueDays = $returnDate->diffInDays($expectedDate);
        }

        $damageReported = in_array($validated['return_condition'], ['rusak_ringan', 'rusak_berat', 'hilang']);

        // Update stok barang
        if ($validated['return_condition'] === 'baik') {
            $item->increment('available_stock', $loan->quantity);
        } elseif ($validated['return_condition'] === 'rusak_ringan') {
            $item->increment('available_stock', $loan->quantity);
            $item->update(['condition' => 'rusak_ringan']);
        } elseif ($validated['return_condition'] === 'rusak_berat') {
            // Jangan kembalikan stok tersedia karena tidak bisa dipinjam
            $item->update(['condition' => 'rusak_berat']);
        } elseif ($validated['return_condition'] === 'hilang') {
            $item->decrement('total_stock', $loan->quantity);
        }

        $loan->update([
            'actual_return_date' => $validated['actual_return_date'],
            'return_condition' => $validated['return_condition'],
            'damage_notes' => $validated['damage_notes'],
            'damage_reported' => $damageReported,
            'is_overdue' => $isOverdue,
            'overdue_days' => $overdueDays,
            'status' => 'returned',
        ]);

        $message = "Pengembalian barang berhasil dicatat! Status peminjaman selesai.";
        if ($damageReported) {
            $message .= " Peringatan: Kerusakan barang telah dicatat dalam sistem dan notice dibuat!";
        }
        if ($isOverdue) {
            $message .= " Catatan: Pengembalian terlambat {$overdueDays} hari dan otomatis masuk laporan.";
        }

        return redirect()->route('loans.index')->with('success', $message);
    }

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
