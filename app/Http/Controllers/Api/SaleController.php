<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\SaleResource;

class SaleController extends Controller
{

    public function index(Request $request)
    {
        $sales = Sale::with(['kasir', 'payment'])
            ->when($request->search, function ($q) use ($request) {
                $q->whereHas('kasir', fn($sq) => $sq->where('name', 'like', "%{$request->search}%"))
                  ->orWhere('id', $request->search);
            })
            ->latest()
            ->paginate(15);

        return SaleResource::collection($sales);
    }

    public function store(Request $request)
    {
        $request->validate([
            'items'          => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,transfer,debit,qr',
            'amount_paid'    => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            $user    = $request->user();
            $shopId  = $user->shop_id;
            $total   = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->shop_id !== $shopId) {
                    return response()->json(['message' => 'Product not in your shop'], 422);
                }

                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'price'      => $product->price,
                ];
            }

            $sale = Sale::create([
                'shop_id' => $shopId,
                'user_id' => $user->id,
                'total'   => $total,
            ]);

            foreach ($itemsData as $data) {
                $sale->items()->create($data);
            }

            $change = null;
            if ($request->payment_method === 'cash' && $request->amount_paid > $total) {
                $change = $request->amount_paid - $total;
            }

            Payment::create([
                'sale_id'     => $sale->id,
                'amount_paid' => $request->amount_paid,
                'method'      => $request->payment_method,
                'change'      => $change,
            ]);

            $sale->load(['items.product', 'kasir', 'payment']);

            return new SaleResource($sale);
        });
    }

    public function show(Sale $sale)
    {
        $sale->load(['items.product', 'kasir', 'payment']);
        return new SaleResource($sale);
    }
}