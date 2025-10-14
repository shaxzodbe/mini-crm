<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $managerRole = Role::firstOrCreate(['name' => UserRoleEnum::MANAGER->value]);
        Role::firstOrCreate(['name' => UserRoleEnum::ADMIN->value]);

        $manager = User::firstOrCreate(
            ['email' => 'manager@crm.test'],
            [
                'name' => 'Manager',
                'password' => Hash::make('password'),
            ]
        );
        $manager->assignRole($managerRole);

        Ticket::factory()
            ->count(30)
            ->create();
    }
}
