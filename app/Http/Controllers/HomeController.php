<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Rules\MatchOldPassword;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;

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
         $stores = Store::all()->where('status','active');
        return view('home')->with('stores', $stores);
    }





    public function profile(){
        $profile=Auth()->user();
        return view('Buyers.user.users.profile')->with('profile',$profile);
    }

    public function profileUpdate(Request $request,$id){
        $user=User::findOrFail($id);
        $data=$request->all();
        $status=$user->fill($data)->save();
        if($status){
            request()->session('success','Successfully updated your profile');
        }
        else{
            request()->session('error','Please try again!');
        }
        return redirect()->back();
    }

    // Order
    public function orderIndex(){
        $orders=Order::orderBy('id','DESC')->where('user_id',auth()->user()->id)->paginate(10);
        return view('Buyers.user.order.index')->with('orders',$orders);
    }
    public function userOrderDelete($id)
    {
        $order=Order::find($id);
        if($order){
           if($order->status=="process" || $order->status=='delivered' || $order->status=='cancel'){
                return redirect()->back()->with('error','You can not delete this order now');
           }
           else{
                $status=$order->delete();
                if($status){
                    request()->session('success','Order Successfully deleted');
                }
                else{
                    request()->session('error','Order can not deleted');
                }
                return redirect()->route('Buyers.user.order.index');
           }
        }
        else{
            request()->session('error','Order can not found');
            return redirect()->back();
        }
    }

    public function orderShow($id)
    {
        $order=Order::find($id);
        return view('Buyers.user.order.show')->with('order',$order);
    }
    
    public function changePassword(){
        return view('Buyers.user.layouts.userPasswordChange');
    }
    public function changPasswordStore(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);
       User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);

     return redirect()->route('user-profile')->with('success','Password successfully changed');
    }





}

