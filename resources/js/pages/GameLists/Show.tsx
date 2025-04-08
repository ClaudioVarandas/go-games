import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { AppShell } from '@/components/app-shell';
import { AppHeader } from '@/components/app-header';
import Heading from '@/components/heading';
import { GameCard } from '@/components/GameCard';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { 
  ArrowLeft, 
  Edit, 
  Trash2, 
  ListChecks, 
  User, 
  Calendar, 
  Gamepad2,
  AlertTriangle
} from 'lucide-react';
import { 
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog';

interface Game {
  id: number;
  igdb_id: number;
  name: string;
  slug: string;
  cover_url: string | null;
  release_date: string | null;
  summary: string | null;
  rating: number | null;
  genres: string[];
  platforms: string[];
  pivot: {
    notes: string | null;
  };
}

interface User {
  id: number;
  name: string;
}

interface GameList {
  id: number;
  name: string;
  description: string | null;
  is_public: boolean;
  user_id: number;
  created_at: string;
  updated_at: string;
  user: User;
  games: Game[];
}

interface ShowProps {
  gameList: GameList;
  canEdit: boolean;
}

export default function Show({ gameList, canEdit }: ShowProps) {
  const breadcrumbs = [
    { title: 'Home', href: '/' },
    { title: 'Game Lists', href: '/game-lists' },
    { title: gameList.name, href: `/game-lists/${gameList.id}` },
  ];

  const handleDelete = () => {
    router.delete(`/game-lists/${gameList.id}`);
  };

  const handleRemoveGame = (gameId: number) => {
    router.delete(`/game-lists/${gameList.id}/games/${gameId}`);
  };

  return (
    <AppShell>
      <Head title={gameList.name} />
      <AppHeader breadcrumbs={breadcrumbs} />
      
      <div className="container mx-auto px-4 py-8">
        <div className="mb-6">
          <Link href="/game-lists">
            <Button variant="ghost" className="pl-0 flex items-center text-blue-600 hover:text-blue-800">
              <ArrowLeft className="h-4 w-4 mr-2" />
              Back to Game Lists
            </Button>
          </Link>
        </div>
        
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {/* Game List Info */}
          <div className="md:col-span-1">
            <Card className="p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
              <div className="flex items-center mb-4">
                <ListChecks className="h-6 w-6 mr-2 text-blue-600" />
                <h2 className="text-2xl font-bold">{gameList.name}</h2>
              </div>
              
              <Badge variant={gameList.is_public ? "default" : "secondary"} className="mb-4">
                {gameList.is_public ? 'Public' : 'Private'}
              </Badge>
              
              {gameList.description && (
                <div className="mb-6">
                  <p className="text-gray-600 dark:text-gray-300">{gameList.description}</p>
                </div>
              )}
              
              <Separator className="my-4" />
              
              <div className="space-y-3 mb-6">
                <div className="flex items-center text-sm">
                  <User className="h-4 w-4 mr-2 text-gray-500" />
                  <span className="text-gray-600 dark:text-gray-300">Created by {gameList.user.name}</span>
                </div>
                <div className="flex items-center text-sm">
                  <Calendar className="h-4 w-4 mr-2 text-gray-500" />
                  <span className="text-gray-600 dark:text-gray-300">
                    Created on {new Date(gameList.created_at).toLocaleDateString()}
                  </span>
                </div>
                <div className="flex items-center text-sm">
                  <Gamepad2 className="h-4 w-4 mr-2 text-gray-500" />
                  <span className="text-gray-600 dark:text-gray-300">
                    {gameList.games.length} {gameList.games.length === 1 ? 'game' : 'games'}
                  </span>
                </div>
              </div>
              
              {canEdit && (
                <div className="space-y-3">
                  <Link href={`/game-lists/${gameList.id}/edit`} className="w-full">
                    <Button variant="outline" className="w-full flex items-center justify-center">
                      <Edit className="h-4 w-4 mr-2" />
                      Edit List
                    </Button>
                  </Link>
                  
                  <AlertDialog>
                    <AlertDialogTrigger asChild>
                      <Button variant="destructive" className="w-full flex items-center justify-center">
                        <Trash2 className="h-4 w-4 mr-2" />
                        Delete List
                      </Button>
                    </AlertDialogTrigger>
                    <AlertDialogContent>
                      <AlertDialogHeader>
                        <AlertDialogTitle>Are you sure?</AlertDialogTitle>
                        <AlertDialogDescription>
                          This action cannot be undone. This will permanently delete your game list
                          and remove all games from it.
                        </AlertDialogDescription>
                      </AlertDialogHeader>
                      <AlertDialogFooter>
                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                        <AlertDialogAction onClick={handleDelete} className="bg-red-600 hover:bg-red-700">
                          Delete
                        </AlertDialogAction>
                      </AlertDialogFooter>
                    </AlertDialogContent>
                  </AlertDialog>
                </div>
              )}
            </Card>
          </div>
          
          {/* Games in List */}
          <div className="md:col-span-2">
            <div className="flex items-center mb-6">
              <Gamepad2 className="h-5 w-5 mr-2 text-blue-600" />
              <h2 className="text-2xl font-bold">Games in this List</h2>
            </div>
            
            {gameList.games.length > 0 ? (
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {gameList.games.map((game) => (
                  <div key={game.id} className="relative">
                    <GameCard game={game} />
                    
                    {canEdit && (
                      <div className="absolute top-2 right-2">
                        <AlertDialog>
                          <AlertDialogTrigger asChild>
                            <Button size="sm" variant="destructive" className="h-8 w-8 p-0 rounded-full">
                              <Trash2 className="h-4 w-4" />
                            </Button>
                          </AlertDialogTrigger>
                          <AlertDialogContent>
                            <AlertDialogHeader>
                              <AlertDialogTitle>Remove game from list?</AlertDialogTitle>
                              <AlertDialogDescription>
                                Are you sure you want to remove "{game.name}" from this list?
                              </AlertDialogDescription>
                            </AlertDialogHeader>
                            <AlertDialogFooter>
                              <AlertDialogCancel>Cancel</AlertDialogCancel>
                              <AlertDialogAction 
                                onClick={() => handleRemoveGame(game.id)} 
                                className="bg-red-600 hover:bg-red-700"
                              >
                                Remove
                              </AlertDialogAction>
                            </AlertDialogFooter>
                          </AlertDialogContent>
                        </AlertDialog>
                      </div>
                    )}
                    
                    {game.pivot.notes && (
                      <div className="mt-2 p-3 bg-gray-50 dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-700">
                        <p className="text-sm text-gray-600 dark:text-gray-300">
                          <span className="font-medium">Notes:</span> {game.pivot.notes}
                        </p>
                      </div>
                    )}
                  </div>
                ))}
              </div>
            ) : (
              <div className="bg-gray-50 dark:bg-gray-800 rounded-lg p-8 text-center">
                <div className="flex justify-center mb-4">
                  <AlertTriangle className="h-12 w-12 text-gray-400" />
                </div>
                <h3 className="text-lg font-medium mb-2">No games in this list yet</h3>
                <p className="text-gray-500 mb-6">
                  Browse games and add them to this list to start building your collection.
                </p>
                <Link href="/search">
                  <Button>
                    Browse Games
                  </Button>
                </Link>
              </div>
            )}
          </div>
        </div>
      </div>
    </AppShell>
  );
}
