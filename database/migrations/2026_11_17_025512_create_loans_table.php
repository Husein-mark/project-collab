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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_code')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->text('purpose');
            $table->date('borrow_date');
            $table->date('expected_return_date');
            $table->date('actual_return_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'borrowed', 'returned', 'overdue'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('initial_condition')->default('baik'); // baik, lecet_pemakaian
            $table->text('initial_notes')->nullable();
            $table->string('return_condition')->nullable(); // baik, rusak_ringan, rusak_berat, hilang
            $table->text('damage_notes')->nullable();
            $table->boolean('damage_reported')->default(false);
            $table->boolean('is_overdue')->default(false);
            $table->integer('overdue_days')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
