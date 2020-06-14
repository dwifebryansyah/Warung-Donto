<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InformationUnit extends Model
{
    protected $fillable= [
        'category_id',
        'jumlah_awal',
        'satuan_awal_id',
        'jumlah_akhir',
        'satuan_akhir_id',
    ];

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function unitOne()
    {
        return $this->belongsTo('App\Unit', 'satuan_awal_id', 'id');
    }

    public function unitTwo()
    {
        return $this->belongsTo('App\Unit', 'satuan_akhir_id', 'id');
    }

}
