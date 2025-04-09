<?php

namespace Tests\Feature\Http\Admin;

use App\Enums\UserRole;
use App\Filament\Resources\GameResource;
use Database\Factories\GameFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase; // Re-add import for base TestCase

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

// Remove uses(RefreshDatabase::class) as it's handled by TestCase via Pest.php

test('admin can list games', function () {
    $admin = UserFactory::new()->create(['role' => UserRole::Admin]);
    $games = GameFactory::new()->count(3)->create();

    actingAs($admin);

    livewire(GameResource\Pages\ListGames::class)
        ->assertCanSeeTableRecords($games)
        ->assertCountTableRecords(3);
});

test('admin can render create game page', function () {
    $admin = UserFactory::new()->create(['role' => UserRole::Admin]);

    actingAs($admin)
        ->get(GameResource::getUrl('create'))
        ->assertOk();
});

test('admin can render edit game page', function () {
    $admin = UserFactory::new()->create(['role' => UserRole::Admin]);
    $gameToEdit = GameFactory::new()->create();

    actingAs($admin)
        ->get(GameResource::getUrl('edit', ['record' => $gameToEdit]))
        ->assertOk();
});
