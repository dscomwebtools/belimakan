<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'min_amount',
        'discount_amount',
        'discount_percent',
        'valid_from',
        'valid_until',
        'max_uses',
        'max_uses_per_user',
        'active',
    ];

    protected $casts = [
        'min_amount' => 'integer',
        'discount_amount' => 'integer',
        'discount_percent' => 'integer',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'active' => 'boolean',
    ];

    /**
     * Cek apakah kupon masih valid secara tanggal & status
     */
    public function isValid(): bool
    {
        $today = Carbon::today();

        if (!$this->active) {
            return false;
        }

        if ($this->valid_from && $today->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_until && $today->gt($this->valid_until)) {
            return false;
        }

        return true;
    }

    /**
     * Hitung nominal diskon berdasarkan subtotal
     */
    public function calculateDiscount(float $subtotal): float
    {
        if (!$this->isValid() || $subtotal < $this->min_amount) {
            return 0;
        }

        // default pakai diskon tetap
        if ($this->discount_amount > 0) {
            return (float) $this->discount_amount;
        }

        // fallback ke diskon persen
        if ($this->discount_percent > 0) {
            return (float) ($subtotal * ($this->discount_percent / 100));
        }

        return 0;
    }
}
