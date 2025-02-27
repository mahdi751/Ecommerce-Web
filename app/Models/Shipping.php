<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;
    protected $fillable = ['type', 'price', 'status','store_id'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
