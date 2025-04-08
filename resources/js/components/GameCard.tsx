import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import { Card, CardContent, CardFooter, CardHeader } from './ui/card';
import { Badge } from './ui/badge';
import { Calendar, Gamepad2, Star } from 'lucide-react';
import { GameActionsBar } from './GameActionsBar';

interface GameCardProps {
  game: {
    id: number;
    name: string;
    slug: string;
    cover_url: string | null;
    release_date: string | null;
    rating: number | null;
    genres: string[];
    platforms: string[];
    userStatus?: string | null;
  };
  gameLists?: { id: number; name: string }[];
  showActions?: boolean;
}

export function GameCard({ game, gameLists = [], showActions = true }: GameCardProps) {
  const { auth } = usePage().props as any;
  const isAuthenticated = auth && auth.user;
  return (
    <Card className="overflow-hidden h-full flex flex-col border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-300">
      <Link href={`/games/${game.slug}`} className="block">
        <CardHeader className="p-0 relative">
          {game.cover_url ? (
            <img 
              src={game.cover_url} 
              alt={game.name} 
              className="w-full h-48 object-cover"
            />
          ) : (
            <div className="w-full h-48 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
              <Gamepad2 className="h-10 w-10 text-gray-400" />
            </div>
          )}
          {game.rating && (
            <div className="absolute top-2 right-2">
              <div className="flex items-center bg-black/70 text-white px-2 py-1 rounded-full text-xs font-medium">
                <Star className="h-3 w-3 mr-1 text-yellow-400 fill-yellow-400" />
                {game.rating}/10
              </div>
            </div>
          )}
        </CardHeader>
      </Link>
      <CardContent className="flex-grow p-4">
        <Link href={`/games/${game.slug}`} className="block">
          <h3 className="font-bold text-lg mb-2 hover:text-blue-600 transition-colors line-clamp-2">
            {game.name}
          </h3>
        </Link>
        {game.release_date && (
          <p className="text-sm text-gray-500 mb-3 flex items-center">
            <Calendar className="h-3 w-3 mr-1 text-gray-400" />
            {new Date(game.release_date).toLocaleDateString(undefined, {
              year: 'numeric',
              month: 'short',
              day: 'numeric'
            })}
          </p>
        )}
        <div className="flex flex-wrap gap-1 mb-2">
          {game.genres.slice(0, 2).map((genre) => (
            <Badge key={genre} variant="secondary" className="text-xs">
              {genre}
            </Badge>
          ))}
        </div>
      </CardContent>
      <CardFooter className="p-4 pt-0 border-t border-gray-100 dark:border-gray-800">
        <div className="flex flex-col gap-3 w-full">
          <div className="flex flex-wrap gap-1 w-full">
            {game.platforms.slice(0, 3).map((platform) => (
              <Badge key={platform} variant="outline" className="text-xs">
                {platform}
              </Badge>
            ))}
            {game.platforms.length > 3 && (
              <Badge variant="outline" className="text-xs">
                +{game.platforms.length - 3} more
              </Badge>
            )}
          </div>
          
          {isAuthenticated && showActions && (
            <GameActionsBar
              gameId={game.id}
              gameLists={gameLists}
              currentStatus={game.userStatus}
              variant="outline"
              size="sm"
              className="w-full mt-2"
            />
          )}
        </div>
      </CardFooter>
    </Card>
  );
}
