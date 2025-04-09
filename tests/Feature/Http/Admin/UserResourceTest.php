<?php

namespace Tests\Feature\Http\Admin;

use App\Filament\Resources\UserResource;
use Database\Factories\UserFactory;
use Tests\TestCase; // Re-add import for base TestCase

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

test('admin can list users', function () {
    $admin = UserFactory::new()->admin()->create();
    $users = UserFactory::new()->count(3)->create();

    actingAs($admin);

    livewire(UserResource\Pages\ListUsers::class)
        ->assertCanSeeTableRecords($users)
        ->assertCountTableRecords(4); // 3 users + 1 admin
});

test('admin can render create user page', function () {
    $admin = UserFactory::new()->admin()->create();
    $url = UserResource::getUrl('create');

    actingAs($admin)
        ->get($url)
        ->assertOk();
});

test('admin can render edit user page', function () {
    $admin = UserFactory::new()->admin()->create();
    $userToEdit = UserFactory::new()->create();

    actingAs($admin)
        ->get(UserResource::getUrl('edit', ['record' => $userToEdit]))
        ->assertOk();
});
