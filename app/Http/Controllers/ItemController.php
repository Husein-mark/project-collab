<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Loan;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Katalog & daftar ketersediaan barang studio (US #4)
     */
    public function index(Request $request)
    {
        $query = Item::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            if ($request->status === 'available') {
                $query->where('available_stock', '>', 0)->where('condition', '!=', 'rusak_berat');
            } elseif ($request->status === 'borrowed') {
                $query->where('available_stock', '<=', 0);
            } elseif ($request->status === 'damaged') {
                $query->whereIn('condition', ['rusak_ringan', 'rusak_berat']);
            }
        }

        $items = $query->orderBy('name')->paginate(12)->withQueryString();
        $categories = Item::distinct()->pluck('category')->filter()->values();

        return view('items.index', compact('items', 'categories'));
    }

    /**
     * Form tambah barang baru (US #13)
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * Simpan data barang baru (US #13)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'uppercase', 'unique:items,code'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string'],
            'total_stock' => ['required', 'integer', 'min:1'],
            'condition' => ['required', 'in:baik,rusak_ringan,rusak_berat'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['available_stock'] = $validated['total_stock'];

        Item::create($validated);

        return redirect()->route('items.index')
            ->with('success', "Barang '{$validated['name']}' ({$validated['code']}) berhasil ditambahkan ke inventaris studio.");
    }

    /**
     * Form edit data barang (US #13)
     */
    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    /**
     * Perbarui data barang (US #13)
     */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'uppercase', 'unique:items,code,' . $item->id],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string'],
            'total_stock' => ['required', 'integer', 'min:1'],
            'condition' => ['required', 'in:baik,rusak_ringan,rusak_berat'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        // Hitung selisih stok jika total_stock diubah
        $difference = $validated['total_stock'] - $item->total_stock;
        $newAvailable = max(0, $item->available_stock + $difference);
        $validated['available_stock'] = $newAvailable;

        $item->update($validated);

        return redirect()->route('items.index')
            ->with('success', "Data peralatan '{$item->name}' berhasil diperbarui.");
    }

    /**
     * Hapus barang studio (US #13)
     */
    public function destroy(Item $item)
    {
        // Cek apakah ada peminjaman aktif
        $activeLoans = Loan::where('item_id', $item->id)
            ->whereIn('status', ['approved', 'borrowed', 'overdue'])
            ->count();

        if ($activeLoans > 0) {
            return back()->with('error', "Gagal menghapus: Barang '{$item->name}' masih memiliki {$activeLoans} peminjaman aktif!");
        }

        $itemName = $item->name;
        $item->delete();

        return redirect()->route('items.index')
            ->with('success', "Barang '{$itemName}' berhasil dihapus dari sistem.");
    }
}
