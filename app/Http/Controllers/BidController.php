<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bid;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class BidController extends Controller
{
    // Store a newly created bid in storage
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'bid' => 'required|numeric|min:0',
            'product_id' => 'required|exists:products,id',
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

        // Create a new bid
        $bid = new Bid();
        $bid->bid = $request->bid;
        $bid->product_id = $request->product_id;
        $bid->user_id = $user->id;
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
            $product->current_highest_bid = $highestBid->bid;
            $product->save();
        } else {
            // If there are no bids, reset the current highest bid to null or 0
            $product->current_highest_bid = null; // or 0, depending on your business logic
            $product->save();
        }
    }
}
