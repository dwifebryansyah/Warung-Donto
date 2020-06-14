<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    protected $fillable= [
        'code_debt',
        'order_id',
        'tanggal',
        'total_sebelumnya',
        'total_bayar',
        'kembalian',
        'sudah_bayar',
        'sisa_bayar',
        'name',
        'status',
    ];
}
