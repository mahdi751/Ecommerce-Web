<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Event;

use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products=Product::getProductsByStore();
        // return $products;
        return view('Sellers.product.index')->with('products',$products);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $storeId = session('current_store_id');
        $category = Category::where('is_parent', 1)
                              ->where('store_id', $storeId)
                              ->get();
    
        $events = Event::where('store_id', $storeId)->get();
        return view('Sellers.product.create')->with('categories',$category)->with('events',$events);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        $this->validateProduct($request);

        $data=$request->all();
        if ($request->has('is_event_item') && $request->input('is_event_item') == 1) {
            $this->validateEventItem($request);
            $data['is_event_item'] = 1;
            $data['starting_bid_price'] = $request->input('starting_bid_price');
            $data['minimum_bid_increment'] = $request->input('minimum_bid_increment');
            $data['closing_bid'] = $request->input('closing_bid');
            $data['bid_status'] = $request->input('bid_status');
        }
    

        $data['event_id'] = $request->input('event_id');
        $slug=Str::slug($request->title);
        $count=Product::where('slug',$slug)->count();
        if($count>0){
            $slug=$slug.'-'.date('ymdis').'-'.rand(0,999);
        }
        $data['slug']=$slug;
        $data['is_featured']=$request->input('is_featured',0);
        $size=$request->input('size');
        if($size){
            $data['size']=implode(',',$size);
        }
        else{
            $data['size']='';
        }

     
        $status=Product::create($data);
        if($status){
            request()->session()->flash('success','Product Successfully added');
        }
        else{
            request()->session()->flash('error','Please try again!!');
        }
        return redirect()->route('product.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function showProductsForEvent($event_id)
    {
        $products = Product::where('event_id', $event_id)->get();
        $event = Event::findOrFail($event_id);

        return view('Buyers.event.eventItems.index', ['products' => $products, 'event' => $event]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       
        $product=Product::findOrFail($id);
        $storeId = session('current_store_id');
        $category = Category::where('is_parent', 1)
                              ->where('store_id', $storeId)
                              ->get();
        $items=Product::where('id',$id)->get();
        $events = Event::where('store_id', $storeId)->get();
        // return $items;
        return view('Sellers.product.edit')->with('product',$product)
                    ->with('categories',$category)->with('items',$items)->with('events',$events);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product=Product::findOrFail($id);

        $this->validateProduct($request);

        $data=$request->all();

        if ($request->has('is_event_item') && $request->input('is_event_item') == 1) {
            $this->validateEventItem($request);
            $data['is_event_item'] = 1;
            $data['starting_bid_price'] = $request->input('starting_bid_price');
            $data['minimum_bid_increment'] = $request->input('minimum_bid_increment');
            $data['closing_bid'] = $request->input('closing_bid');
            $data['bid_status'] = $request->input('bid_status');
            $data['event_id'] = $request->input('event_id');
        }

        $data['is_featured']=$request->input('is_featured',0);
        $size=$request->input('size');
        if($size){
            $data['size']=implode(',',$size);
        }
        else{
            $data['size']='';
        }
        // return $data;
        $status=$product->fill($data)->save();
        if($status){
            request()->session()->flash('success','Product Successfully updated');
        }
        else{
            request()->session()->flash('error','Please try again!!');
        }
        return redirect()->route('product.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product=Product::findOrFail($id);
        $status=$product->delete();
        
        if($status){
            request()->session()->flash('success','Product successfully deleted');
        }
        else{
            request()->session()->flash('error','Error while deleting product');
        }
        return redirect()->route('product.index');
    }
    
protected function validateProduct($request)
{
    return $this->validate($request, [
        'title' => 'string|required',
        'summary' => 'string|required',
        'description' => 'string|nullable',
        'photo' => 'string|nullable',
        'size' => 'nullable',
        'stock' => 'required|numeric',
        'cat_id' => 'required|exists:categories,id',
        'child_cat_id' => 'nullable|exists:categories,id',
        'is_featured' => 'sometimes|in:1',
        'status' => 'required|in:active,inactive',
        'condition' => 'required|in:default,new,hot',
        'price' => 'required|numeric',
        'discount' => 'nullable|numeric',
    ]);
}

protected function validateEventItem($request)
{
    return $this->validate($request, [
        'starting_bid_price' => 'required|numeric',
        'minimum_bid_increment' => 'required|numeric',
        'closing_bid' => 'required|numeric',
        'bid_status' => 'required|in:open,closed',
        'event_id' => 'required|exists:events,id',
    ]);
}
}

