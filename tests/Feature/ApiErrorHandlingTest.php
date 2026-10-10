<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiErrorHandlingTest extends TestCase
{
    public function test_validation_error_uses_standard_contract(): void
{
    $response = $this->postJson('/api/products', []);

    dump([
        'status' => $response->status(),
        'body' => $response->json(),
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonPath('success', false)
        ->assertJsonPath('error.code', 'VALIDATION_ERROR')
        ->assertJsonStructure([
            'success',
            'error' => ['code', 'message', 'details'],
        ]);
}

    public function test_missing_product_returns_404(): void
    {
        $this->getJson('/api/products/999')
            ->assertStatus(404)
            ->assertJsonPath('error.code', 'RESOURCE_NOT_FOUND');
    }

    public function test_business_exception_returns_409(): void
    {
        $this->postJson('/api/orders', [])
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'INSUFFICIENT_STOCK')
            ->assertJsonPath('error.message', 'Insufficient inventory');
    }

    public function test_unauthenticated_request_returns_401(): void
    {
        $this->getJson('/api/profile')
            ->assertStatus(401)
            ->assertJsonPath('error.code', 'UNAUTHENTICATED');
    }

    public function test_unexpected_error_does_not_expose_internal_details(): void
    {
        $this->app['config']->set('app.debug', false);

        $response = $this->getJson('/api/system-check');

        $response->assertStatus(500)
            ->assertJsonPath('error.code', 'INTERNAL_SERVER_ERROR')
            ->assertJsonPath(
                'error.message',
                'An unexpected server error occurred.'
            )
            ->assertDontSee('Simulated internal processing failure')
            ->assertDontSee('stack trace');
    }


    
}