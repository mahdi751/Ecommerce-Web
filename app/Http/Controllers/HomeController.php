<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
         $stores = Store::all();
        // ->where('status','active');
        return view('home')->with('stores', $stores);
    }


    public function storePressed(Request $request){
        $current_store_id = $request->id;
        session(['current_store_id' => $current_store_id]);
        $stores = Store::all();
        return view('home')->with('stores', $stores);
    }

}
