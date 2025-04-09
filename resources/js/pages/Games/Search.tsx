import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { AppShell } from '@/components/app-shell';
import { AppHeader } from '@/components/app-header';
import { AppNavigation } from '@/components/app-navigation';
import Heading from '@/components/heading';
import { GameCard } from '@/components/GameCard';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { ArrowLeft, Search as SearchIcon } from 'lucide-react';

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

interface SearchProps {
  games: Game[];
  query: string;
  error?: string;
  gameLists?: GameList[];
}

export default function Search({ games, query, error, gameLists = [] }: SearchProps) {
  const breadcrumbs = [
    { title: 'Home', href: '/' },
    { title: 'Search', href: '/search' },
    ...(query ? [{ title: query, href: `/search?query=${encodeURIComponent(query)}` }] : []),
  ];

  return (
    <AppShell>
      <Head title={query ? `Search: ${query}` : 'Search Games'} />
      <AppHeader breadcrumbs={[]} />
      <AppNavigation />
      
      <div className="container mx-auto px-4 py-8">
        <div className="flex items-center mb-8">
          <Link href="/">
            <Button variant="ghost" size="icon" className="mr-2">
              <ArrowLeft className="h-5 w-5" />
            </Button>
          </Link>
          <Heading>Search Games</Heading>
        </div>

        {error && (
          <Alert variant="destructive" className="mb-6">
            <AlertDescription>{error}</AlertDescription>
          </Alert>
        )}

        {query && (
          <div className="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-6 flex items-center">
            <SearchIcon className="h-5 w-5 mr-2 text-blue-600 dark:text-blue-400" />
            <h2 className="text-xl font-medium">
              {games.length > 0
                ? `Found ${games.length} result${games.length === 1 ? '' : 's'} for "${query}"`
                : `No results found for "${query}"`}
            </h2>
          </div>
        )}

        {games.length > 0 ? (
          <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            {games.map((game) => (
              <div key={game.igdb_id} className="transform transition-transform duration-300 hover:scale-105">
                <GameCard game={game} gameLists={gameLists} />
              </div>
            ))}
          </div>
        ) : query ? (
          <div className="bg-gray-50 dark:bg-gray-800 rounded-lg p-12 text-center">
            <div className="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
              <SearchIcon className="h-8 w-8 text-gray-400" />
            </div>
            <p className="text-gray-500 text-lg mb-4">No games found matching your search.</p>
            <p className="text-gray-500">Try a different search term or browse the latest releases.</p>
            <Link href="/">
              <Button variant="outline" className="mt-6">
                Browse Latest Releases
              </Button>
            </Link>
          </div>
        ) : (
          <div className="bg-gray-50 dark:bg-gray-800 rounded-lg p-12 text-center">
            <div className="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
              <SearchIcon className="h-8 w-8 text-gray-400" />
            </div>
            <p className="text-gray-500 text-lg">Enter a search term to find games.</p>
            <p className="text-gray-500 mt-2">Search by game title, genre, or platform.</p>
          </div>
        )}
      </div>
    </AppShell>
  );
}
