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

       
        // Validate the request data
        $request->validate([
            'bid' => 'required|numeric|min:0',
            'product_id' => 'required|exists:products,id',
            'event_id' => 'required|exists:events,id',
        ]);

        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Get the authenticated user
        $user = Auth::user();

        // // Check if the user already placed a bid for the product
        // if ($user->bids()->where('product_id', $request->product_id)->exists()) {
        //     return response()->json(['error' => 'You have already placed a bid for this product'], 422);
        // }
        // Check if the user already placed a bid for the product
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
if( ($request->bid <= $minimumBidAmount) && ($request->bid < $product->closing_bid)) {
    return response()->json(['error' => 'Your bid must be higher than the current highest bid plus the minimum bid increment'], 422);
}

    // Check if the bid is higher than the starting bid price
    if ($request->bid < $product->starting_bid_price) {
        return response()->json(['error' => 'Your bid must be higher than the starting bid price'], 422);
    }
    $currentHighestBid = optional($product->highestBid)->bid ?? 0 ;

    // If there's a current highest bid, check if the bid is higher
    if ($currentHighestBid && $request->bid <= $currentHighestBid) {
        return response()->json(['error' => 'Your bid must be higher than the current highest bid'], 422);
    }
    
        // Create a new bid
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
        // Update the current highest bid for the associated product
       

        return response()->json(['message' => 'Bid placed successfully'], 201);
    }

    // Update the specified bid in storage
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

        // Update the current highest bid for the associated product
        $this->updateCurrentHighestBid($request->product_id);

        return response()->json(['message' => 'Bid updated successfully'], 200);
    }

    // Remove the specified bid from storage
    public function destroy($id)
    {
        $bid = Bid::findOrFail($id);
        $product_id = $bid->product_id;
        $bid->delete();

        // Update the current highest bid for the associated product
        $this->updateCurrentHighestBid($product_id);

        return response()->json(['message' => 'Bid deleted successfully'], 200);
    }

    // Update the current highest bid for the associated product
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
