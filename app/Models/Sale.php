<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\ShopScope;

class Sale extends Model
{
    protected $fillable = [
        'shop_id',
        'user_id',
        'total'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new ShopScope);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function kasir()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}