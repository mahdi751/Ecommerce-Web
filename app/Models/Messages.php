<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'subject', 'email', 'photo', 'phone', 'message', 'read_at', 'store_id'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
