<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = 'thuong_hieu';
    protected $primarykey = 'id';
    public $timestamps = false;
    public $incrementing = false;
    protected $guarded = [];

    public function Product()
    {
        return $this->hasMany('App\Models\Product', 'id_th', 'id');
    }
}
