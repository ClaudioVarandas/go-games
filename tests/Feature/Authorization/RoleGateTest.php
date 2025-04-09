<?php

namespace Tests\Feature\Authorization;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;

uses(RefreshDatabase::class);

it('allows admin users to view admin panel', function () {
    $adminUser = User::factory()->create(['role' => UserRole::Admin]);

    expect(Gate::forUser($adminUser)->allows('view-admin-panel'))->toBeTrue();
});

it('prevents gamer users from viewing admin panel', function () {
    $gamerUser = User::factory()->create(['role' => UserRole::Gamer]);

    expect(Gate::forUser($gamerUser)->denies('view-admin-panel'))->toBeTrue();
});

it('prevents guest users (null user) from viewing admin panel', function () {
    // Simulate a guest user by checking the gate without a user instance
    expect(Gate::allows('view-admin-panel'))->toBeFalse();
});
