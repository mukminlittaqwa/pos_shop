<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $products = Product::query()
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            })
            ->latest()
            ->paginate(15);

        return ProductResource::collection($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
        ]);

        $product = Product::create([
            'shop_id'     => $request->user()->shop_id,
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
        ]);

        return new ProductResource($product);
    }

    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function update(Request $request, Product $product)
    {
        if ($product->shop_id !== $request->user()->shop_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
        ]);

        $product->update($request->only('name', 'description', 'price'));

        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {
        if ($product->shop_id !== request()->user()->shop_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted']);
    }
}