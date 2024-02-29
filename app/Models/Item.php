<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    // colonne del db che posso essere riempite massivamente
    protected $fillable =[
        "name",
        "sku",
        "price",
    ];

}
