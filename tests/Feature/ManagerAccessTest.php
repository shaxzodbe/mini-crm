<?php

use App\Models\User;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);
use function Pest\Laravel\{get, actingAs};

test('guest users cannot access the manager panel', function () {
    get(route('manager.tickets.index'))
        ->assertRedirect('/login');
});

test('authenticated users without manager role cannot access the manager panel', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('manager.tickets.index'))
        ->assertStatus(403);
});

test('manager user is redirected to the tickets index after login', function () {
    $manager = createTestManager();

    actingAs($manager)
        ->get(route('manager.dashboard'))
        ->assertRedirect(route('manager.tickets.index'));

    actingAs($manager)
        ->get(route('manager.tickets.index'))
        ->assertStatus(200);
});
