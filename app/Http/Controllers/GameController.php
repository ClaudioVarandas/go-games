<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use MarcReichel\IGDBLaravel\Models\Game as IGDBGame;

class GameController extends Controller
{
    /**
     * Display the homepage with upcoming game releases.
     */
    public function index()
    {
        // Get games releasing in the next week
        $nextWeek = Carbon::now()->addWeek();
        $nextWeekGames = $this->getUpcomingGames(Carbon::now(), $nextWeek);

        // Get games releasing in the current month
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $thisMonthGames = $this->getUpcomingGames($startOfMonth, $endOfMonth);

        // Get user's game lists if authenticated
        $gameLists = $this->getUserGameLists();

        return Inertia::render('Games/Index', [
            'nextWeekGames' => $nextWeekGames,
            'thisMonthGames' => $thisMonthGames,
            'gameLists' => $gameLists,
        ]);
    }

    /**
     * Search for games.
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        // Get user's game lists if authenticated
        $gameLists = $this->getUserGameLists();
        
        if (empty($query)) {
            return Inertia::render('Games/Search', [
                'games' => [],
                'query' => '',
                'gameLists' => $gameLists,
            ]);
        }

        try {
            $games = IGDBGame::search($query)
                ->with(['cover', 'genres', 'platforms'])
                ->where('category', 0) // Main game
                ->limit(20)
                ->get();

            // Process games one by one to identify which one causes the issue
            $formattedGames = collect();
            foreach ($games as $game) {
                try {
                    $formattedGame = $this->formatGame($game);
                    
                    // Save the game to the database to ensure it has a local ID
                    $dbGame = Game::updateOrCreate(
                        ['igdb_id' => $formattedGame['igdb_id']],
                        $formattedGame
                    );
                    
                    // Add the database ID to the formatted game
                    $formattedGame['id'] = $dbGame->id;
                    
                    // Add user's status for this game if authenticated
                    if (auth()->check()) {
                        try {
                            $userStatus = $dbGame->statusForUser(auth()->id());
                            $formattedGame['userStatus'] = $userStatus && $userStatus->status ? $userStatus->status->value : null;
                        } catch (\Exception $statusEx) {
                            // If there's an issue with the status, just set it to null
                            $formattedGame['userStatus'] = null;
                        }
                    }
                    
                    $formattedGames->push($formattedGame);
                } catch (\Exception $gameEx) {
                    // Skip this game if there's an issue
                    continue;
                }
            }

            return Inertia::render('Games/Search', [
                'games' => $formattedGames,
                'query' => $query,
                'gameLists' => $gameLists,
            ]);
        } catch (\Exception $e) {
            // Log the exception for debugging
            \Log::error('Search error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return Inertia::render('Games/Search', [
                'games' => [],
                'query' => $query,
                'gameLists' => $gameLists,
                'error' => 'An error occurred while searching for games. Please try again later.',
            ]);
        }
    }

    /**
     * Display the specified game.
     */
    public function show($slug)
    {
        try {
            // First try to find the game in our database
            $game = Game::where('slug', $slug)->first();

            if (!$game) {
                // If not found in our database, try to fetch from IGDB
                try {
                    $igdbGame = IGDBGame::where('slug', $slug)
                        ->with(['cover', 'genres', 'platforms', 'screenshots', 'similar_games'])
                        ->first();

                    if (!$igdbGame) {
                        return response()->json(['error' => 'Game not found'], 404);
                    }

                    // Format the game data
                    $gameData = $this->formatGame($igdbGame);

                    // Store in our database for future reference
                    $game = Game::updateOrCreate(
                        ['igdb_id' => $gameData['igdb_id']],
                        $gameData
                    );
                } catch (\Exception $e) {
                    \Log::error('Game fetch error: ' . $e->getMessage());
                    abort(500, 'An error occurred while fetching game data.');
                }
            }

            // Get user's game lists if authenticated
            $gameLists = $this->getUserGameLists();

            // Add user's status for this game if authenticated
            if (auth()->check()) {
                try {
                    $userStatus = $game->statusForUser(auth()->id());
                    $game->userStatus = $userStatus && $userStatus->status ? $userStatus->status->value : null;
                } catch (\Exception $statusEx) {
                    // If there's an issue with the status, just set it to null
                    $game->userStatus = null;
                }
            }

            return Inertia::render('Games/Show', [
                'game' => $game,
                'gameLists' => $gameLists,
            ]);
        } catch (\Exception $e) {
            \Log::error('Game show error: ' . $e->getMessage());
            abort(500, 'An error occurred while loading the game.');
        }
    }

    /**
     * Get upcoming game releases between the given dates.
     */
    private function getUpcomingGames(Carbon $startDate, Carbon $endDate)
    {
        try {
            $games = IGDBGame::whereBetween('first_release_date', [
                    $startDate->timestamp,
                    $endDate->timestamp
                ])
                ->with(['cover', 'genres', 'platforms'])
                ->where('category', 0) // Main game
                ->orderBy('first_release_date', 'asc')
                ->limit(20)
                ->get();

            // Process games one by one to identify which one causes the issue
            $formattedGames = collect();
            foreach ($games as $game) {
                try {
                    $formattedGame = $this->formatGame($game);
                    
                    // Save the game to the database to ensure it has a local ID
                    $dbGame = Game::updateOrCreate(
                        ['igdb_id' => $formattedGame['igdb_id']],
                        $formattedGame
                    );
                    
                    // Add the database ID to the formatted game
                    $formattedGame['id'] = $dbGame->id;
                    
                    // Add user's status for this game if authenticated
                    if (auth()->check()) {
                        try {
                            $userStatus = $dbGame->statusForUser(auth()->id());
                            $formattedGame['userStatus'] = $userStatus && $userStatus->status ? $userStatus->status->value : null;
                        } catch (\Exception $statusEx) {
                            // If there's an issue with the status, just set it to null
                            $formattedGame['userStatus'] = null;
                        }
                    }
                    
                    $formattedGames->push($formattedGame);
                } catch (\Exception $gameEx) {
                    // Skip this game if there's an issue
                    continue;
                }
            }

            return $formattedGames;
        } catch (\Exception $e) {
            \Log::error('Upcoming games error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Format a collection of IGDB games and save them to the database.
     */
    private function formatAndSaveGames($games)
    {
        return $games->map(function ($game) {
            $formattedGame = $this->formatGame($game);
            
            // Save the game to the database to ensure it has a local ID
            $dbGame = Game::updateOrCreate(
                ['igdb_id' => $formattedGame['igdb_id']],
                $formattedGame
            );
            
            // Add the database ID to the formatted game
            $formattedGame['id'] = $dbGame->id;
            
            // Add user's status for this game if authenticated
            if (auth()->check()) {
                $userStatus = $dbGame->statusForUser(auth()->id());
                $formattedGame['userStatus'] = $userStatus && $userStatus->status ? $userStatus->status->value : null;
            }
            
            return $formattedGame;
        });
    }
    
    /**
     * Format a collection of IGDB games without saving.
     */
    private function formatGames($games)
    {
        return $games->map(function ($game) {
            return $this->formatGame($game);
        });
    }

    /**
     * Format an IGDB game for our application.
     */
    private function formatGame($game)
    {
        $coverUrl = null;
        if (isset($game->cover)) {
            $coverUrl = 'https:' . str_replace('t_thumb', 't_cover_big', $game->cover->url);
        }

        $genres = [];
        if (isset($game->genres)) {
            $genres = collect($game->genres)->pluck('name')->toArray();
        }

        $platforms = [];
        if (isset($game->platforms)) {
            $platforms = collect($game->platforms)->pluck('name')->toArray();
        }

        $releaseDate = null;
        if (isset($game->first_release_date)) {
            $releaseDate = Carbon::createFromTimestamp($game->first_release_date)->toDateString();
        }

        $data = [
            'igdb_id' => $game->id,
            'name' => $game->name,
            'slug' => $game->slug,
            'cover_url' => $coverUrl,
            'release_date' => $releaseDate,
            'summary' => $game->summary ?? null,
            'rating' => isset($game->rating) ? round($game->rating / 10, 1) : null,
            'genres' => $genres,
            'platforms' => $platforms,
        ];

        // If this is a model from our database, include the ID
        if ($game instanceof Game) {
            $data['id'] = $game->id;
            
            // Add user's status for this game if authenticated
            if (auth()->check()) {
                $userStatus = $game->statusForUser(auth()->id());
                $data['userStatus'] = $userStatus && $userStatus->status ? $userStatus->status->value : null;
            }
        }

        return $data;
    }

    /**
     * Get the authenticated user's game lists.
     *
     * @return array
     */
    private function getUserGameLists()
    {
        if (auth()->check()) {
            return auth()->user()->gameLists()
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        }

        return [];
    }
}
