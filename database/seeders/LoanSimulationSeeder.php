<?php

namespace Database\Seeders;

use App\Models\DamageNotice;
use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LoanSimulationSeeder extends Seeder
{
    public function run(): void
    {
        $guru = User::where('role', 'guru')->first();
        $alwi = User::where('email', 'alwi@siswa.sch.id')->first();
        $anugrah = User::where('email', 'anugrah@siswa.sch.id')->first();
        $jahan = User::where('email', 'jahan@siswa.sch.id')->first();
        $belva = User::where('email', 'belva@siswa.sch.id')->first();
        $chiko = User::where('email', 'chiko@siswa.sch.id')->first();

        $cam1 = Item::where('code', 'CAM-001')->first();
        $cam2 = Item::where('code', 'CAM-002')->first();
        $mic1 = Item::where('code', 'MIC-001')->first();
        $mic2 = Item::where('code', 'MIC-002')->first();
        $lgt1 = Item::where('code', 'LGT-001')->first();
        $trp1 = Item::where('code', 'TRP-001')->first();

        if (! $guru || ! $alwi || ! $cam1) {
            return;
        }

        // 1. Peminjaman selesai tepat waktu (Alwi)
        Loan::updateOrCreate(
            ['loan_code' => 'PJ-20261105-ALW1'],
            [
                'user_id' => $alwi->id,
                'item_id' => $cam1->id,
                'quantity' => 1,
                'purpose' => 'Liputan Wawancara Kepala Sekolah untuk Buletin Sekolah',
                'borrow_date' => Carbon::now()->subDays(10),
                'expected_return_date' => Carbon::now()->subDays(8),
                'actual_return_date' => Carbon::now()->subDays(8),
                'status' => 'returned',
                'approved_by' => $guru->id,
                'approved_at' => Carbon::now()->subDays(10),
                'initial_condition' => 'baik',
                'return_condition' => 'baik',
                'is_overdue' => false,
            ]
        );

        // 2. Peminjaman terlambat 3 hari dan selesai (Anugrah)
        Loan::updateOrCreate(
            ['loan_code' => 'PJ-20261107-ANG1'],
            [
                'user_id' => $anugrah->id,
                'item_id' => $trp1->id,
                'quantity' => 1,
                'purpose' => 'Shooting Video Musik Ekskul Paduan Suara',
                'borrow_date' => Carbon::now()->subDays(8),
                'expected_return_date' => Carbon::now()->subDays(6),
                'actual_return_date' => Carbon::now()->subDays(3),
                'status' => 'returned',
                'approved_by' => $guru->id,
                'approved_at' => Carbon::now()->subDays(8),
                'initial_condition' => 'baik',
                'return_condition' => 'baik',
                'is_overdue' => true,
                'overdue_days' => 3,
            ]
        );

        // 3. Peminjaman dengan kerusakan barang & Notice (Chiko)
        $loanDamaged = Loan::updateOrCreate(
            ['loan_code' => 'PJ-20261109-CHK1'],
            [
                'user_id' => $chiko->id,
                'item_id' => $mic2->id,
                'quantity' => 1,
                'purpose' => 'Rekaman Podcast OSIS Episode 4',
                'borrow_date' => Carbon::now()->subDays(5),
                'expected_return_date' => Carbon::now()->subDays(3),
                'actual_return_date' => Carbon::now()->subDays(3),
                'status' => 'returned',
                'approved_by' => $guru->id,
                'approved_at' => Carbon::now()->subDays(5),
                'initial_condition' => 'baik',
                'return_condition' => 'rusak_ringan',
                'damage_notes' => 'Jack audio 3.5mm bengkok karena tersenggol saat recording.',
                'damage_reported' => true,
                'is_overdue' => false,
            ]
        );

        DamageNotice::updateOrCreate(
            ['loan_id' => $loanDamaged->id],
            [
                'item_id' => $mic2->id,
                'user_id' => $chiko->id,
                'damage_level' => 'rusak_ringan',
                'notes' => 'Jack audio 3.5mm bengkok karena tersenggol saat recording.',
                'status' => 'menunggu_tindakan',
            ]
        );

        // 4. Peminjaman aktif sedang berjalan (Belva)
        Loan::updateOrCreate(
            ['loan_code' => 'PJ-20261114-BLV1'],
            [
                'user_id' => $belva->id,
                'item_id' => $lgt1->id,
                'quantity' => 1,
                'purpose' => 'Pemotretan Buku Tahunan Sekolah Kelas XII',
                'borrow_date' => Carbon::now()->subDays(2),
                'expected_return_date' => Carbon::tomorrow(), // H-1 reminder trigger!
                'status' => 'borrowed',
                'approved_by' => $guru->id,
                'approved_at' => Carbon::now()->subDays(2),
                'initial_condition' => 'baik',
            ]
        );

        // 5. Peminjaman sedang OVERDUE / Keterlambatan aktif (Jahan)
        $overdueLoan = Loan::updateOrCreate(
            ['loan_code' => 'PJ-20261111-JHN1'],
            [
                'user_id' => $jahan->id,
                'item_id' => $cam2->id,
                'quantity' => 1,
                'purpose' => 'Praktikum Dokumentasi Lapangan Mapel DKV',
                'borrow_date' => Carbon::now()->subDays(5),
                'expected_return_date' => Carbon::now()->subDays(2),
                'status' => 'overdue',
                'approved_by' => $guru->id,
                'approved_at' => Carbon::now()->subDays(5),
                'initial_condition' => 'baik',
                'is_overdue' => true,
                'overdue_days' => 2,
            ]
        );

        // 6. Peminjaman baru berstatus PENDING (Alwi)
        Loan::updateOrCreate(
            ['loan_code' => 'PJ-20261116-ALW2'],
            [
                'user_id' => $alwi->id,
                'item_id' => $mic1->id,
                'quantity' => 1,
                'purpose' => 'Ujian Praktik Kejuruan Siaran Berita TV',
                'borrow_date' => Carbon::today(),
                'expected_return_date' => Carbon::now()->addDays(2),
                'status' => 'pending',
                'initial_condition' => 'baik',
            ]
        );
    }
}
