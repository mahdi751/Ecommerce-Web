<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'slug', 'summary', 'description', 'photo', 'stock', 'size', 'condition', 'status', 'price', 'discount', 'is_featured', 'cat_id', 'child_cat_id','starting_bid_price','minimum_bid_increment','current_highest_bid','closing_bid','bid_status','event_id','is_event_item'];
    

    // public function category()
    // {
    //     return $this->belongsTo(Category::class, 'cat_id');
    // }

    // public function childCategory()
    // {
    //     return $this->belongsTo(Category::class, 'child_cat_id');
    // }

    // public function carts()
    // {
    //     return $this->hasMany(Cart::class);
    // }

    

    public function cat_info(){
        return $this->hasOne('App\Models\Category','id','cat_id');
    }
    public function sub_cat_info(){
        return $this->hasOne('App\Models\Category','id','child_cat_id');
    }
    public static function getAllProduct(){
        return Product::with(['cat_info','sub_cat_info'])->orderBy('id','desc')->paginate(10);
    }
    public static function getProductsByStore(){
        $storeId = session('current_store_id');
        return Product::whereHas('cat_info', function($query) use ($storeId) {
                    $query->where('store_id', $storeId);
                })
                ->orWhereHas('sub_cat_info', function($query) use ($storeId) {
                    $query->where('store_id', $storeId);
                })
                ->orderBy('id', 'desc')
                ->paginate(10);
    }
    public function rel_prods(){
        return $this->hasMany('App\Models\Product','cat_id','cat_id')->where('status','active')->orderBy('id','DESC')->limit(8);
    }
    public function getReview(){
        return $this->hasMany('App\Models\ProductReview','product_id','id')->with('user_info')->where('status','active')->orderBy('id','DESC');
    }
    public static function getProductBySlug($slug){
        return Product::with(['cat_info','rel_prods','getReview'])->where('slug',$slug)->first();
    }
    public static function countActiveProduct(){
        $data=Product::where('status','active')->count();
        if($data){
            return $data;
        }
        return 0;
    }

    public function carts(){
        return $this->hasMany(Cart::class)->whereNotNull('order_id');
    }

    public function wishlists(){
        return $this->hasMany(Wishlist::class)->whereNotNull('cart_id');
    }

  
}
