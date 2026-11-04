<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
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
}
