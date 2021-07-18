<?php

namespace App\Models;

use App\Models\SaleReturnPayment;
use App\Traits\InvoiceAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleReturn extends Model
{
    use SoftDeletes, InvoiceAttributes;

    protected $fillable = [
        'warehouse_id',
        'customer_id',
        'tax',
        'discount',
        'discount_method',
        'status',
        'shipping',
        'note',
        'date'
    ];

    protected $append = ['reference'];

    protected $fieldActionName = 'price';

    public function details()
    {
        return $this->hasMany(SaleReturnDetail::class);
    }

    public function payments()
    {
        return $this->hasMany(SaleReturnPayment::class);
    }

    public function getReferenceAttribute()
    {
        return 'RT_' . (1110 + $this->id);
    }
}
