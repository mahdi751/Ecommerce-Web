<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $userID = Auth::id();
         $stores=Store::all()->where("owner_id",$userID);
         return view('Sellers.store.index')->with('stores',$stores);
    }

    public function create()
    {
        return view('Sellers.store.create');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userID = Auth::id();
        $request['owner_id'] = $userID;

        $this->validate($request, [
            'name' => 'string|required',
            'description' => 'string|required',
            'email' => 'string|nullable',
            'phone_number' => 'required|required',
            'address' => 'string|nullable',
            'photo'=>'string|nullable',
            'owner_id' => 'required|exists:users,id',
        ]);

            $data= $request->all();
            $status=Store::create($data);
            if($status){
                request()->session()->flash('success','Store successfully added');
            }
            else{
                request()->session()->flash('error','Error occurred, Please try again!');
            }
            return redirect()->route('store.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = User::select(\DB::raw("COUNT(*) as count"), \DB::raw("DAYNAME(created_at) as day_name"), \DB::raw("DAY(created_at) as day"))
        ->where('created_at', '>', Carbon::today()->subDay(6))
        ->groupBy('day_name','day')
        ->orderBy('day')
        ->get();
     $array[] = ['Name', 'Number'];
     foreach($data as $key => $value)
     {
       $array[++$key] = [$value->day_name, $value->count];
     }

        $store=Store::find($id);
        session(['current_store_name' => $store->name]);
        session(['current_store_id' => $id]);
        return view('Sellers.InStoreIndex')->with('users', json_encode($array));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        $store=Store::findOrFail($id);
        return view('Sellers.store.edit')->with('store',$store);
    }

    public function update(Request $request, $id)
    {
        $store=Store::findOrFail($id);
        $userID = Auth::id();
        $request['owner_id'] = $userID;

        $this->validate($request, [
            'name' => 'string|required',
            'description' => 'string|required',
            'email' => 'string|nullable',
            'phone_number' => 'required|required',
            'address' => 'string|nullable',
            'photo'=>'string|nullable',
            'owner_id' => 'required|exists:users,id',
        ]);

        $data= $request->all();

        $status=$store->fill($data)->save();
        if($status){
            request()->session()->flash('success','Store successfully updated');
        }
        else{
            request()->session()->flash('error','Error occurred, Please try again!');
        }
        return redirect()->route('store.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $store=Store::findOrFail($id);
        $status=$store->delete();

        if($status){
            request()->session()->flash('success','Store successfully deleted');
        }
        else{
            request()->session()->flash('error','Error while deleting Store');
        }
        return redirect()->route('store.index');
    }
}
