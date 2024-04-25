<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
class Product extends Model
{
    use HasFactory;
    protected $apiKey;
    protected $baseUrl;

    public function __construct(){
        $this->apiKey = config('currency_freaks.api_key');
        $this->baseUrl = 'https://api.currencyfreaks.com/v2.0/';
    }
    protected $fillable = ['title', 'slug', 'summary', 'description', 'photo', 'stock', 'size', 'condition', 'status', 'price', 'discount', 'is_featured', 'cat_id', 'child_cat_id','starting_bid_price','minimum_bid_increment','highest_bid_id','closing_bid','bid_status','event_id','is_event_item'];
    

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
    
    public function event_info(){
        return $this->hasOne('App\Models\Event','id','event_id');
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
    public static function countStoreActiveProduct(){
        $storeId = session('current_store_id');

        $data=Product::whereHas('cat_info', function($query) use ($storeId) {
            $query->where('store_id', $storeId);
        })
        ->orWhereHas('sub_cat_info', function($query) use ($storeId) {
            $query->where('store_id', $storeId);
        })->where('status','active')->count();

        
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

    public function bids()
{
    return $this->hasMany(Bid::class);
}

public function highestBid()
{
    return $this->belongsTo(Bid::class, 'highest_bid_id');
}

public function highestBidder()
{
    return $this->belongsTo(Bid::class, 'highest_bid_id')->with('user');
}
public function getBidByUser()
{

    if (!Auth::check()) {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    $user = Auth::user();
    return $this->hasMany(Bid::class)->where('user_id', $user->id)->orderByDesc('bid');
}


public function getAmountConverted($cur, $amount) {
    if (Cache::has('currency_conversion_rates')) {
        $rates = Cache::get('currency_conversion_rates');
    } else {
        $response = Http::get($this->baseUrl . 'rates/latest', [
            'apikey' => $this->apiKey,
        ]);

        if ($response->successful() && isset($response->json()["rates"])) {
            $rates = $response->json()["rates"];

            Cache::put('currency_conversion_rates', $rates, now()->addHours(6)); 
        } else {
            return "Failed to fetch conversion rates";
        }
    }

    if (isset($rates[$cur])) {
        return $rates[$cur] * $amount;
    } else {
        return "Currency not supported";
    }
}


  
}
