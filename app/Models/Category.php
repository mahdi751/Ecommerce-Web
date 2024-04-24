<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'slug', 'summary', 'photo', 'is_parent', 'parent_id', 'store_id', 'status'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function childCategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function GetProducts()
    {
        return $this->hasMany(Product::class);
    }

    public function child_cat(){
        return $this->hasMany('App\Models\Category','parent_id','id')->where('status','active');
    }



    public static function getAllCategory(){
        $storeId = session('current_store_id');
        return  Category::orderBy('id','DESC')->paginate(10)->where('store_id',$storeId);
    }

    public static function getChildByParentID($id){
        $storeId = session('current_store_id');
        return Category::where('parent_id',$id)->orderBy('id','ASC')->pluck('title','id')->where('store_id',$storeId);
    }

    public static function getAllParentWithChild(){
        $storeId = session('current_store_id');
        return Category::with('child_cat')->where('is_parent',1)->where('status','active')->orderBy('title','ASC')->where('store_id',$storeId)->get();
    }

    public static function countActiveCategory(){
        $data=Category::where('status','active')->count();
        if($data){
            return $data;
        }
        return 0;
    }
    public static function countStoreActiveCategory(){
        $storeId = session('current_store_id');
        $data=Category::where('status','active')->where('store_id', $storeId)->count();
        if($data){
            return $data;
        }
        return 0;
    }
   

}
