<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

function createTestManager(): User
{
    Role::firstOrCreate(['name' => 'manager']);
    Role::firstOrCreate(['name' => 'user']);

    $user = User::factory()->create([
        'name' => 'Test Manager',
        'email' => 'test@manager.com',
        'password' => Hash::make('password'),
    ]);

    $user->assignRole('manager');

    return $user;
}
