<?php

use App\Models\Product;

// Golden path
test('Products available', function () {
    Product::factory(10)->create();

    $products = Product::All();

    $this->getJson('/api/products')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'price',
                    'description'
                ],
            ],
        ])
        ->assertJsonCount(10, 'data')
        ->assertJsonFragment([
            'name' => $products->first()->name,
            'price' => $products->first()->price,
            'description' => $products->first()->description,
        ]);
});

test('Get product by id', function () {
    $product = Product::factory()->create();

    $this->getJson('/api/products/' . $product->id)
        ->assertOk()
        ->assertJson([
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'description' => $product->description,
            ],
        ]);
});

// Sad path
test('Product not found', function () {
    $this->getJson('/api/products/1')
        ->assertNotFound()
        ->assertJson([
            'message' => 'No query results for model [App\Models\Product] 1',
        ]);
});

test('Products not available', function () {
    $this->getJson('/api/products')
        ->assertOk()
        ->assertJson([
            'data' => [],
        ]);
});
