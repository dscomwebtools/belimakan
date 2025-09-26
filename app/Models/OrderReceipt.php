<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OrderReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_number',
        'receipt_img',
    ];

    /**
     * One-to-one relationship with Order
     */

    protected $appends = ['receipt_img_url'];

    public function getReceiptImgUrlAttribute()
    {
        return $this->receipt_img
            ? Storage::url($this->receipt_img)
            : null;
    }

    public function order()
    {
        return $this->hasOne(Order::class, 'order_receipt_id');
    }
}
