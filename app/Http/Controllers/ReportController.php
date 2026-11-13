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

    /**
     * Daftar rekap kedisiplinan peminjaman tiap siswa (US #12)
     */
    public function studentHistory(Request $request)
    {
        $query = User::where('role', 'siswa')
            ->withCount([
                'loans as total_loans',
                'loans as overdue_loans' => function ($q) {
                    $q->where('is_overdue', true);
                },
                'loans as damaged_loans' => function ($q) {
                    $q->where('damage_reported', true);
                },
                'loans as active_loans' => function ($q) {
                    $q->whereIn('status', ['approved', 'borrowed', 'overdue']);
                },
            ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nis_nip', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('reports.students', compact('students'));
    }

    /**
     * Detail riwayat peminjaman siswa untuk evaluasi kedisiplinan guru (US #12)
     */
    public function studentDetail(User $user)
    {
        if ($user->role !== 'siswa') {
            abort(404);
        }

        $loans = Loan::with('item')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        $totalLoans = Loan::where('user_id', $user->id)->count();
        $overdueCount = Loan::where('user_id', $user->id)->where('is_overdue', true)->count();
        $damageCount = Loan::where('user_id', $user->id)->where('damage_reported', true)->count();

        // Hitung persentase kedisiplinan (0 - 100%)
        $disciplineScore = 100;
        if ($totalLoans > 0) {
            $deduction = ($overdueCount * 15) + ($damageCount * 30);
            $disciplineScore = max(10, 100 - $deduction);
        }

        return view('reports.student_detail', compact(
            'user', 
            'loans', 
            'totalLoans', 
            'overdueCount', 
            'damageCount', 
            'disciplineScore'
        ));
    }
}
