<?php

namespace Database\Factories;

use App\Enums\TicketStatusEnum;
use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        $statuses = TicketStatusEnum::cases();

        return [
            'customer_id' => Customer::factory(),
            'subject' => $this->faker->sentence(3),
            'text' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement($statuses),
            'manager_response_at' => $this->faker->optional(0.5)->dateTimeBetween('-1 week', 'now'),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
