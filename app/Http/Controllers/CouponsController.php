<?php

namespace App\Http\Controllers;
use App\Models\Coupons;
use Illuminate\Http\Request;
use App\Models\Cart;
class CouponsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $storeId = session('current_store_id');
        $coupon=Coupons::orderBy('id','DESC')->where('store_id',$storeId)->paginate(10);
        return view('Sellers.coupon.index')->with('coupons',$coupon);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Sellers.coupon.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $storeId = session('current_store_id');
        $request['store_id'] = $storeId;
        // return $request->all();
        $this->validate($request,[
            'code'=>'string|required',
            'type'=>'required|in:fixed,percent',
            'value'=>'required|numeric',
            'status'=>'required|in:active,inactive'
        ]);
        $data=$request->all();
        $status=Coupons::create($data);
        if($status){
            request()->session()->flash('success','Coupon Successfully added');
        }
        else{
            request()->session()->flash('error','Please try again!!');
        }
        return redirect()->route('coupon.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $storeId = session('current_store_id');
        $coupon=Coupons::find($id);
        if($coupon){
            return view('Sellers.coupon.edit')->with('coupon',$coupon);
        }
        else{
            return view('Sellers.coupon.index')->with('error','Coupon not found');
        }
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

        $storeId = session('current_store_id');
        $request['store_id'] = $storeId;
        $coupon=Coupons::find($id);
        $this->validate($request,[
            'code'=>'string|required',
            'type'=>'required|in:fixed,percent',
            'value'=>'required|numeric',
            'status'=>'required|in:active,inactive'
        ]);
        $data=$request->all();
        
        $status=$coupon->fill($data)->save();
        if($status){
            request()->session()->flash('success','Coupon Successfully updated');
        }
        else{
            request()->session()->flash('error','Please try again!!');
        }
        return redirect()->route('coupon.index');
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $coupon=Coupons::find($id);
        if($coupon){
            $status=$coupon->delete();
            if($status){
                request()->session()->flash('success','Coupon successfully deleted');
            }
            else{
                request()->session()->flash('error','Error, Please try again');
            }
            return redirect()->route('coupon.index');
        }
        else{
            request()->session()->flash('error','Coupon not found');
            return redirect()->back();
        }
    }

    public function couponStore(Request $request){
        // return $request->all();
        $coupon=Coupons::where('code',$request->code)->first();
        // dd($coupon);
        if(!$coupon){
            request()->session()->flash('error','Invalid coupon code, Please try again');
            return back();
        }
        if($coupon){
            $total_price=Cart::where('user_id',auth()->user()->id)->where('order_id',null)->sum('price');
            // dd($total_price);
            session()->put('coupon',[
                'id'=>$coupon->id,
                'code'=>$coupon->code,
                'value'=>$coupon->discount($total_price)
            ]);
            request()->session()->flash('success','Coupon successfully applied');
            return redirect()->back();
        }
    }
}
