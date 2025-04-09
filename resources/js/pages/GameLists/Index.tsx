import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { AppShell } from '@/components/app-shell';
import { AppHeader } from '@/components/app-header';
import { AppNavigation } from '@/components/app-navigation';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { ListPlus, Gamepad2, Plus, ListChecks } from 'lucide-react';

interface GameList {
  id: number;
  name: string;
  description: string | null;
  is_public: boolean;
  games_count: number;
  created_at: string;
  updated_at: string;
}

interface IndexProps {
  gameLists: GameList[];
}

export default function Index({ gameLists }: IndexProps) {
  const breadcrumbs = [
    { title: 'Home', href: '/' },
    { title: 'My Lists', href: '/game-lists' },
  ];

  return (
    <AppShell>
      <Head title="My Lists" />
      <AppHeader breadcrumbs={[]} />
      <AppNavigation />
      
      <div className="container mx-auto px-4 py-8">
        <div className="flex items-center justify-between mb-8">
          <div className="flex items-center">
            <ListChecks className="h-6 w-6 mr-2 text-blue-600" />
            <Heading>My Lists</Heading>
          </div>
          <Link href="/game-lists/create">
            <Button className="flex items-center">
              <Plus className="h-4 w-4 mr-2" />
              Create New List
            </Button>
          </Link>
        </div>

        {gameLists.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {gameLists.map((list) => (
              <Card key={list.id} className="overflow-hidden h-full flex flex-col border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-300">
                <CardHeader className="p-4 pb-0">
                  <div className="flex justify-between items-start">
                    <Link href={`/game-lists/${list.id}`} className="block">
                      <h3 className="font-bold text-xl mb-1 hover:text-blue-600 transition-colors line-clamp-2">
                        {list.name}
                      </h3>
                    </Link>
                    <Badge variant={list.is_public ? "default" : "secondary"}>
                      {list.is_public ? 'Public' : 'Private'}
                    </Badge>
                  </div>
                </CardHeader>
                
                <CardContent className="p-4 flex-grow">
                  {list.description && (
                    <p className="text-gray-600 dark:text-gray-300 mb-4 line-clamp-3">
                      {list.description}
                    </p>
                  )}
                  <div className="flex items-center text-sm text-gray-500">
                    <Gamepad2 className="h-4 w-4 mr-1" />
                    <span>{list.games_count} {list.games_count === 1 ? 'game' : 'games'}</span>
                  </div>
                </CardContent>
                
                <CardFooter className="p-4 pt-0 border-t border-gray-100 dark:border-gray-800">
                  <div className="w-full">
                    <Link href={`/game-lists/${list.id}`}>
                      <Button variant="outline" className="w-full">View List</Button>
                    </Link>
                  </div>
                </CardFooter>
              </Card>
            ))}
          </div>
        ) : (
          <div className="bg-gray-50 dark:bg-gray-800 rounded-lg p-8 text-center">
            <div className="flex justify-center mb-4">
              <ListPlus className="h-12 w-12 text-gray-400" />
            </div>
            <h3 className="text-lg font-medium mb-2">No game lists yet</h3>
            <p className="text-gray-500 mb-6">Create your first game list to start organizing your favorite games.</p>
            <Link href="/game-lists/create">
              <Button>
                <Plus className="h-4 w-4 mr-2" />
                Create New List
              </Button>
            </Link>
          </div>
        )}
      </div>
    </AppShell>
  );
}
