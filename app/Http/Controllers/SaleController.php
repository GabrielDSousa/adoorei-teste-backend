<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use App\Http\Resources\SaleResource;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Sale::class, 'sale');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return SaleResource::collection(Sale::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        /** @var \App\Models\User */
        $user = auth()->user();
        $sale = $user->sales()->create();

        foreach ($validated['products'] as $product) {
            $sale->products()->attach($product['id'], ['quantity' => $product['quantity']]);
        }

        return SaleResource::make($sale);
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        return SaleResource::make($sale);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        foreach ($validated['products'] as $product) {
            $sale->products()->attach($product['id'], ['quantity' => $product['quantity']]);
        }

        return SaleResource::make($sale);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        $sale->delete();

        return response()->noContent();
    }
}
