<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['order_number', 'user_id', 'sub_total', 'shipping_id', 'coupon', 'total_amount', 'payment_method', 'payment_status', 'status', 'first_name', 'last_name', 'email', 'phone', 'country', 'currency', 'post_code', 'address1', 'address2'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shipping()
    {
        return $this->belongsTo(Shipping::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
}
