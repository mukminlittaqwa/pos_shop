<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        if (is_null($this->resource)) {
            return [];
        }

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description ?? '-',
            'price'       => number_format($this->price, 2),
            'created_at'  => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}