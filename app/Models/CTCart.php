<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CTCart extends Model
{
    protected $table = 'ct_giohang';
    protected $primarykey = 'id';
    public $timestamps = false;
    public $incrementing = false;
    protected $guarded = [];

    public function Product()
    {
        return $this->belongsTo('App\Models\Product', 'id_sp', 'id');
    }
    public function Cart()
    {
        return $this->belongsTo('App\Models\Cart', 'id_gh', 'id');
    }
}
