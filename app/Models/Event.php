<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'slug', 'description', 'photo', 'store_id','start_time','end_time'];
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
     public function GetProducts()
    {
        return $this->hasMany(Product::class);
     }
    public static function getAllEvents(){
        $storeId = session('current_store_id');
        return  Event::orderBy('id','DESC')->paginate(10)->where('store_id',$storeId);
    }
    

}
