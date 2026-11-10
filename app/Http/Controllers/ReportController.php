<?php

namespace App\Http\Controllers;

use App\Models\DamageNotice;
use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Halaman laporan peminjaman dan rekap otomatis (US #10 & Monitoring)
     */
    public function index(Request $request)
    {
        $query = Loan::with(['item', 'user', 'approver'])->latest();

        // Filter status khusus laporan
        if ($request->filled('type')) {
            if ($request->type === 'overdue') {
                $query->where(function ($q) {
                    $q->where('status', 'overdue')->orWhere('is_overdue', true);
                });
            } elseif ($request->type === 'damaged') {
                $query->where('damage_reported', true);
            } elseif ($request->type === 'returned') {
                $query->where('status', 'returned');
            } elseif ($request->type === 'active') {
                $query->whereIn('status', ['approved', 'borrowed']);
            }
        }

        // Filter rentang tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('borrow_date', [$request->start_date, $request->end_date]);
        }

        $loans = $query->paginate(20)->withQueryString();

        // Statistik Ringkas Laporan
        $totalLoans = Loan::count();
        $totalOverdue = Loan::where('is_overdue', true)->count();
        $totalDamaged = Loan::where('damage_reported', true)->count();
        $totalReturned = Loan::where('status', 'returned')->count();

        return view('reports.index', compact(
            'loans', 
            'totalLoans', 
            'totalOverdue', 
            'totalDamaged', 
            'totalReturned'
        ));
    }
}
