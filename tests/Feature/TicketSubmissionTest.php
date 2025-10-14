<?php

use App\Models\Customer;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
use function Pest\Laravel\{post};


beforeEach(function () {
    $this->validPayload = [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'phone' => '+998901234567',
        'subject' => 'Issue with payment',
        'text' => 'Payment failed during checkout.',
    ];
});

test('a new ticket can be submitted successfully', function () {
    post('/api/tickets', $this->validPayload)
        ->assertStatus(201)
        ->assertJsonStructure(['data' => ['id', 'subject', 'status']]);

    $this->assertDatabaseHas('customers', ['email' => 'john.doe@example.com']);
    $this->assertDatabaseCount('tickets', 1);
});

test('submitting a second ticket within 24 hours fails', function () {
    post('/api/tickets', $this->validPayload)
        ->assertStatus(201);

    post('/api/tickets', $this->validPayload)
        ->assertStatus(429)
        ->assertJson(['message' => 'Вы можете отправить только одну заявку в течение 24 часов. Пожалуйста, подождите.']);
});

test('a ticket can be submitted after 24 hours', function () {
    Ticket::factory()->create([
        'customer_id' => Customer::factory()->create(['email' => 'john.doe@example.com']),
        'created_at' => Carbon::now()->subHours(25),
    ]);

    post('/api/tickets', $this->validPayload)
        ->assertStatus(201);
});
