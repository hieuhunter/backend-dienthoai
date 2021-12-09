<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'san_pham';
    protected $primarykey = 'id';
    protected $guarded = [];
    public $timestamps = false;

    protected $appends = ['image_url'];
    
    public function Category()
    {
        return $this->belongsTo('App\Models\Category', 'id_dm', 'id');
    }

    public function Brand()
    {
        return $this->belongsTo('App\Models\Brand', 'id_th', 'id');
    }

    public function getImageUrlAttribute() 
    {
        return config('app.url') . $this->hinh;
    }
}   