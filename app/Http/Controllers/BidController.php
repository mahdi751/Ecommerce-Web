<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bid;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Events\NewBid;

class BidController extends Controller
{
    // Store a newly created bid in storage
    public function store(Request $request)
    {

        $request->validate([
            'bid' => 'required|numeric|min:0',
            'product_id' => 'required|exists:products,id',
            'event_id' => 'required|exists:events,id',
        ]);

        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $user = Auth::user();


    $previousBid = Bid::where('product_id', $request->product_id)
    ->where('user_id', $user->id)
    ->orderBy('bid', 'desc')
    ->first();

if ($previousBid && $request->bid <= $previousBid->bid) {
return response()->json(['error' => 'Your bid must be higher than your previous bid'], 422);
}
$product = Product::findOrFail($request->product_id);
if( ( $product->bid_status == 'closed')) {
    return response()->json(['error' => 'this item was sold'], 422);
}
$minimumBidAmount = ($product->highestBid->bid ?? $product->starting_bid_price) + $product->minimum_bid_increment;

// Ensure the bid amount is higher than the minimum bid amount
if( ($request->bid < $minimumBidAmount) && ($request->bid < $product->closing_bid)) {
    return response()->json(['error' => 'Your bid must be higher than the current highest bid plus the minimum bid increment'], 422);
}

    if ($request->bid < $product->starting_bid_price) {
        return response()->json(['error' => 'Your bid must be higher than the starting bid price'], 422);
    }
    $currentHighestBid = optional($product->highestBid)->bid ?? 0 ;

    if ($currentHighestBid && $request->bid <= $currentHighestBid) {
        return response()->json(['error' => 'Your bid must be higher than the current highest bid'], 422);
    }
    
        $bid = new Bid();
        if ($request->bid >= $product->closing_bid) {
            $bid->bid = $product->closing_bid;
            
        }else{

            $bid->bid = $request->bid;
        }
       
        $bid->product_id = $request->product_id;
        $bid->event_id = $request->event_id ;
        $bid->user_id = $user->id ;
        $bid->save();
        


        $this->updateCurrentHighestBid($request->product_id);


        
        broadcast(new NewBid($bid))->toOthers();
       

        return response()->json(['message' => 'Bid placed successfully'], 201);
    }

    public function update(Request $request, $id)
    {
        $bid = Bid::findOrFail($id);
        $this->validate($request, [
            'bid' => 'required|numeric|min:0',
            'product_id' => 'required|exists:products,id',
            'event_id' => 'required|exists:events,id',
        ]);

        $data = $request->all();
        $status = $bid->fill($data)->save();

        if ($status) {
            request()->session()->flash('success', 'Bid Successfully updated');
        } else {
            request()->session()->flash('error', 'Please try again!!');
        }

        $this->updateCurrentHighestBid($request->product_id);

        return response()->json(['message' => 'Bid updated successfully'], 200);
    }

    public function destroy($id)
    {
        $bid = Bid::findOrFail($id);
        $product_id = $bid->product_id;
        $bid->delete();

        $this->updateCurrentHighestBid($product_id);

        return response()->json(['message' => 'Bid deleted successfully'], 200);
    }

    private function updateCurrentHighestBid($product_id)
    {
        $product = Product::findOrFail($product_id);
        $highestBid = $product->bids()->orderBy('bid', 'desc')->first();

        if ($highestBid) {
            $product->highest_bid_id = $highestBid->id;
            if ($highestBid->bid == $product->closing_bid) {
                $product->bid_status = 'closed';
            }
            $product->save();
        } else {
          
            $product->highest_bid_id = null; 
            $product->save();
        }
    }
}
