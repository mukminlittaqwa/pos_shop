<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\ShopScope;

class Product extends Model
{
    protected $fillable = [
        'shop_id',
        'name',
        'description',
        'price'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new ShopScope);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}