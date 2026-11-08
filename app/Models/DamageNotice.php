<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DamageNotice extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'item_id',
        'user_id',
        'damage_level',
        'notes',
        'status',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getLevelBadgeAttribute(): array
    {
        return match ($this->damage_level) {
            'rusak_ringan' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'label' => 'Rusak Ringan'],
            'rusak_berat' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-800', 'label' => 'Rusak Berat'],
            'hilang' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Barang Hilang'],
            default => ['bg' => 'bg-slate-100', 'text' => 'text-slate-800', 'label' => $this->damage_level],
        };
    }
}
