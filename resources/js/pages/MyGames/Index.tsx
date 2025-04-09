import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { AppShell } from '@/components/app-shell';
import { AppHeader } from '@/components/app-header';
import { AppNavigation } from '@/components/app-navigation';
import { GameCard } from '@/components/GameCard';
import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group';
import { 
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue
} from '@/components/ui/select';
import { Filter } from 'lucide-react';

// Define tab types
type TabType = 'games' | 'backlog' | 'wishlist';

const breadcrumbs = [
    {
        title: 'Home',
        href: '/',
    },
    {
        title: 'My Games',
        href: '/my-games',
    },
];

interface Game {
    id: number;
    name: string;
    slug: string;
    cover_url: string | null;
    release_date: string | null;
    rating: number | null;
    genres: string[];
    platforms: string[];
    userStatus?: string | null;
}

interface GameList {
    id: number;
    name: string;
}

// Game status options from the PHP enum
const GAME_STATUSES = {
    all: 'All',
    playing: 'Playing',
    beaten: 'Beaten',
    completed: 'Completed',
    on_hold: 'On Hold',
    abandoned: 'Abandoned'
};

interface MyGamesProps {
    games: Game[];
    gameLists: GameList[];
    filters?: {
        status?: string | null;
    };
}

export default function MyGames({ games, gameLists, filters = {} }: MyGamesProps) {
    const [activeTab, setActiveTab] = useState<TabType>('games');
    const [statusFilter, setStatusFilter] = useState<string>(filters.status || 'all');
    
    // Handle status filter change
    const handleStatusFilterChange = (value: string) => {
        setStatusFilter(value);
        
        // Update URL with the new filter
        router.get(
            '/my-games',
            value === 'all' ? {} : { status: value },
            { preserveState: true, preserveScroll: true, replace: true }
        );
    };
    
    return (
        <AppShell>
            <Head title="My Games" />
            <AppHeader breadcrumbs={[]} />
            <AppNavigation />
            
            <div className="container mx-auto px-4 py-8">
                <div className="flex flex-col space-y-6">
                    <div className="flex justify-between items-center">
                        <h1 className="text-3xl font-bold">My Games</h1>
                    </div>
                    
                    <div className="border-b border-gray-200 dark:border-gray-700">
                        <ToggleGroup 
                            type="single" 
                            value={activeTab} 
                            onValueChange={(value) => value && setActiveTab(value as TabType)}
                            className="flex"
                        >
                            <ToggleGroupItem value="games" className="px-6 py-3 text-lg">
                                Games ({games.length})
                            </ToggleGroupItem>
                            <ToggleGroupItem value="backlog" className="px-6 py-3 text-lg" disabled>
                                Backlog (Coming Soon)
                            </ToggleGroupItem>
                            <ToggleGroupItem value="wishlist" className="px-6 py-3 text-lg" disabled>
                                Wishlist (Coming Soon)
                            </ToggleGroupItem>
                        </ToggleGroup>
                    </div>
                    
                    {activeTab === 'games' && (
                        <div>
                            <div className="mb-6 flex items-center">
                                <div className="flex items-center space-x-2">
                                    <Filter className="h-5 w-5 text-gray-500" />
                                    <span className="text-gray-700 dark:text-gray-300">Filter by status:</span>
                                </div>
                                <div className="ml-3 w-48">
                                    <Select value={statusFilter} onValueChange={handleStatusFilterChange}>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select status" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {Object.entries(GAME_STATUSES).map(([value, label]) => (
                                                <SelectItem key={value} value={value}>
                                                    {label}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>
                            
                            {games.length > 0 ? (
                                <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                                    {games.map((game) => (
                                        <div key={game.id} className="transform transition-transform duration-300 hover:scale-105">
                                            <GameCard game={game} gameLists={gameLists} />
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="text-center py-12">
                                    <p className="text-gray-500 text-lg mb-4">You haven't added any games to your collection yet.</p>
                                    <p className="text-gray-500">Browse games and set their status to add them to your collection.</p>
                                </div>
                            )}
                        </div>
                    )}
                    
                    {activeTab === 'backlog' && (
                        <div className="text-center py-12">
                            <p className="text-gray-500 text-lg">Backlog feature coming soon!</p>
                        </div>
                    )}
                    
                    {activeTab === 'wishlist' && (
                        <div className="text-center py-12">
                            <p className="text-gray-500 text-lg">Wishlist feature coming soon!</p>
                        </div>
                    )}
                </div>
            </div>
        </AppShell>
    );
}
