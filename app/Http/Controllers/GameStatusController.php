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

        $games = $userGameStatuses->map(function ($status) {
            $game = $status->game;
            $game->userStatus = $status->status->value;

            return $game;
        });

        // Get user's game lists for the AddToList functionality
        $gameLists = $user->gameLists()
            ->select('id', 'name', 'type')
            ->orderBy('name')
            ->get();

        // Fetch or create the user's backlog list
        $backlogList = $user->gameLists()
            ->where('type', 'backlog')
            ->first();

        if (! $backlogList) {
            $backlogList = $user->gameLists()->make();
            $backlogList->name = 'Backlog';
            $backlogList->type = 'backlog';
            $backlogList->is_public = false;
            $user->gameLists()->save($backlogList);
        }

        // Fetch games in backlog
        $backlogGames = $backlogList->games()->get();

        // Fetch or create the user's wishlist list
        $wishlistList = $user->gameLists()
            ->where('type', 'wishlist')
            ->first();

        if (! $wishlistList) {
            $wishlistList = $user->gameLists()->make();
            $wishlistList->name = 'Wishlist';
            $wishlistList->type = 'wishlist';
            $wishlistList->is_public = false;
            $user->gameLists()->save($wishlistList);
        }

        // Fetch games in wishlist
        $wishlistGames = $wishlistList->games()->get();

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
