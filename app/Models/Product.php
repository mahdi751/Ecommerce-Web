<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'slug', 'summary', 'description', 'photo', 'stock', 'size', 'condition', 'status', 'price', 'discount', 'is_featured', 'cat_id', 'child_cat_id'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'cat_id');
    }

    public function childCategory()
    {
        return $this->belongsTo(Category::class, 'child_cat_id');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    public static function getAllProduct(){
        return Product::with(['cat_info','sub_cat_info'])->orderBy('id','desc')->paginate(10);
    }
}
