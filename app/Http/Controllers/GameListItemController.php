<?php

namespace App\Http\Controllers;

use App\Actions\AddGameToListAction;
use App\Actions\RemoveGameFromListAction;
use App\Http\Requests\AddGameToListRequest;
use App\Models\Game;
use App\Models\GameList;
use Illuminate\Http\Request;

class GameListItemController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddGameToListRequest $request, GameList $gameList, AddGameToListAction $action)
    {
        $game = Game::findOrFail($request->game_id);
        $item = $action->handle($gameList, $game, $request->validated());

        // Check if this is an API request (from AddToListButton)
        if ($request->wantsJson() || $request->ajax()) {
            // Return JSON response with the game list item for API requests
            return response()->json([
                'success' => true,
                'message' => 'Game added to list successfully.',
                'gameListItem' => $item
            ]);
        }

        return back()->with('success', 'Game added to list successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GameList $gameList, Game $game, RemoveGameFromListAction $action)
    {
        // Check if the user can remove games from this list
        if (auth()->id() !== $gameList->user_id) {
            abort(403, 'You do not have permission to remove games from this list.');
        }

        $action->handle($gameList, $game);

        return back()->with('success', 'Game removed from list successfully.');
    }
}
