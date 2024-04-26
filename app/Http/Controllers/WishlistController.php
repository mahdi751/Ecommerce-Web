<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Cache;
class WishlistController extends Controller
{
    protected $product=null;
    public function __construct(Product $product){
        $this->product=$product;
    }

    public function index(){
        $selectedCurrency = Cache::get('selected_currency_' . auth()->id());
        $selectedCurrencySign = "";
        switch ($selectedCurrency) {
            case 'LBP':
                $selectedCurrencySign = 'L.L '; // Assign the currency sign for LBP
                break;
            case 'USD':
                $selectedCurrencySign = '$ '; // Assign the currency sign for USD
                break;
            case 'EUR':
                $selectedCurrencySign = '€ '; // Assign the currency sign for EUR
                break;
            case 'KWD':
                $selectedCurrencySign = 'KWD '; // Assign the currency sign for KWD
                break;
            // Add more cases for other currencies if needed
            default:
                $selectedCurrencySign = ''; // Default value if no currency is selected
        }
        if($selectedCurrency == null){
            $selectedCurrency = "USD";
        }

        return view("Buyers.pages.wishlist")->with("selectedCurrency",$selectedCurrency)->with("selectedCurrencySign",$selectedCurrencySign);
    }

    public function wishlist(Request $request){
        // dd($request->all());
        if (empty($request->slug)) {
            request()->session()->flash('error','Invalid Products');
            return back();
        }        
        $product = Product::where('slug', $request->slug)->first();
        // return $product;
        if (empty($product)) {
            request()->session()->flash('error','Invalid Products');
            return back();
        }

        $already_wishlist = Wishlist::where('user_id', Auth::id())->where('cart_id',null)->where('product_id', $product->id)->first();
        // return $already_wishlist;
        if($already_wishlist) {
            request()->session()->flash('error','You already placed in wishlist');
            return back();
        }else{
            
            $wishlist = new Wishlist;
            $wishlist->user_id = Auth::id();
            $wishlist->product_id = $product->id;
            $wishlist->price = ($product->price-($product->price*$product->discount)/100);
            $wishlist->quantity = 1;
            $wishlist->amount=$wishlist->price*$wishlist->quantity;
            if ($wishlist->product->stock < $wishlist->quantity || $wishlist->product->stock <= 0) return back()->with('error','Stock not sufficient!.');
            $wishlist->save();
        }
        request()->session()->flash('success','Product successfully added to wishlist');
        return back();       
    }  
    
    public function wishlistDelete(Request $request){
        $wishlist = Wishlist::find($request->id);
        if ($wishlist) {
            $wishlist->delete();
            request()->session()->flash('success','Wishlist successfully removed');
            return back();  
        }
        request()->session()->flash('error','Error please try again');
        return back();       
    }     
}