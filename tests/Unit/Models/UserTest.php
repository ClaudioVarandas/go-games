<?php

namespace Tests\Unit\Models;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('casts role to UserRole enum', function () {
    $user = User::factory()->create(['role' => UserRole::Admin]);

    expect($user->role)->toBeInstanceOf(UserRole::class)
        ->and($user->role)->toBe(UserRole::Admin);

    $userGamer = User::factory()->create(['role' => UserRole::Gamer]);
    expect($userGamer->role)->toBeInstanceOf(UserRole::class)
        ->and($userGamer->role)->toBe(UserRole::Gamer);
});

it('correctly identifies an admin user', function () {
    $adminUser = User::factory()->create(['role' => UserRole::Admin]);
    $gamerUser = User::factory()->create(['role' => UserRole::Gamer]);

    expect($adminUser->isAdmin())->toBeTrue()
        ->and($gamerUser->isAdmin())->toBeFalse();
});

it('correctly identifies a gamer user', function () {
    $adminUser = User::factory()->create(['role' => UserRole::Admin]);
    $gamerUser = User::factory()->create(['role' => UserRole::Gamer]);

    expect($gamerUser->isGamer())->toBeTrue()
        ->and($adminUser->isGamer())->toBeFalse();
});

it('defaults new users to the gamer role via factory', function () {
    $user = User::factory()->create(); // No role specified, should use factory default

    expect($user->role)->toBe(UserRole::Gamer)
        ->and($user->isGamer())->toBeTrue()
        ->and($user->isAdmin())->toBeFalse();
});
