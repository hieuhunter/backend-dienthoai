<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class CTBill extends Model{

    protected $table = 'ct_hoadon';
    protected $primarykey = 'id';
    public $timestamps = false;
    public $incrementing = false;
    protected $guarded = [];

    public function Product()
    {
        return $this->belongsTo('App\Models\Product', 'id_sp', 'id');
    }
    public function Bill()
    {
        return $this->belongsTo('App\Models\Bill', 'id_hd', 'id');
    }
}