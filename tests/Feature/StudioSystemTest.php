<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudioSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_siswa_dan_guru_bisa_login()
    {
        $response = $this->post(route('login.post'), [
            'email' => 'alwi@siswa.sch.id',
            'password' => 'password',
        ]);
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_siswa_dapat_melihat_katalog_barang()
    {
        $siswa = User::where('email', 'alwi@siswa.sch.id')->first();
        $response = $this->actingAs($siswa)->get(route('items.index'));
        $response->assertStatus(200);
        $response->assertSee('Katalog');
    }

    public function test_siswa_dapat_mengajukan_peminjaman_barang()
    {
        $siswa = User::where('email', 'anugrah@siswa.sch.id')->first();
        $item = Item::first();

        $response = $this->actingAs($siswa)->post(route('loans.store'), [
            'item_id' => $item->id,
            'quantity' => 1,
            'borrow_date' => date('Y-m-d'),
            'expected_return_date' => date('Y-m-d', strtotime('+2 days')),
            'purpose' => 'Praktikum Uji Kompetensi Kejuruan',
        ]);

        $response->assertRedirect(route('loans.my'));
        $this->assertDatabaseHas('loans', [
            'user_id' => $siswa->id,
            'item_id' => $item->id,
            'status' => 'pending',
        ]);
    }

    public function test_guru_dapat_menyetujui_permohonan_peminjaman()
    {
        $guru = User::where('role', 'guru')->first();
        $pendingLoan = Loan::where('status', 'pending')->first();

        $stockBefore = $pendingLoan->item->available_stock;

        $response = $this->actingAs($guru)->post(route('loans.approve', $pendingLoan));
        $response->assertSessionHas('success');

        $pendingLoan->refresh();
        $this->assertEquals('approved', $pendingLoan->status);
        $this->assertEquals($stockBefore - $pendingLoan->quantity, $pendingLoan->item->available_stock);
    }

    public function test_guru_dapat_mengunduh_ekspor_laporan_csv()
    {
        $guru = User::where('role', 'guru')->first();
        $response = $this->actingAs($guru)->get(route('reports.export'));

        $response->assertStatus(200);
        $this->assertEquals('text/csv; charset=UTF-8', $response->headers->get('Content-Type'));
    }
}
