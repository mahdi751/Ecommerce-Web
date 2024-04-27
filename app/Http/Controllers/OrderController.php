<?php

namespace App\Http\Controllers;

use App\Models\Memory;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Shipping;
use App\Models\User;
use PDF;
use Notification;
use Helper;
use CoinGate\Client;
use CoinGate\CoinGate;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Notifications\StatusNotification;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $storeId = session('current_store_id');
        $orders=Order::orderBy('id','DESC')->where('store_id', $storeId)->paginate(10);
        return view('Sellers.order.index')->with('orders',$orders);
    }
    public function createStripeSession($order){
        // dd($request->all());
        \Stripe\Stripe::setApiKey(config("stripe.sk"));
        $selectedCurrency = Cache::get('selected_currency_' . auth()->id());
        $order['quantity']=count(Helper::getAllProductFromCart());
        if($order['shipping_id']){
            $shipping=Shipping::where('id',$order['shipping_id'])->pluck('price');
            if(session('coupon')){
                $order['total_amount']=Helper::totalCartPrice()+$shipping[0]-session('coupon')['value'];
            }
            else{
                $order['total_amount']=Helper::totalCartPrice()+$shipping[0];
            }
        }
        else{

            $store_id = Memory::where('storeId', '>', 0)
                   ->where('userId', auth()->id())
                   ->orderBy('id', 'desc')
                   ->value('storeId');
            $shipping = Shipping::create([
                'type' => 'Free',
                'price' => 0,
                'status' => 'active',
                'store_id' => $store_id,
            ]);

            $order['shipping_id']=$shipping->id;
            if(session('coupon')){
                $order['total_amount']=Helper::totalCartPrice()-session('coupon')['value'];
            }
            else{
                $order['total_amount']=Helper::totalCartPrice();
            }
        }

        $status=$order->save();
        if($order)
        $users=User::where('role','admin')->first();
        $details=[
            'title'=>'New order created',
            'actionURL'=>route('order.show',$order->id),
            'fas'=>'fa-file-alt'
        ];



        session()->forget('cart');
        session()->forget('coupon');
        $session = \Stripe\Checkout\Session::create([
            'line_items' => [
                [
                    'price_data' =>[
                        'currency' => $selectedCurrency,
                        'product_data' => [
                            'name' => "anything for now",
                        ],
                        'unit_amount' => round(Helper::getAmountConverted($selectedCurrency, ($order['total_amount'])) * 100),
                    ],
                    'quantity' => $order['quantity'],
                ],
            ],
            'mode' => 'payment',
            'success_url' => route('payment.success', ['order_id' => $order['id']]),
            'cancel_url' => route('payment.cancel', ['order_id' => $order['id']]),
        ]);

        return $session;
    }



    public function stripeSuccess($order_id)
    {
        $order = Order::find($order_id);
        $order['payment_status'] = 'paid';
        $order->save();
        Cart::where('user_id', auth()->user()->id)->where('order_id', null)->update(['order_id' => $order_id]);
        request()->session()->flash('success','Your product successfully placed in order');
        return redirect()->route('home');
    }

    public function stripeCancel($order_id)
    {
        $order = Order::find($order_id);
        if ($order) {
            // Delete the order
            $order->delete();
            // Flash a message indicating the order has been deleted
            request()->session()->flash('error', 'Your order has been canceled and deleted');
        }
        request()->session()->flash('error','Your product was not placed in an order');
        return redirect()->route('home');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'first_name'=>'string|required',
            'last_name'=>'string|required',
            'address1'=>'string|required',
            'address2'=>'string|nullable',
            'coupon'=>'nullable|numeric',
            'phone'=>'numeric|required',
            'post_code'=>'string|nullable',
            'email'=>'string|required'
        ]);




        $store_id = Memory::where('storeId', '>', 0)
                   ->where('userId', auth()->id())
                   ->orderBy('id', 'desc')
                   ->value('storeId');
        // return $request->all();
        $selectedCurrency = Cache::get('selected_currency_' . auth()->id());
        if(empty(Cart::where('user_id',auth()->user()->id)->where('order_id',null)->first())){
            request()->session()->flash('error','Cart is Empty !');
            return back();
        }


        $order=new Order();
        $order_data=$request->all();
        $order_data['currency'] = $selectedCurrency;
        $order_data['store_id'] = $store_id;
        $order_data['order_number']='ORD-'.strtoupper(Str::random(10));
        $order_data['user_id']=$request->user()->id;

        // return session('coupon')['value'];
        $order_data['sub_total']=Helper::totalCartPrice();
        $order_data['quantity']=count(Helper::getAllProductFromCart());
        if(session('coupon')){
            $order_data['coupon']=session('coupon')['value'];
        }
        if($request->shipping){
            $order_data['shipping_id']=$request->shipping;
            $shipping=Shipping::where('id',$order_data['shipping_id'])->pluck('price');
            if(session('coupon')){
                $order_data['total_amount']=Helper::totalCartPrice()+$shipping[0]-session('coupon')['value'];
            }
            else{
                $order_data['total_amount']=Helper::totalCartPrice()+$shipping[0];
            }
        }
        else{

            $store_id = Memory::where('storeId', '>', 0)
                   ->where('userId', auth()->id())
                   ->orderBy('id', 'desc')
                   ->value('storeId');
            $shipping = Shipping::create([
                'type' => 'Free',
                'price' => 0,
                'status' => 'active',
                'store_id' => $store_id,
            ]);

            $order_data['shipping_id']=$shipping->id;
            if(session('coupon')){
                $order_data['total_amount']=Helper::totalCartPrice()-session('coupon')['value'];
            }
            else{
                $order_data['total_amount']=Helper::totalCartPrice();
            }
        }
        $order_data['status']="new";
        if(request('payment_method')=='stripe'){
            $order_data['payment_method']='stripe';
            $order_data['payment_status']='unpaid';
        }
        else if(request('payment_method')=='coingate'){
            $order_data['payment_method']='coingate';
            $order_data['payment_status']='unpaid';
        }else {
            $order_data['payment_method']='cod';
            $order_data['payment_status']='unpaid';
        }




        $order->fill($order_data);
        $status=$order->save();
        if($order)
        $users=User::where('role','admin')->first();
        $details=[
            'title'=>'New order created',
            'actionURL'=>route('order.show',$order->id),
            'fas'=>'fa-file-alt'
        ];



        session()->forget('cart');
        session()->forget('coupon');

        if(request('payment_method')=='stripe'){

            $session = $this->createStripeSession($order);

            return redirect()->away($session->url);
        }
        else if(request('payment_method')=='coingate'){
            $client = new Client('kUgJ_pWh5fpMDyzxCnvWZhxCDt2SriVw_zeGEtbY', true); 
            $token = hash('sha512', 'coingate' . rand());
    
            $params = array(
                'order_id'          => $order->id,
                'price_amount'      => Helper::getAmountConverted($selectedCurrency, Helper::totalCartPrice()+$shipping[0]),
                'price_currency'    => $selectedCurrency ,
                'receive_currency'  => 'EUR',
                'callback_url'      => 'http://127.0.0.1:8000/coin-gate/callback/' . $order->id . '&token=' . $token,
                'cancel_url'        => 'http://127.0.0.1:8000/coin-gate/cancel/' . $order->id,
                'success_url'       => 'http://127.0.0.1:8000/coin-gate/success/' . $order->id,
                'title'             => 'Order #' . $order->id,
                'description'       => "New Order" ,
            );
            $ordernew = $client->order->create($params);
            return redirect()->away($ordernew->payment_url);
        }




        Cart::where('user_id', auth()->user()->id)->where('order_id', null)->update(['order_id' => $order->id]);
        request()->session()->flash('success','Your product successfully placed in order');
        return redirect()->route('home');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order=Order::find($id);
        // return $order;
        return view('Sellers.order.show')->with('order',$order);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $order=Order::find($id);
        return view('Sellers.order.edit')->with('order',$order);
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
        $order=Order::find($id);
        $this->validate($request,[
            'status'=>'required|in:new,process,delivered,cancel'
        ]);
        $data=$request->all();
        if($request->status=='delivered'){
            foreach($order->cart as $cart){
                $product=$cart->product;
                $product->stock -=$cart->quantity;
                $product->save();
            }
        }
        $status=$order->fill($data)->save();
        if($status){
            request()->session()->flash('success','Successfully updated order');
        }
        else{
            request()->session()->flash('error','Error while updating order');
        }
        return redirect()->route('order.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order=Order::find($id);
        if($order){
            $status=$order->delete();
            if($status){
                request()->session()->flash('success','Order Successfully deleted');
            }
            else{
                request()->session()->flash('error','Order can not deleted');
            }
            return redirect()->route('order.index');
        }
        else{
            request()->session()->flash('error','Order can not found');
            return redirect()->back();
        }
    }

    public function orderTrack(){
        return view('frontend.pages.order-track');
    }

    public function productTrackOrder(Request $request){
        $order=Order::where('user_id',auth()->user()->id)->where('order_number',$request->order_number)->first();
        if($order){
            if($order->status=="new"){
            request()->session()->flash('success','Your order has been placed. please wait.');
            return redirect()->route('home');

            }
            elseif($order->status=="process"){
                request()->session()->flash('success','Your order is under processing please wait.');
                return redirect()->route('home');

            }
            elseif($order->status=="delivered"){
                request()->session()->flash('success','Your order is successfully delivered.');
                return redirect()->route('home');

            }
            else{
                request()->session()->flash('error','Your order canceled. please try again');
                return redirect()->route('home');

            }
        }
        else{
            request()->session()->flash('error','Invalid order numer please try again');
            return back();
        }
    }

    public function pdf(Request $request){
        $order=Order::getAllOrder($request->id);
        $file_name=$order->order_number.'-'.$order->first_name.'.pdf';
        $pdf=PDF::loadview('Sellers.order.pdf',compact('order'));
        return $pdf->download($file_name);
    }
    public function incomeChart(Request $request){
        $year=\Carbon\Carbon::now()->year;
        // dd($year);
        $items=Order::with(['cart_info'])->whereYear('created_at',$year)->where('status','delivered')->get()
            ->groupBy(function($d){
                return \Carbon\Carbon::parse($d->created_at)->format('m');
            });
        $result=[];
        foreach($items as $month=>$item_collections){
            foreach($item_collections as $item){
                $amount=$item->cart_info->sum('amount');
                $m=intval($month);
                isset($result[$m]) ? $result[$m] += $amount :$result[$m]=$amount;
            }
        }
        $data=[];
        for($i=1; $i <=12; $i++){
            $monthName=date('F', mktime(0,0,0,$i,1));
            $data[$monthName] = (!empty($result[$i]))? number_format((float)($result[$i]), 2, '.', '') : 0.0;
        }
        return $data;
    }


    public function callBackCoinGate($order_id)
    {
        dd('HI callBackCoinGate');
    }

    public function successCoinGate($order_id)
    {
        $order = Order::find($order_id);
        $order['payment_status'] = 'paid';
        $order->save();
        Cart::where('user_id', auth()->user()->id)->where('order_id', null)->update(['order_id' => $order_id]);
        request()->session()->flash('success','Your product successfully placed in order');
        return redirect()->route('home');
    }


    public function cancelCoinGate($order_id)
    {
        $order = Order::find($order_id);
        if ($order) {
            $order->delete();
            request()->session()->flash('error', 'Your order has been canceled and deleted');
        }
        request()->session()->flash('error','Your product was not placed in an order');
        return redirect()->route('home');
    }

    public function failCoinGate()
    {
        dd('HI failCoinGate');
    }
}

