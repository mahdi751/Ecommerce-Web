<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'product_id', 'rate', 'review', 'status'];

    public function user_info(){
        return $this->hasOne('App\Models\User','id','user_id');
    }

    public static function getAllReview(){
        $storeId = session('current_store_id');

        return ProductReview::whereHas('product', function ($query) use ($storeId) {
            $query->whereHas('cat_info', function ($query) use ($storeId) {
                $query->where('store_id', $storeId);
            })
            ->orWhereHas('sub_cat_info', function ($query) use ($storeId) {
                $query->where('store_id', $storeId);
            });
        })
        ->with('user_info')->paginate(10);
    }
    public static function getAllUserReview(){
        $storeId = session('current_store_id');
    
        return ProductReview::whereHas('product', function ($query) use ($storeId) {
                    $query->whereHas('cat_info', function ($query) use ($storeId) {
                        $query->where('store_id', $storeId);
                    })
                    ->orWhereHas('sub_cat_info', function ($query) use ($storeId) {
                        $query->where('store_id', $storeId);
                    });
                })
                ->where('user_id', auth()->user()->id)
                ->with('user_info')
                ->paginate(10);
    }

    public function product(){
        return $this->hasOne(Product::class,'id','product_id');
    }
}
