<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'gio_hang';
    protected $primarykey = 'id';
    public $timestamps = false;
    public $incrementing = true;
    protected $guarded = [];

    public function User()
    {
        return $this->belongsTo('App\Models\User', 'id_kh', 'id');
    }
    public function CTCart()
    {
        return $this->hasMany('App\Models\CTCart', 'id_gh', 'id');
    }
}
