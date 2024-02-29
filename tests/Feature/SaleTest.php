<?php

use App\Models\Sale;
use App\Models\Product;
use App\Models\User;

// Golden path
test('Get all sales', function () {
    $this->actingAs($user = User::factory()->create());

    $products = Product::factory(6)->create();
    $sales = Sale::factory(3)->create();

    $sales->each(function ($sale) use ($products) {
        $sale->products()->attach($products->random()->id, ['quantity' => 1]);
    });

    $this->getJson('/api/sales')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'amount',
                    'products' => [
                        '*' => [
                            'id',
                            'name',
                            'price',
                            'description',
                            'quantity'
                        ],
                    ],
                    'created_at',
                    'updated_at',
                ],
            ],
        ])
        ->assertJsonCount(3, 'data');
});

test('Get a sale by id', function () {
    $this->actingAs($user = User::factory()->create());

    $products = Product::factory(2)->create();
    $sale = Sale::factory()->recycle($user)->create();

    $sale->products()->attach($products->random()->id, ['quantity' => 1]);

    $this->getJson('/api/sales/' . $sale->id)
        ->assertOk()
        ->assertJson([
            'data' => [
                'id' => $sale->id,
                'amount' => $sale->total(),
                'products' => [
                    0 => [
                        'id' => $sale->products->first()->id,
                        'name' => $sale->products->first()->name,
                        'price' => $sale->products->first()->price,
                        'description' => $sale->products->first()->description,
                        'quantity' => 1
                    ]
                ],
                'created_at' => $sale->created_at->toISOString(),
                'updated_at' => $sale->updated_at->toISOString(),
            ],
        ]);
});

test('Create a sale', function () {
    $this->actingAs($user = User::factory()->create());

    $products = Product::factory(2)->create();

    $this->postJson('/api/sales', [
        'products' => [
            ['id' => $products->first()->id, 'quantity' => 1],
            ['id' => $products->last()->id, 'quantity' => 1],
        ],
    ])
        ->assertCreated()
        ->assertJsonStructure([
            'data' => [
                'id',
                'amount',
                'products' => [
                    '*' => [
                        'id',
                        'name',
                        'price',
                        'description',
                        'quantity'
                    ],
                ],
                'created_at',
                'updated_at',
            ],
        ]);
});

test('Add more products to a sale', function () {
    $this->actingAs($user = User::factory()->create());

    $products = Product::factory(3)->create();
    $sale = Sale::factory()->recycle($user)->create();
    $sale->products()->attach($products->first()->id, ['quantity' => 1]);

    $this->putJson('/api/sales/' . $sale->id, [
        'products' => [
            ['id' => $products->get(2)->id, 'quantity' => 1],
            ['id' => $products->last()->id, 'quantity' => 1],
        ],
    ])
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'amount',
                'products' => [
                    '*' => [
                        'id',
                        'name',
                        'price',
                        'description',
                        'quantity'
                    ],
                ],
                'created_at',
                'updated_at',
            ],
        ]);
});

test('Cancel a sale', function () {
    $this->actingAs($user = User::factory()->create());

    $sale = Sale::factory()->recycle($user)->create();

    $this->deleteJson('/api/sales/' . $sale->id)
        ->assertNoContent();

    $this->assertSoftDeleted('sales', ['id' => $sale->id]);
});

// Sad path - Validation
test('Create a sale with invalid data', function () {
    $this->actingAs($user = User::factory()->create());

    Product::factory(3)->create();
    $this->postJson('/api/sales', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'products'
        ]);
})->with([
    'products' => [
        'string',
        1,
        true,
        false,
        null,
        ['id' => -1, 'quantity' => 1],
        ['id' => 1, 'quantity' => 0],
    ],
]);

test('Add more products to a sale with invalid data', function () {
    $this->actingAs($user = User::factory()->create());

    $products = Product::factory(3)->create();
    $sale = Sale::factory()->recycle($user)->create();
    $sale->products()->attach($products->first()->id, ['quantity' => 1]);

    $this->putJson('/api/sales/' . $sale->id, [])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'products'
        ]);
})->with([
    'products' => [
        'string',
        1,
        true,
        false,
        null,
        ['id' => 1, 'quantity' => 0],
        ['id' => -1, 'quantity' => 1],
    ],
]);

// Sad path - Authorization
test('Get all sales without being authenticated', function () {
    $this->getJson('/api/sales')
        ->assertUnauthorized();
});

test('Get a sale by id without being authenticated', function () {
    $sale = Sale::factory()->create();

    $this->getJson('/api/sales/' . $sale->id)
        ->assertUnauthorized();
});

test('Create a sale without being authenticated', function () {
    $this->postJson('/api/sales', [])
        ->assertUnauthorized();
});

test('Add more products to a sale without being authenticated', function () {
    $sale = Sale::factory()->create();

    $this->putJson('/api/sales/' . $sale->id, [])
        ->assertUnauthorized();
});

test('Cancel a sale without being authenticated', function () {
    $sale = Sale::factory()->create();

    $this->deleteJson('/api/sales/' . $sale->id)
        ->assertUnauthorized();
});

test('Get a sale by id from another user', function () {
    $this->actingAs($user = User::factory()->create());

    $sale = Sale::factory()->create();

    $this->getJson('/api/sales/' . $sale->id)
        ->assertForbidden();
});

test('Add more products to a sale from another user', function () {
    $this->actingAs($user = User::factory()->create());

    $sale = Sale::factory()->create();

    $this->putJson('/api/sales/' . $sale->id, [])
        ->assertForbidden();
});

test('Cancel a sale from another user', function () {
    $this->actingAs($user = User::factory()->create());

    $sale = Sale::factory()->create();

    $this->deleteJson('/api/sales/' . $sale->id)
        ->assertForbidden();
});

// Sad path - Not found
test('Get a sale by id that does not exist', function () {
    $this->actingAs($user = User::factory()->create());

    $this->getJson('/api/sales/1')
        ->assertNotFound();
});

test('Add more products to a sale that does not exist', function () {
    $this->actingAs($user = User::factory()->create());

    $this->putJson('/api/sales/1', [])
        ->assertNotFound();
});

test('Cancel a sale that does not exist', function () {
    $this->actingAs($user = User::factory()->create());

    $this->deleteJson('/api/sales/1')
        ->assertNotFound();
});

// Sad path - Conflict
test('Cancel a sale that is already canceled', function () {
    $this->actingAs($user = User::factory()->create());

    $sale = Sale::factory()->recycle($user)->create();

    $this->deleteJson('/api/sales/' . $sale->id)
        ->assertNoContent();

    $this->deleteJson('/api/sales/' . $sale->id)
        ->assertNotFound();
});
