<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category',
        'description',
        'total_stock',
        'available_stock',
        'condition',
        'location',
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function isAvailable(): bool
    {
        return $this->available_stock > 0 && $this->condition !== 'rusak_berat';
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->condition === 'rusak_berat') {
            return 'Rusak (Tidak Tersedia)';
        }
        if ($this->available_stock <= 0) {
            return 'Habis (Sedang Dipinjam)';
        }
        return 'Tersedia (' . $this->available_stock . ')';
    }
}
