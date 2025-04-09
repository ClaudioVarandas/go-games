<?php

namespace App\Http\Controllers;

use App\Actions\CreateGameListAction;
use App\Actions\DeleteGameListAction;
use App\Actions\UpdateGameListAction;
use App\Http\Requests\CreateGameListRequest;
use App\Http\Requests\UpdateGameListRequest;
use App\Models\GameList;
use Inertia\Inertia;

class GameListController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gameLists = auth()->user()->gameLists()
            ->withCount('games')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('GameLists/Index', [
            'gameLists' => $gameLists,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('GameLists/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateGameListRequest $request, CreateGameListAction $action)
    {
        $user = $request->user();
        $gameList = $action->handle($user, $request->validated());

        // Check if this is an API request (from AddToListButton)
        if ($request->wantsJson() || $request->ajax()) {
            // Return JSON response with the game list for API requests
            return response()->json([
                'success' => true,
                'message' => 'Game list created successfully.',
                'gameList' => $gameList,
            ]);
        }

        // For regular form submissions, redirect to the show page
        return redirect()->route('game-lists.show', $gameList)
            ->with('success', 'Game list created successfully.')
            ->with('gameList', $gameList);
    }

    /**
     * Display the specified resource.
     */
    public function show(GameList $gameList)
    {
        // Check if the user can view this list
        if (! $gameList->is_public && auth()->id() !== $gameList->user_id) {
            abort(403, 'You do not have permission to view this list.');
        }

        $gameList->load(['games', 'user']);

        return Inertia::render('GameLists/Show', [
            'gameList' => $gameList,
            'canEdit' => auth()->id() === $gameList->user_id,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GameList $gameList)
    {
        // Check if the user can edit this list
        if (auth()->id() !== $gameList->user_id) {
            abort(403, 'You do not have permission to edit this list.');
        }

        return Inertia::render('GameLists/Edit', [
            'gameList' => $gameList,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGameListRequest $request, GameList $gameList, UpdateGameListAction $action)
    {
        $action->handle($gameList, $request->validated());

        return redirect()->route('game-lists.show', $gameList)
            ->with('success', 'Game list updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GameList $gameList, DeleteGameListAction $action)
    {
        // Check if the user can delete this list
        if (auth()->id() !== $gameList->user_id) {
            abort(403, 'You do not have permission to delete this list.');
        }

        $action->handle($gameList);

        return redirect()->route('game-lists.index')
            ->with('success', 'Game list deleted successfully.');
    }
}
