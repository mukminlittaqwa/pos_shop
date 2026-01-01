<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'product_name' => $this->product->name,
            'quantity'     => $this->quantity,
            'price'        => number_format($this->price, 2),
            'subtotal'     => number_format($this->price * $this->quantity, 2),
        ];
    }
}
