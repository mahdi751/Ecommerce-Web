<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupons extends Model
{
    use HasFactory;
    protected $fillable = ['code', 'type', 'value', 'status', 'store_id'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    public static function findByCode($code){
        return self::where('code',$code)->first();
    }
    public function discount($total){
        if($this->type=="fixed"){
            return $this->value;
        }
        elseif($this->type=="percent"){
            return ($this->value /100)*$total;
        }
        else{
            return 0;
        }
    }
}
