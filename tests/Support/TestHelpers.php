<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

function createTestManager(): User
{
    $manager = User::factory()->create();
    Role::firstOrCreate(['name' => 'manager']);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    $manager->assignRole('manager');
    return $manager;
}
