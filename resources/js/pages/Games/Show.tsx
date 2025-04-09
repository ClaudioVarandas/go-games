import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import { AppShell } from '@/components/app-shell';
import { AppHeader } from '@/components/app-header';
import { AppNavigation } from '@/components/app-navigation';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { ArrowLeft, Calendar, Gamepad2, Star, Tag } from 'lucide-react';
import { GameActionsBar } from '@/components/GameActionsBar';

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
  userStatus?: string | null;
}

interface GameList {
  id: number;
  name: string;
}

interface ShowProps {
  game: Game;
  gameLists?: GameList[];
}

export default function Show({ game, gameLists = [] }: ShowProps) {
  const { auth } = usePage().props as any;
  const isAuthenticated = auth && auth.user;
  const breadcrumbs = [
    { title: 'Home', href: '/' },
    { title: 'Games', href: '/' },
    { title: game.name, href: `/games/${game.slug}` },
  ];

  return (
    <AppShell>
      <Head title={game.name} />
      <AppHeader breadcrumbs={[]} />
      <AppNavigation />
      
      <div className="container mx-auto px-4 py-8">
        <div className="mb-6">
          <Link href="/">
            <Button variant="ghost" className="pl-0 flex items-center text-blue-600 hover:text-blue-800">
              <ArrowLeft className="h-4 w-4 mr-2" />
              Back to Games
            </Button>
          </Link>
        </div>
        
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {/* Game Cover */}
          <div className="md:col-span-1">
            {game.cover_url ? (
              <div className="rounded-lg overflow-hidden shadow-lg border border-gray-200 dark:border-gray-700">
                <img 
                  src={game.cover_url} 
                  alt={game.name} 
                  className="w-full h-auto"
                />
              </div>
            ) : (
              <div className="w-full aspect-[3/4] bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center shadow-lg">
                <Gamepad2 className="h-16 w-16 text-gray-400" />
              </div>
            )}
            
            <div className="mt-6 space-y-4">
              {game.release_date && (
                <Card className="overflow-hidden border-l-4 border-l-blue-500">
                  <CardContent className="p-4 flex items-start">
                    <Calendar className="h-5 w-5 mr-3 text-blue-500 mt-0.5" />
                    <div>
                      <h3 className="font-medium text-sm text-gray-500 dark:text-gray-400">Release Date</h3>
                      <p className="mt-1 font-semibold">{new Date(game.release_date).toLocaleDateString(undefined, { 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                      })}</p>
                    </div>
                  </CardContent>
                </Card>
              )}
              
              {game.rating && (
                <Card className="overflow-hidden border-l-4 border-l-yellow-500">
                  <CardContent className="p-4 flex items-start">
                    <Star className="h-5 w-5 mr-3 text-yellow-500 mt-0.5" />
                    <div>
                      <h3 className="font-medium text-sm text-gray-500 dark:text-gray-400">Rating</h3>
                      <p className="mt-1 font-semibold">{game.rating}/10</p>
                    </div>
                  </CardContent>
                </Card>
              )}
            </div>
          </div>
          
          {/* Game Details */}
          <div className="md:col-span-2">
            <h1 className="text-3xl md:text-4xl font-bold mb-4">{game.name}</h1>
            
            <div className="mt-6">
              <div className="flex items-center mb-3">
                <Tag className="h-5 w-5 mr-2 text-blue-600" />
                <h3 className="text-lg font-semibold">Genres</h3>
              </div>
              <div className="flex flex-wrap gap-2 mb-6">
                {game.genres.length > 0 ? game.genres.map((genre) => (
                  <Badge key={genre} variant="secondary" className="px-3 py-1 text-sm">
                    {genre}
                  </Badge>
                )) : (
                  <span className="text-gray-500 italic">No genres listed</span>
                )}
              </div>
              
              <div className="flex items-center mb-3">
                <Gamepad2 className="h-5 w-5 mr-2 text-blue-600" />
                <h3 className="text-lg font-semibold">Platforms</h3>
              </div>
              <div className="flex flex-wrap gap-2 mb-6">
                {game.platforms.length > 0 ? game.platforms.map((platform) => (
                  <Badge key={platform} variant="outline" className="px-3 py-1 text-sm">
                    {platform}
                  </Badge>
                )) : (
                  <span className="text-gray-500 italic">No platforms listed</span>
                )}
              </div>
            </div>
            
            <Separator className="my-6" />
            
            <div className="bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
              <h2 className="text-xl font-bold mb-4">Summary</h2>
              {game.summary ? (
                <p className="text-gray-700 dark:text-gray-300 leading-relaxed">{game.summary}</p>
              ) : (
                <p className="text-gray-500 italic">No summary available for this game.</p>
              )}
            </div>
            
            <div className="mt-8 flex flex-wrap gap-4 justify-between">
              <div className="flex gap-2">
                <Link href="/">
                  <Button variant="outline">
                    <ArrowLeft className="h-4 w-4 mr-2" />
                    Back to Games
                  </Button>
                </Link>
                
                {isAuthenticated && (
                  <GameActionsBar 
                    gameId={game.id} 
                    gameLists={gameLists}
                    currentStatus={game.userStatus}
                  />
                )}
              </div>
              
              <Link href="/search">
                <Button>
                  Find More Games
                </Button>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </AppShell>
  );
}
