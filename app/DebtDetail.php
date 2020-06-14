<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DebtDetail extends Model
{
    protected $fillable= [
        'debt_id',
        'product_id',
        'qty',
    ];

    public function debt()
    {
        return $this->belongsTo('App\Debt');
    }

    public function product()
    {
        return $this->belongsTo('App\Product');
    }

}
