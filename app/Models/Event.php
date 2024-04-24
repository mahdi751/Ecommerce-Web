<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'slug', 'description', 'photo', 'store_id','start_time','end_time','status'];
    
    public function endEvent() {
        // Mark the event as ended
        $this->status = 'inactive';
        $this->save();
    
        // Retrieve all products associated with this event
        $eventProducts = $this->GetProducts()->get();
    
        // Loop through each product and update its bidding status
        foreach ($eventProducts as $product) {
            // Close bidding for the product
            $product->bid_status = 'closed';
    
            // Set the product's price to the current highest bid
          if( $product->highestBid){
                $product->price = $product->highestBid->bid;
          }
        
    
            // Save the changes
            $product->save();
            $this->addProductToCart($product);
        }
    }

    private function addProductToCart($product) {
        // Check if the product has a highest bid and if it's greater than 0
        if ($product->highestBid && $product->highestBid->bid > 0) {
            // Check if the user is authenticated
         
                
                $alreadyCart = Cart::where('user_id', $product->highestBid->user_id)
                                    ->where('product_id', $product->id)
                                    ->where('order_id', null)
                                    ->first();
                
                if (!$alreadyCart) {
                    $cart = new Cart;
                    $cart->user_id = $product->highestBid->user_id;
                    $cart->product_id = $product->id;
                    $cart->price = $product->price; // Set the price to the highest bid
                    $cart->quantity = 1; // Set the quantity to 1
                    $cart->amount = $product->price; // Set the amount to the highest bid
                    $cart->save();
                }
            
        }
    }
    
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
     public function GetProducts()
    {
        return $this->hasMany(Product::class);
     }
    public static function getAllEvents(){
        $storeId = session('current_store_id');
        return  Event::orderBy('id','DESC')->paginate(10)->where('store_id',$storeId);
    }
    public static function getAllEventsBuyer(){
        
        return  Event::orderBy('id','DESC')->paginate(10);
    }
    public static function countActiveEvents(){
        $data=Event::where('status','active')->count();
        if($data){
            return $data;
        }
        return 0;
    }
    public static function countStoreActiveEvents(){
        $storeId = session('current_store_id');
        $data=Event::where('status','active')->where('store_id', $storeId)->count();
        if($data){
            return $data;
        }
        return 0;
    }
   

}
