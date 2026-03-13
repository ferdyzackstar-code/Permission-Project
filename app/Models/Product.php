<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Tambahkan relasi ini juga biar bisa panggil $product->category->name
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function transaction_details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
