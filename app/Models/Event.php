<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'slug', 'description', 'photo', 'store_id','start_time','end_time'];
    public function endEvent() {
        // Mark the event as ended
        $this->status = 'inactive';
        $this->save();
    
        // Retrieve all products associated with this event
        $eventProducts = $this->GetProducts()->where('bid_status', 'active')->get();
    
        // Loop through each product and update its bidding status
        foreach ($eventProducts as $product) {
            // Close bidding for the product
            $product->bid_status = 'closed';
    
            // Set the product's price to the current highest bid
            $product->price = $product->current_highest_bid;
    
            // Save the changes
            $product->save();
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
    

}
