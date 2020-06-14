<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable= [
        'name',
        'category_id',
        'unit_id',
        'information_unit_id',
        'code_item',
        'harga_jual',
        'stok',

    ];

    public function unit()
    {
        return $this->belongsTo('App\Unit');
    }

    public function informationUnit()
    {
        return $this->belongsTo('App\InformationUnit');
    }

    public function supplier()
    {
        return $this->hasOne('App\Supplier');
    }

}
