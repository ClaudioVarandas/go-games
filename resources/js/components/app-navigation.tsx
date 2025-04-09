import { Icon } from '@/components/icon';
import { cn } from '@/lib/utils';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { Home, LayoutGrid, ListChecks } from 'lucide-react';

const mainNavItems: NavItem[] = [
    {
        title: 'Home',
        href: '/',
        icon: Home,
    },
    {
        title: 'My Games',
        href: '/my-games',
        icon: LayoutGrid,
    },
    {
        title: 'My Lists',
        href: '/game-lists',
        icon: ListChecks,
    },
];

export function AppNavigation() {
    const page = usePage<SharedData>();
    
    return (
        <div className="bg-white dark:bg-gray-800 shadow-md">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div className="flex h-12 justify-center">
                    <nav className="flex">
                        <ul className="flex space-x-8">
                            {mainNavItems.map((item, index) => (
                                <li key={index} className="relative flex items-center">
                                    <Link
                                        href={item.href}
                                        className={cn(
                                            "flex h-full items-center px-3 font-medium transition-colors",
                                            page.url === item.href 
                                                ? "text-blue-600 dark:text-blue-400" 
                                                : "text-gray-600 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400"
                                        )}
                                    >
                                        {item.icon && <Icon iconNode={item.icon} className="mr-2 h-4 w-4" />}
                                        {item.title}
                                    </Link>
                                    {page.url === item.href && (
                                        <div className="absolute bottom-0 left-0 h-0.5 w-full bg-blue-600 dark:bg-blue-400"></div>
                                    )}
                                </li>
                            ))}
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    );
}
