<?php

namespace Tests\Feature\Http\Admin;

use App\Enums\UserRole;
use App\Filament\Resources\GameListResource;
use Database\Factories\GameListFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase; // Re-add import for base TestCase

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

// Remove uses(RefreshDatabase::class) as it's handled by TestCase via Pest.php

test('admin can list game lists', function () {
    $admin = UserFactory::new()->create(['role' => UserRole::Admin]);
    // Create lists associated with different users
    $lists = GameListFactory::new()->count(3)->recycle(UserFactory::new()->count(2)->create())->create();

    actingAs($admin);

    livewire(GameListResource\Pages\ListGameLists::class)
        ->assertCanSeeTableRecords($lists)
        ->assertCountTableRecords(3);
});

test('admin can render create game list page', function () {
    $admin = UserFactory::new()->create(['role' => UserRole::Admin]);

    actingAs($admin)
        ->get(GameListResource::getUrl('create'))
        ->assertOk();
});

test('admin can render edit game list page', function () {
    $admin = UserFactory::new()->create(['role' => UserRole::Admin]);
    $listToEdit = GameListFactory::new()->create();

    actingAs($admin)
        ->get(GameListResource::getUrl('edit', ['record' => $listToEdit]))
        ->assertOk();
});
