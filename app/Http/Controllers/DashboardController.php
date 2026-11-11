<?php

namespace App\Http\Controllers;

use App\Models\DamageNotice;
use App\Models\Item;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isGuru()) {
            return $this->guruDashboard();
        }

        return $this->siswaDashboard($user);
    }

    private function siswaDashboard($user)
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        // 1. Peminjaman yang sedang dipinjam atau disetujui
        $activeLoans = Loan::with('item')
            ->where('user_id', $user->id)
            ->whereIn('status', ['approved', 'borrowed', 'overdue'])
            ->get();

        foreach ($activeLoans as $loan) {
            $loan->checkOverdue();
        }

        // 2. Pengingat Jatuh Tempo (H-1 & Hari Ini) (US #9)
        $dueSoonLoans = $activeLoans->filter(function ($loan) use ($today, $tomorrow) {
            return $loan->expected_return_date->lte($tomorrow) && $loan->status !== 'returned';
        });

        // 3. Status Pengajuan Terkini (US #8)
        $recentLoans = Loan::with('item')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // 4. Quick Counters
        $pendingCount = Loan::where('user_id', $user->id)->where('status', 'pending')->count();
        $approvedCount = Loan::where('user_id', $user->id)->where('status', 'approved')->count();
        $borrowedCount = Loan::where('user_id', $user->id)->where('status', 'borrowed')->count();
        $overdueCount = Loan::where('user_id', $user->id)->where(function ($q) {
            $q->where('status', 'overdue')->orWhere('is_overdue', true);
        })->whereNull('actual_return_date')->count();

        return view('dashboard.siswa', compact(
            'activeLoans',
            'dueSoonLoans',
            'recentLoans',
            'pendingCount',
            'approvedCount',
            'borrowedCount',
            'overdueCount'
        ));
    }

    private function guruDashboard()
    {
        // Guru dashboard will be handled by Jahan's step, or provide base here
        $totalItems = Item::count();
        $availableItems = Item::where('available_stock', '>', 0)->count();
        $pendingLoans = Loan::with(['item', 'user'])->where('status', 'pending')->latest()->take(5)->get();
        $activeLoansCount = Loan::whereIn('status', ['approved', 'borrowed'])->count();
        $overdueLoansCount = Loan::where('status', 'overdue')->orWhere('is_overdue', true)->whereNull('actual_return_date')->count();
        $unhandledDamageCount = DamageNotice::where('status', 'menunggu_tindakan')->count();

        return view('dashboard.guru', compact(
            'totalItems',
            'availableItems',
            'pendingLoans',
            'activeLoansCount',
            'overdueLoansCount',
            'unhandledDamageCount'
        ));
    }
}
