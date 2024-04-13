<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index()
    {
        $events =Event::getAllEvents();
        return view('Sellers.event.index')->with('events',$events);
    }

    public function create()
    {
      
        return view('Sellers.event.create');
    }
    public function store(Request $request)
    {
        $storeId = session('current_store_id');
        $request['store_id'] = $storeId;


            $this->validate($request,[
            'title'=>'string|required',
            'description'=>'string|nullable',
            'photo'=>'string|nullable',
            'store_id'=>'required',
            'start_time'=>'required',
            'end_time'=>'required',
        ]);

        $data= $request->all();
        $slug=Str::slug($request->title);

        $count=Event::where('slug',$slug)->count();
        if($count>0){
            $slug=$slug.'-'.date('ymdis').'-'.rand(0,999);
        }
        $data['slug']=$slug;
      

        $status=Event::create($data);
        if($status){
            request()->session()->flash('success','Event successfully added');
        }
        else{
            request()->session()->flash('error','Error occurred, Please try again!');
        }
        return redirect()->route('event.index');
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

    public function edit($id)
    {

        $event=Event::findOrFail($id);
        return view('Sellers.event.edit')->with('event',$event);
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
        $event=Event::findOrFail($id);

        $storeId = session('current_store_id');
        $request['store_id'] = $storeId;
  


            $this->validate($request,[
            'title'=>'string|required',
            'description'=>'string|nullable',
            'photo'=>'string|nullable',
            'store_id'=>'required',
            'start_time'=>'required',
            'end_time'=>'required',
        ]);

        $data= $request->all();
        $status=$event->fill($data)->save();
        if($status){
            request()->session()->flash('success','Event successfully updated');
        }
        else{
            request()->session()->flash('error','Error occurred, Please try again!');
        }
        return redirect()->route('event.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $event=Event::findOrFail($id);
        $status=$event->delete();

        if($status){
            request()->session()->flash('success','Event successfully deleted');
        }
        else{
            request()->session()->flash('error','Error while deleting event');
        }
        return redirect()->route('event.index');
    }

   


}
