<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'api');
    }

    /** @test */
    public function it_can_get_all_customers()
    {
        Customer::factory()->count(5)->create();

        $response = $this->getJson('/api/customers');

        $response->assertStatus(200)
                 ->assertJsonCount(5, 'data')
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'name', 'phone', 'address']
                     ]
                 ]);
    }

    /** @test */
    public function it_can_create_a_customer()
    {
        $customerData = [
            'name' => 'John Doe',
            'phone' => '1234567890',
            'address' => '123 Test St',
        ];

        $response = $this->postJson('/api/customers', $customerData);

        $response->assertStatus(201)
                 ->assertJsonFragment($customerData);

        $this->assertDatabaseHas('customers', $customerData);
    }

    /** @test */
    public function it_fails_to_create_a_customer_with_invalid_data()
    {
        $response = $this->postJson('/api/customers', ['name' => '']);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('name');
    }

    /** @test */
    public function it_can_get_a_single_customer()
    {
        $customer = Customer::factory()->create();

        $response = $this->getJson('/api/customers/' . $customer->id);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'name' => $customer->name,
                     'phone' => $customer->phone,
                 ]);
    }

    /** @test */
    public function it_can_update_a_customer()
    {
        $customer = Customer::factory()->create();

        $updateData = [
            'name' => 'Jane Doe',
            'phone' => '0987654321',
        ];

        $response = $this->putJson('/api/customers/' . $customer->id, $updateData);

        $response->assertStatus(200)
                 ->assertJsonFragment($updateData);

        $this->assertDatabaseHas('customers', array_merge(['id' => $customer->id], $updateData));
    }

    /** @test */
    public function it_can_delete_a_customer()
    {
        $customer = Customer::factory()->create();

        $response = $this->deleteJson('/api/customers/' . $customer->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }
}
