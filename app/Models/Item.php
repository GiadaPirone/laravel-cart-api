<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable =[
        "name",
        "sku",
        "price",
    ];

    public function Carts(){
        return $this->belongsToMany(Cart::class);
    }
}
