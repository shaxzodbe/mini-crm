<?php

namespace Tests\Feature;

use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_ticket(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->postJson('/api/v1/tickets', [
            'name' => 'John Doe',
            'phone' => '+79991234567',
            'email' => 'john@example.com',
            'subject' => 'Help me',
            'text' => 'I have a problem',
            'files' => [$file],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data' => ['id', 'subject', 'status']]);

        $this->assertDatabaseHas('customers', ['email' => 'john@example.com']);
        $this->assertDatabaseHas('tickets', ['subject' => 'Help me']);
    }

    public function test_rate_limiting(): void
    {
        $this->postJson('/api/v1/tickets', [
            'name' => 'John Doe',
            'phone' => '+79991234567',
            'email' => 'john@example.com',
            'subject' => 'First ticket',
            'text' => 'Content',
        ])->assertStatus(201);

        $this->postJson('/api/v1/tickets', [
            'name' => 'John Doe',
            'phone' => '+79991234567',
            'email' => 'john@example.com',
            'subject' => 'Second ticket',
            'text' => 'Content',
        ])->assertStatus(429);
    }

    public function test_statistics(): void
    {
        Ticket::factory()->create(['created_at' => Carbon::now()]);
        Ticket::factory()->create(['created_at' => Carbon::now()->subDays(2)]);

        $response = $this->getJson('/api/v1/ticket-statistics');

        $response->assertStatus(200)
            ->assertJsonPath('data.day', 1)
            ->assertJsonPath('data.week', 2);
    }
}
