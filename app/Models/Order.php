<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // Helper untuk auto-generate Invoice Number
    public static function generateInvoiceNumber()
    {
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', now()->toDateString())->count();
        return 'INV-' . $date . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }
}
