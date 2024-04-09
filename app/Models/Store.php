<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'email', 'phone_number', 'address','photo', 'owner_id', 'status'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function coupons()
    {
        return $this->hasMany(Coupons::class);
    }

    public function messages()
    {
        return $this->hasMany(Messages::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
