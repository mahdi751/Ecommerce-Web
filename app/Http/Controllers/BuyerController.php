<?php

namespace App\Http\Controllers;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Category;
use App\Models\Memory;
use App\Models\PostTag;
use App\Models\PostCategory;
use App\Models\Post;
use App\Models\Cart;
use App\Models\Brand;
use App\User;
use Auth;
use Session;
use Newsletter;
use DB;
use Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;


class BuyerController extends Controller
{

    public function index(Request $request){
        return redirect()->route($request->user()->role);
    }

    public function home(Request $request){
        // Retrieve the current store ID from the request
        $storeParameters = $request->route()->parameters();
        $store_id = $storeParameters['store_id'];
        session(['current_store_id' => $store_id]);
    
        // Log the current store ID
        Log::info('Current Store ID: ' . $store_id);
    
        // Check if the store ID is greater than 0 and log the action
       
        if (is_numeric($store_id) && $store_id > 0) {
            Log::info('Current Store ID inside save: ' . $store_id);
            $newMemory = new Memory();
            $newMemory->storeId = $store_id;
            $newMemory->save();
            Log::info('Memory row stored for Store ID: ' . $store_id);
        } else {
            Log::warning('Invalid Store ID: ' . $store_id);
        }
    
        // Retrieve the latest store ID from the memory table and log it
        $store_id = Memory::where('storeId', '>', 0)->orderBy('id', 'desc')->value('storeId');
        Log::info('Latest Store ID from Memory table: ' . $store_id);
    
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
        // Retrieve events, categories, featured products, and products
        $currentDateTime = Carbon::now();
        $events = Event::where('store_id', $store_id)
                        ->where('status','active')
                        ->where('start_time', '<=', $currentDateTime)
                        ->where('end_time', '>=', $currentDateTime)
                        ->orderBy('id','DESC')
                        ->paginate(10);
        // Log the retrieved events
      
    
        $category_ids = Category::where('store_id', $store_id)
                                ->where('status', 'active')
                                ->where('is_parent', 1)
                                ->pluck('id');
        $categories = Category::whereIn('id', $category_ids)->get();
        // Log the retrieved categories

    
        $featured = Product::whereIn('cat_id', $category_ids)
                           ->where('status', 'active')
                           ->where('is_featured', 1)
                           ->where('is_event_item', 0)
                           ->orderBy('price', 'DESC')
                           ->limit(2)
                           ->get();
        // Log the retrieved featured products
      
    
        $products = Product::whereIn('cat_id', $category_ids)
                           ->where('status', 'active')
                           ->where('is_event_item', 0)
                           ->orderBy('id', 'DESC')
                           ->limit(8)
                           ->get();
        // Log the retrieved products
     
    
        // Pass the retrieved data to the frontend view
        return view('Buyers.index')
                ->with('selectedCurrency', $selectedCurrency)
                ->with("selectedCurrencySign",$selectedCurrencySign)
                ->with('store_id', $store_id)
                ->with('featured', $featured)
                ->with('product_lists', $products)
                ->with('categories', $categories)
                ->with('events',$events);
    }




    public function aboutUs(){
        return view('Buyers.pages.about-us');
    }

    public function contact(){
        return view('Buyers.pages.contact');
    }

    public function productDetail($slug){
        $product_detail= Product::getProductBySlug($slug);
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
        // dd($product_detail);
        return view('Buyers.pages.productDetail')
            ->with('product_detail',$product_detail)
            ->with('selectedCurrency', $selectedCurrency)
            ->with("selectedCurrencySign",$selectedCurrencySign);
            
        }

    public function productGrids(Request $request){
      // Initialize query builder for the Product model
      $products = Product::query();
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

      $store_id = $request->input('store_id');
      // Retrieve the current store ID from the request
      //$store_id = Memory::findOrFail(1)->storeId;
      $store_id = Memory::where('storeId', '>', 0)->orderBy('id', 'desc')->value('storeId');
      
      // Retrieve category IDs associated with the store ID
      $category_ids = Category::where('store_id',$store_id)
      ->where('status', 'active')
      ->where('is_parent', 1)
      ->pluck('id');
      
      // Filter products to include only those belonging to the retrieved category IDs
      $products->whereIn('cat_id', $category_ids)->where('is_event_item', 0);
      
      // Sort products based on user-selected option
      if(!empty($_GET['sortBy'])){
          if($_GET['sortBy'] == 'title'){
              $products->where('status', 'active')->orderBy('title', 'ASC');
          }
          if($_GET['sortBy'] == 'price'){
              $products->orderBy('price', 'ASC');
          }
        }
        
        // Filter products based on price range if provided
        if(!empty($_GET['price'])){
            $price = explode('-', $_GET['price']);
            $products->whereBetween('price', $price);
        }
        
        // Retrieve recent products for display
        $recent_products = Product::where('status', 'active')
        ->where('is_event_item', 0)
        ->orderBy('id', 'DESC')
        ->limit(3)
        ->get();
        
        // Paginate products based on user-selected option or default to 9 products per page
        if(!empty($_GET['show'])){
            $products = $products->where('status', 'active')->paginate($_GET['show']);
        } else {
            $products = $products->where('status', 'active')->paginate(9);
        }
        
        // Render the view with filtered, sorted, and paginated products along with recent products
        return view('Buyers.pages.product-grids')
        
              ->with('selectedCurrency', $selectedCurrency)
              ->with("selectedCurrencySign",$selectedCurrencySign)
        ->with('products', $products)
              ->with('recent_products', $recent_products);
  }

  public function productLists(Request $request){
    $products = Product::query();

    $store_id = $request->input('store_id');
   // $store_id = $request->input('store_id');

    //dd($store_id);
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


    // Retrieve the store ID from the request
    //$store_id = Memory::findOrFail(1)->storeId;
    $store_id = Memory::where('storeId', '>', 0)->orderBy('id', 'desc')->value('storeId');
    
    
    
    
    // Retrieve the category IDs associated with the store ID
    $category_ids = Category::where('store_id', $store_id)->pluck('id');
    $categories = Category::whereIn('id', $category_ids)->get();
    
    // Apply category filter based on the retrieved category IDs
    $products->whereIn('cat_id', $category_ids)->where('is_event_item', 0);
    
    // Apply other filters if provided
    if(!empty($_GET['sortBy'])){
        if($_GET['sortBy'] == 'title'){
            $products->where('status', 'active')->orderBy('title', 'ASC');
        }
        if($_GET['sortBy'] == 'price'){
            $products->orderBy('price', 'ASC');
        }
    }
    
    if(!empty($_GET['price'])){
        $price = explode('-', $_GET['price']);
        $products->whereBetween('price', $price);
    }
    
    // Retrieve recent products
    $recent_products = Product::where('status', 'active')->where('is_event_item', 0)->orderBy('id', 'DESC')->limit(3)->get();
    
    // Paginate the results
    if(!empty($_GET['show'])){
        $products = $products->where('status', 'active')->paginate($_GET['show']);
    } else {
        $products = $products->where('status', 'active')->paginate(6);
    }
    
    // Return the view with the products
    return view('Buyers.pages.product-lists')
    ->with('selectedCurrency', $selectedCurrency)
    ->with("selectedCurrencySign",$selectedCurrencySign)
    ->with('products', $products)->with('recent_products', $recent_products)->with("categories",$categories);
}
public function productFilter(Request $request){
    //$store_id = Memory::findOrFail(1)->storeId;
    $store_id = Memory::where('storeId', '>', 0)->orderBy('id', 'desc')->value('storeId');
    
    $category_ids = Category::where('store_id', $store_id)
    ->where('status', 'active')
    ->where('is_parent', 1)
    ->pluck('id');

$categories = Category::whereIn('id', $category_ids)->get();

  $data = $request->all();
  $showURL = "";
  if(!empty($data['show'])){
      $showURL .= '&show='.$data['show'];
  }

  $sortByURL = '';
  if(!empty($data['sortBy'])){
      $sortByURL .= '&sortBy='.$data['sortBy'];
  }

  $catURL = "";
  if(!empty($data['category'])){
      $store_id = $request->route()->parameters();
      dd($store_id);
      $category_ids = Category::where('store_id', $store_id)->pluck('id');
      foreach($data['category'] as $category){
          if($category_ids->contains($category)) {
              if(empty($catURL)){
                  $catURL .= '&category='.$category;
              } else {
                  $catURL .= ','.$category;
              }
          }
      }
  }

  $priceRangeURL = "";
  if(!empty($data['price_range'])){
      $priceRangeURL .= '&price='.$data['price_range'];
  }

  if(request()->is('e-shop.loc/product-grids')){
    return redirect()->route('product-grids', $catURL.$priceRangeURL.$showURL.$sortByURL.'&store_id='.$store_id)->with("categories",$categories);
} else {
    return redirect()->route('product-lists', $catURL.$priceRangeURL.$showURL.$sortByURL.'&store_id='.$store_id)->with("categories",$categories);
}
}


public function productSearch(Request $request){
    // Retrieve the store ID from the session
    //$store_id = Memory::findOrFail(1)->storeId;
    $store_id = Memory::where('storeId', '>', 0)->orderBy('id', 'desc')->value('storeId');
    $selectedCurrency = Cache::get('selected_currency_' . auth()->id());
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


    
    
    // Retrieve category IDs associated with the store ID
    $category_ids = Category::where('store_id', $store_id)
    ->where('status', 'active')
    ->where('is_parent', 1)
    ->pluck('id');
    
    // Retrieve recent products for display
    $recent_products = Product::whereIn('cat_id', $category_ids)
                               ->where('is_event_item', 0)
                               ->where('status', 'active')
                               ->orderBy('id', 'DESC')
                               ->limit(3)
                               ->get();
                               
                               // Search for products with the given search query and associated with the categories of the store
                               $products = Product::whereIn('cat_id', $category_ids)
                       ->where('is_event_item', 0)
                       ->where('status', 'active')
                       ->where(function($query) use ($request) {
                            $query->where('title', 'like', '%' . $request->search . '%')
                            ->orWhere('slug', 'like', '%' . $request->search . '%')
                            ->orWhere('description', 'like', '%' . $request->search . '%')
                            ->orWhere('summary', 'like', '%' . $request->search . '%')
                            ->orWhere('price', 'like', '%' . $request->search . '%');
                        })
                       ->orderBy('id', 'DESC')
                       ->paginate(9);
                       
                       $categories = Category::whereIn('id', $category_ids)->get();
                       
    // Return the view with the search results and recent products
    return view('Buyers.pages.product-grids')
    ->with('selectedCurrency', $selectedCurrency)
    ->with("selectedCurrencySign",$selectedCurrencySign)
    ->with('products', $products)->with('recent_products', $recent_products)->with('store_id',$store_id)->with("categories",$categories);
}



public function productCat(Request $request){
  // Retrieve the current store ID from the request
  $current_store_id = $request->id;
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


  // Retrieve products associated with the category slug and store ID
  $products = Category::where('slug', $request->slug)
  ->where('store_id', $current_store_id)
  ->firstOrFail()
  ->products()
  ->where('status', 'active')
  ->orderBy('id', 'DESC')
  ->get();
  
  // Retrieve recent products
  $recent_products = Product::where('status', 'active')
  ->where('is_event_item', 0)
  ->orderBy('id', 'DESC')
  ->limit(3)
  ->get();
  
  // Determine which view to return based on the request URL
  $view = request()->is('e-shop.loc/product-grids') ? 'Buyers.pages.product-grids' : 'Buyers.pages.product-lists';
  
  // Pass the retrieved products and recent products to the view
  return view($view)
  ->with('selectedCurrency', $selectedCurrency)
  ->with("selectedCurrencySign",$selectedCurrencySign)
  ->with('products', $products)
          ->with('recent_products', $recent_products);
}

public function productSubCat(Request $request){
  // Retrieve the current store ID from the request
  $current_store_id = $request->id;
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

  
  // Retrieve products associated with the subcategory slug and store ID
  $products = Category::where('slug', $request->sub_slug)
  ->where('store_id', $current_store_id)
  ->firstOrFail()
  ->products()
  ->where('status', 'active')
  ->orderBy('id', 'DESC')
  ->get();
  
  // Retrieve recent products
  $recent_products = Product::where('status', 'active')
  ->where('is_event_item', 0)
  ->orderBy('id', 'DESC')
  ->limit(3)
  ->get();
  
  // Determine which view to return based on the request URL
  $view = request()->is('e-shop.loc/product-grids') ? 'Buyers.product-grids' : 'Buyers.pages.product-lists';
  
  // Pass the retrieved products and recent products to the view
  return view($view)
            ->with('selectedCurrency', $selectedCurrency)
            ->with("selectedCurrencySign",$selectedCurrencySign)
          ->with('products', $products)
          ->with('recent_products', $recent_products);
}


public function login(){
    return view('Buyers.pages.login');
}


public function updateCurrency(Request $request) {
    $selectedCurrency = $request->input('cur');
    session(['selected_currency' => $selectedCurrency]);

    Cache::put('selected_currency_' . auth()->id(), $selectedCurrency, now()->addHours(24)); 
    $store_id = Memory::where('storeId', '>', 0)->orderBy('id', 'desc')->value('storeId');
    Log::info('Latest Store ID from Memory table: ' . $store_id);
    return redirect()->route('homestore', ['store_id' => $store_id]);

}


}
