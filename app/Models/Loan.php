<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_code',
        'user_id',
        'item_id',
        'quantity',
        'purpose',
        'borrow_date',
        'expected_return_date',
        'actual_return_date',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'initial_condition',
        'initial_notes',
        'return_condition',
        'damage_notes',
        'damage_reported',
        'is_overdue',
        'overdue_days',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
        'approved_at' => 'datetime',
        'damage_reported' => 'boolean',
        'is_overdue' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function damageNotice()
    {
        return $this->hasOne(DamageNotice::class);
    }

    /**
     * Check and calculate overdue dynamically
     */
    public function checkOverdue(): bool
    {
        if (in_array($this->status, ['approved', 'borrowed', 'overdue']) && ! $this->actual_return_date) {
            $today = Carbon::today();
            if ($today->gt($this->expected_return_date)) {
                $days = $today->diffInDays($this->expected_return_date);
                $this->update([
                    'status' => 'overdue',
                    'is_overdue' => true,
                    'overdue_days' => $days,
                ]);
                return true;
            }
        }
        return $this->is_overdue;
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'label' => 'Menunggu Approval'],
            'approved' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Disetujui (Siap Diambil)'],
            'borrowed' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-800', 'label' => 'Sedang Dipinjam'],
            'returned' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'label' => 'Sudah Dikembalikan'],
            'rejected' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-800', 'label' => 'Ditolak'],
            'overdue' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Terlambat (' . $this->overdue_days . ' Hari)'],
            default => ['bg' => 'bg-slate-100', 'text' => 'text-slate-800', 'label' => $this->status],
        };
    }
}
