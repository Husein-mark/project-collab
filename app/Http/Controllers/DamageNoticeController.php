<?php

namespace App\Http\Controllers;

use App\Models\DamageNotice;
use Illuminate\Http\Request;

class DamageNoticeController extends Controller
{
    /**
     * Daftar notice kerusakan barang untuk guru (US #6)
     */
    public function index(Request $request)
    {
        $query = DamageNotice::with(['loan', 'item', 'user'])->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $notices = $query->paginate(15)->withQueryString();

        return view('notices.index', compact('notices'));
    }

    /**
     * Update status tindak lanjut notice kerusakan (US #6)
     */
    public function updateStatus(Request $request, DamageNotice $notice)
    {
        $request->validate([
            'status' => ['required', 'in:menunggu_tindakan,dalam_perbaikan,selesai'],
        ]);

        $notice->update(['status' => $request->status]);

        // Jika selesai diperbaiki, pulihkan kondisi barang ke 'baik'
        if ($request->status === 'selesai') {
            $item = $notice->item;
            if ($item->condition === 'rusak_berat') {
                $item->increment('available_stock', $notice->loan->quantity);
            }
            $item->update(['condition' => 'baik']);
        }

        return back()->with('success', "Status notice kerusakan untuk '{$notice->item->name}' berhasil diperbarui menjadi: " . ucfirst(str_replace('_', ' ', $request->status)));
    }
}
