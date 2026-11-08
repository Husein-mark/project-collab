<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('damage_notices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Siswa peminjam saat rusak
            $table->enum('damage_level', ['rusak_ringan', 'rusak_berat', 'hilang']);
            $table->text('notes')->nullable();
            $table->enum('status', ['menunggu_tindakan', 'dalam_perbaikan', 'selesai'])->default('menunggu_tindakan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('damage_notices');
    }
};
