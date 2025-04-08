import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { AppShell } from '@/components/app-shell';
import { AppHeader } from '@/components/app-header';
import Heading from '@/components/heading';
import { SearchBar } from '@/components/SearchBar';
import { GameCard } from '@/components/GameCard';
import { Button } from '@/components/ui/button';
import { Calendar, ChevronRight } from 'lucide-react';

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

interface IndexProps {
  nextWeekGames: Game[];
  thisMonthGames: Game[];
  gameLists?: GameList[];
}

export default function Index({ nextWeekGames, thisMonthGames, gameLists = [] }: IndexProps) {
  const breadcrumbs = [
    { title: 'Home', href: '/' },
    { title: 'Game Releases', href: '/' },
  ];

  return (
    <AppShell>
      <Head title="Game Releases" />
      <AppHeader breadcrumbs={breadcrumbs} />
      
      <div className="container md:max-w-7xl mx-auto px-4 py-8">
        <div className="mb-8">
          <Heading>Game Releases</Heading>
        </div>

        <section className="mb-12">
          <div className="flex items-center justify-between mb-6">
            <div className="flex items-center">
              <Calendar className="h-5 w-5 mr-2 text-blue-600" />
              <h2 className="text-2xl font-bold">Coming Next Week</h2>
            </div>
            <Link href="/search">
              <Button variant="ghost" className="text-blue-600 hover:text-blue-800">
                View all <ChevronRight className="h-4 w-4 ml-1" />
              </Button>
            </Link>
          </div>
          
          {nextWeekGames.length > 0 ? (
            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
              {nextWeekGames.map((game) => (
                <div key={game.igdb_id} className="transform transition-transform duration-300 hover:scale-105">
                  <GameCard game={game} gameLists={gameLists} />
                </div>
              ))}
            </div>
          ) : (
            <div className="bg-gray-50 dark:bg-gray-800 rounded-lg p-8 text-center">
              <p className="text-gray-500">No upcoming releases found for next week.</p>
            </div>
          )}
        </section>

        <section>
          <div className="flex items-center justify-between mb-6">
            <div className="flex items-center">
              <Calendar className="h-5 w-5 mr-2 text-blue-600" />
              <h2 className="text-2xl font-bold">This Month</h2>
            </div>
            <Link href="/search">
              <Button variant="ghost" className="text-blue-600 hover:text-blue-800">
                View all <ChevronRight className="h-4 w-4 ml-1" />
              </Button>
            </Link>
          </div>
          
          {thisMonthGames.length > 0 ? (
            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
              {thisMonthGames.map((game) => (
                <div key={game.igdb_id} className="transform transition-transform duration-300 hover:scale-105">
                  <GameCard game={game} gameLists={gameLists} />
                </div>
              ))}
            </div>
          ) : (
            <div className="bg-gray-50 dark:bg-gray-800 rounded-lg p-8 text-center">
              <p className="text-gray-500">No upcoming releases found for this month.</p>
            </div>
          )}
        </section>
      </div>
    </AppShell>
  );
}
