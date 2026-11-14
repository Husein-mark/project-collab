<?php

namespace App\Http\Controllers;

use App\Models\DamageNotice;
use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
     * Ekspor Laporan Peminjaman ke format CSV / Excel (US #14)
     */
    public function export(Request $request): StreamedResponse
    {
        $query = Loan::with(['item', 'user', 'approver'])->latest();

        if ($request->filled('type')) {
            if ($request->type === 'overdue') {
                $query->where(function ($q) {
                    $q->where('status', 'overdue')->orWhere('is_overdue', true);
                });
            } elseif ($request->type === 'damaged') {
                $query->where('damage_reported', true);
            } elseif ($request->type === 'returned') {
                $query->where('status', 'returned');
            }
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('borrow_date', [$request->start_date, $request->end_date]);
        }

        $loans = $query->get();
        $fileName = 'rekap_peminjaman_studio_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($loans) {
            $handle = fopen('php://output', 'w');
            
            // BOM UTF-8 for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header kolom
            fputcsv($handle, [
                'No',
                'Kode Transaksi',
                'Nama Siswa',
                'NIS',
                'Email Siswa',
                'Kode Barang',
                'Nama Barang',
                'Kategori',
                'Jumlah',
                'Keperluan',
                'Tanggal Pinjam',
                'Batas Rencana Kembali',
                'Tanggal Aktual Kembali',
                'Status Transaksi',
                'Terlambat (Hari)',
                'Kondisi Kembali',
                'Catatan Kerusakan',
            ]);

            $i = 1;
            foreach ($loans as $loan) {
                fputcsv($handle, [
                    $i++,
                    $loan->loan_code,
                    $loan->user->name ?? '-',
                    $loan->user->nis_nip ?? '-',
                    $loan->user->email ?? '-',
                    $loan->item->code ?? '-',
                    $loan->item->name ?? '-',
                    $loan->item->category ?? '-',
                    $loan->quantity,
                    $loan->purpose,
                    $loan->borrow_date->format('Y-m-d'),
                    $loan->expected_return_date->format('Y-m-d'),
                    $loan->actual_return_date ? $loan->actual_return_date->format('Y-m-d') : 'Belum Kembali',
                    $loan->status,
                    $loan->is_overdue ? $loan->overdue_days : 0,
                    $loan->return_condition ?? 'Normal',
                    $loan->damage_notes ?? '-',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Rekap statistik barang yang paling sering rusak/hilang (US #15)
     */
    public function damagedStats()
    {
        // Barang dengan frekuensi notice terbanyak
        $itemsWithDamage = Item::withCount([
            'loans as total_damage_count' => function ($q) {
                $q->where('damage_reported', true);
            },
            'loans as heavy_damage_count' => function ($q) {
                $q->where('return_condition', 'rusak_berat');
            },
            'loans as lost_count' => function ($q) {
                $q->where('return_condition', 'hilang');
            },
        ])
        ->having('total_damage_count', '>', 0)
        ->orderByDesc('total_damage_count')
        ->get();

        // Total seluruh kasus kerusakan
        $totalDamageCases = DamageNotice::count();
        $inRepairCount = DamageNotice::where('status', 'dalam_perbaikan')->count();
        $completedRepairCount = DamageNotice::where('status', 'selesai')->count();
        $pendingActionCount = DamageNotice::where('status', 'menunggu_tindakan')->count();

        return view('reports.damaged_stats', compact(
            'itemsWithDamage',
            'totalDamageCases',
            'inRepairCount',
            'completedRepairCount',
            'pendingActionCount'
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
