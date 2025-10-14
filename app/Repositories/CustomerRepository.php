<?php

namespace App\Repositories;

use App\Models\Customer;

class CustomerRepository
{
    public function findOrCreateByPhoneOrEmail(string $phone, string $email, array $data = []): Customer
    {
        $customer = Customer::where('phone', $phone)
            ->orWhere('email', $email)
            ->first();

        if ($customer) {
            $customer->update(array_merge(['phone' => $phone, 'email' => $email], $data));
            return $customer;
        }

        return Customer::create(array_merge([
            'phone' => $phone,
            'email' => $email,
        ], $data));
    }
}

