<?php

namespace App\Console\Commands;

use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckOverdueLoans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loans:check-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pemeriksaan otomatis keterlambatan pengembalian barang studio sekolah';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        
        $activeLoans = Loan::whereIn('status', ['approved', 'borrowed', 'overdue'])
            ->whereNull('actual_return_date')
            ->where('expected_return_date', '<', $today)
            ->get();

        $count = 0;
        foreach ($activeLoans as $loan) {
            $days = $today->diffInDays($loan->expected_return_date);
            $loan->update([
                'status' => 'overdue',
                'is_overdue' => true,
                'overdue_days' => $days,
            ]);
            $count++;
        }

        $this->info("Pemeriksaan selesai: {$count} peminjaman terdeteksi dan diperbarui status keterlambatannya.");
    }
}
