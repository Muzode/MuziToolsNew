<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    protected $guarded = [];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
    // Kurangi stok berdasarkan quantity
    public function reduceStock($quantity)
    {
        $this->decrement('stok', $quantity);
    }

    // Cek kecukupan stok
    public function hasEnoughStock($quantity)
    {
        return $this->stok >= $quantity;
    }
}
