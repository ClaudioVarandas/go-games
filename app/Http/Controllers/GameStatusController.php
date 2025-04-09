<?php

namespace App\Http\Controllers;

use App\Actions\UpdateGameStatusAction;
use App\Http\Requests\UpdateGameStatusRequest;
use App\Models\Game;
use App\Models\UserGameStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GameStatusController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get all games with statuses for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $statusFilter = $request->query('status');

        $query = UserGameStatus::where('user_id', $user->id);

        // Apply status filter if provided
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $userGameStatuses = $query->with('game')->get();

        // Fetch or create the user's backlog list
        $backlogList = $user->gameLists()->firstOrCreate(
            ['type' => 'backlog'],
            ['name' => 'Backlog', 'is_public' => false]
        );

        $backlogGames = $backlogList->games()->get();
        $backlogGameIds = $backlogGames->pluck('id')->toArray();

        // Fetch or create the user's wishlist list
        $wishlistList = $user->gameLists()->firstOrCreate(
            ['type' => 'wishlist'],
            ['name' => 'Wishlist', 'is_public' => false]
        );

        $wishlistGames = $wishlistList->games()->get();
        $wishlistGameIds = $wishlistGames->pluck('id')->toArray();

        $games = $userGameStatuses->map(function ($status) use ($backlogGameIds, $wishlistGameIds, $backlogList, $wishlistList) {
            $game = $status->game;
            return [
                'id' => $game->id,
                'name' => $game->name,
                'slug' => $game->slug,
                'cover_url' => $game->cover_url,
                'release_date' => $game->release_date,
                'rating' => $game->rating,
                'genres' => $game->genres ?? [],
                'platforms' => $game->platforms ?? [],
                'userStatus' => $status->status->value,
                'isInBacklog' => in_array($game->id, $backlogGameIds),
                'isInWishlist' => in_array($game->id, $wishlistGameIds),
                'backlogListId' => $backlogList->id,
                'wishlistListId' => $wishlistList->id,
            ];
        });

        // Get user's game lists for the AddToList functionality
        $gameLists = $user->gameLists()
            ->orderBy('name')
            ->get()
            ->map(function ($list) {
                return [
                    'id' => $list->id,
                    'name' => $list->name,
                    'type' => $list->type,
                    'description' => $list->description,
                ];
            });

        return Inertia::render('MyGames/Index', [
            'games' => $games,
            'gameLists' => $gameLists,
            'backlogGames' => $backlogGames,
            'wishlistGames' => $wishlistGames,
            'filters' => [
                'status' => $statusFilter,
            ],
        ]);
    }

    /**
     * Store or update a game status.
     */
    public function store(UpdateGameStatusRequest $request, Game $game, UpdateGameStatusAction $action)
    {
        $status = $action->handle(auth()->user(), $game, $request->validated());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Game status updated successfully.',
                'status' => $status,
            ]);
        }

        return back()->with('success', 'Game status updated successfully.');
    }

    /**
     * Remove the game status.
     */
    public function destroy(Request $request, Game $game, UpdateGameStatusAction $action)
    {
        $action->handle(auth()->user(), $game, ['status' => null]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Game status removed successfully.',
            ]);
        }

        return back()->with('success', 'Game status removed successfully.');
    }
}
