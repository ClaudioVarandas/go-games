<?php

use App\Enums\UserRole;
use Database\Factories\UserFactory;

use function Pest\Laravel\actingAs;

test('guests cannot access the admin panel', function () {
    $this->get('/admin')
        ->assertRedirect('/admin/login');
});

test('non-admin users cannot access the admin panel', function () {
    $user = UserFactory::new()->create(['role' => UserRole::Gamer]);

    actingAs($user)
        ->get('/admin')
        ->assertForbidden();

    actingAs($user)
        ->get('/admin/users')
        ->assertForbidden();
});

test('admin users can access the admin panel', function () {
    $admin = UserFactory::new()->create(['role' => UserRole::Admin]);

    actingAs($admin)
        ->get('/admin')
        ->assertOk();

    actingAs($admin)
        ->get('/admin/users') // Check resource route
        ->assertOk();
});
