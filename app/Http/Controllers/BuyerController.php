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


class BuyerController extends Controller
{

    public function index(Request $request){
        return redirect()->route($request->user()->role);
    }

    public function home(Request $request){

        $storeParameters = $request->route()->parameters();
        $store_id = $storeParameters['store_id'];
        session(['current_store_id' => $store_id]);

        Log::info('Current Store ID: ' . $store_id);

        if (is_numeric($store_id) && $store_id > 0) {
            Log::info('Current Store ID inside save: ' . $store_id);
            $newMemory = new Memory();
            $newMemory->storeId = $store_id;
            $newMemory->save();
            Log::info('Memory row stored for Store ID: ' . $store_id);
        } else {
            Log::warning('Invalid Store ID: ' . $store_id);
        }


        $store_id = Memory::where('storeId', '>', 0)->orderBy('id', 'desc')->value('storeId');
        Log::info('Latest Store ID from Memory table: ' . $store_id);

        $currentDateTime = Carbon::now();
        $events = Event::where('store_id', $store_id)
                        ->where('status','active')
                        ->where('start_time', '<=', $currentDateTime)
                        ->where('end_time', '>=', $currentDateTime)
                        ->orderBy('id','DESC')
                        ->paginate(10);



        $category_ids = Category::where('store_id', $store_id)
                                ->where('status', 'active')
                                ->where('is_parent', 1)
                                ->pluck('id');
        $categories = Category::whereIn('id', $category_ids)->get();



        $featured = Product::whereIn('cat_id', $category_ids)
                           ->where('status', 'active')
                           ->where('is_featured', 1)
                           ->where('is_event_item', 0)
                           ->orderBy('price', 'DESC')
                           ->limit(2)
                           ->get();



        $products = Product::whereIn('cat_id', $category_ids)
                           ->where('status', 'active')
                           ->where('is_event_item', 0)
                           ->orderBy('id', 'DESC')
                           ->limit(8)
                           ->get();

        return view('Buyers.index')
                ->with('store_id', $store_id)
                ->with('featured', $featured)
                ->with('product_lists', $products)
                ->with('categories', $categories)
                ->with('events',$events);
    }






    public function productDetail($slug){
        $product_detail= Product::getProductBySlug($slug);
        // dd($product_detail);
        return view('Buyers.pages.productDetail')->with('product_detail',$product_detail);
    }

    public function productGrids(Request $request){

      $products = Product::query();

      $store_id = $request->input('store_id');

      $store_id = Memory::where('storeId', '>', 0)->orderBy('id', 'desc')->value('storeId');


      $category_ids = Category::where('store_id',$store_id)
                              ->where('status', 'active')
                              ->where('is_parent', 1)
                              ->pluck('id');


      $products->whereIn('cat_id', $category_ids)->where('is_event_item', 0);

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

      $recent_products = Product::where('status', 'active')
                                ->where('is_event_item', 0)
                                ->orderBy('id', 'DESC')
                                ->limit(3)
                                ->get();


      if(!empty($_GET['show'])){
          $products = $products->where('status', 'active')->paginate($_GET['show']);
      } else {
          $products = $products->where('status', 'active')->paginate(9);
      }


      return view('Buyers.pages.product-grids')
              ->with('products', $products)
              ->with('recent_products', $recent_products);
  }

  public function productLists(Request $request){
    $products = Product::query();

    $store_id = $request->input('store_id');

    $store_id = Memory::where('storeId', '>', 0)->orderBy('id', 'desc')->value('storeId');





    $category_ids = Category::where('store_id', $store_id)->pluck('id');
    $categories = Category::whereIn('id', $category_ids)->get();


    $products->whereIn('cat_id', $category_ids)->where('is_event_item', 0);


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


    $recent_products = Product::where('status', 'active')->where('is_event_item', 0)->orderBy('id', 'DESC')->limit(3)->get();

    // Paginate the results
    if(!empty($_GET['show'])){
        $products = $products->where('status', 'active')->paginate($_GET['show']);
    } else {
        $products = $products->where('status', 'active')->paginate(6);
    }


    return view('Buyers.pages.product-lists')->with('products', $products)->with('recent_products', $recent_products)->with("categories",$categories);
}
public function productFilter(Request $request){

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

    $store_id = Memory::where('storeId', '>', 0)->orderBy('id', 'desc')->value('storeId');


    $category_ids = Category::where('store_id', $store_id)
                             ->where('status', 'active')
                             ->where('is_parent', 1)
                             ->pluck('id');


    $recent_products = Product::whereIn('cat_id', $category_ids)
                               ->where('is_event_item', 0)
                               ->where('status', 'active')
                               ->orderBy('id', 'DESC')
                               ->limit(3)
                               ->get();


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
    return view('Buyers.pages.product-grids')->with('products', $products)->with('recent_products', $recent_products)->with('store_id',$store_id)->with("categories",$categories);
}



public function productCat(Request $request){

  $current_store_id = $request->id;

  $products = Category::where('slug', $request->slug)
                      ->where('store_id', $current_store_id)
                      ->firstOrFail()
                      ->products()
                      ->where('status', 'active')
                      ->orderBy('id', 'DESC')
                      ->get();

  $recent_products = Product::where('status', 'active')
                            ->where('is_event_item', 0)
                            ->orderBy('id', 'DESC')
                            ->limit(3)
                            ->get();


  $view = request()->is('e-shop.loc/product-grids') ? 'Buyers.pages.product-grids' : 'Buyers.pages.product-lists';


  return view($view)
          ->with('products', $products)
          ->with('recent_products', $recent_products);
}

public function productSubCat(Request $request){

  $current_store_id = $request->id;


  $products = Category::where('slug', $request->sub_slug)
                      ->where('store_id', $current_store_id)
                      ->firstOrFail()
                      ->products()
                      ->where('status', 'active')
                      ->orderBy('id', 'DESC')
                      ->get();


  $recent_products = Product::where('status', 'active')
                            ->where('is_event_item', 0)
                            ->orderBy('id', 'DESC')
                            ->limit(3)
                            ->get();


  $view = request()->is('e-shop.loc/product-grids') ? 'Buyers.product-grids' : 'Buyers.pages.product-lists';


  return view($view)
          ->with('products', $products)
          ->with('recent_products', $recent_products);
}


public function login(){
    return view('Buyers.pages.login');
}



}
