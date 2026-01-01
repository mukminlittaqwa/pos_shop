<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{

        public function toArray($request)
            {
                return [
                    'id'         => $this->id,
                    'total'      => number_format($this->total, 2),
                    'kasir'      => $this->kasir->name,
                    'items'      => SaleItemResource::collection($this->whenLoaded('items')),
                    'payment'    => $this->whenLoaded('payment', function () {
                        return [
                            'method'      => $this->payment->method,
                            'amount_paid' => number_format($this->payment->amount_paid, 2),
                            'change'      => $this->payment->change ? number_format($this->payment->change, 2) : null,
                            'status'      => $this->payment ? 'paid' : 'pending',
                        ];
                    }),
                    'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                ];
            }
}
