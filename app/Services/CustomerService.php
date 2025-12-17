<?php

namespace App\Services;

use App\Models\Customer;

class CustomerService
{
    public function findOrCreate(?string $name, string $phone, string $email): Customer
    {
        return Customer::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'phone' => $phone,
            ]
        );
    }
}
