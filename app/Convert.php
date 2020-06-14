<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Convert extends Model
{
    protected $fillable= [
        'product_id',
        'category_id',
        'convert_awal',
        'convert_akhir',
        'stok',
    ];

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function product()
    {
        return $this->belongsTo('App\Product');
    }

    public function informationUnit()
    {
        return $this->belongsTo('App\InformationUnit');
    }

}
