import { Icon } from '@/components/icon';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { UserMenuContent } from '@/components/user-menu-content';
import { useInitials } from '@/hooks/use-initials';
import { type BreadcrumbItem, type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { Home, LayoutGrid, ListChecks, Menu, Search } from 'lucide-react';
import AppLogo from './app-logo';
import AppLogoIcon from './app-logo-icon';
import { SearchBar } from './SearchBar';

// Navigation items moved to app-navigation.tsx
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

interface AppHeaderProps {
    breadcrumbs?: BreadcrumbItem[];
}

export function AppHeader({ breadcrumbs = [] }: AppHeaderProps) {
    const page = usePage<SharedData>();
    const { auth } = page.props;
    const getInitials = useInitials();
    return (
        <div className="border-sidebar-border/80 border-b">
            <div className="mx-auto flex h-16 items-center justify-between px-4 md:max-w-7xl">
                {/* Mobile Menu */}
                <div className="lg:hidden">
                    <Sheet>
                        <SheetTrigger asChild>
                            <Button variant="ghost" size="icon" className="mr-2 h-[34px] w-[34px]">
                                <Menu className="h-5 w-5" />
                            </Button>
                        </SheetTrigger>
                        <SheetContent side="left" className="bg-sidebar flex h-full w-64 flex-col items-stretch justify-between">
                            <SheetTitle className="sr-only">Navigation Menu</SheetTitle>
                            <SheetHeader className="flex justify-start text-left">
                                <AppLogoIcon className="h-6 w-6 fill-current text-black dark:text-white" />
                            </SheetHeader>
                            <div className="flex h-full flex-1 flex-col space-y-4 p-4">
                                <div className="flex h-full flex-col justify-between text-sm">
                                    <div className="flex flex-col space-y-4">
                                        {mainNavItems.map((item) => (
                                            <Link key={item.title} href={item.href} className="flex items-center space-x-2 font-medium">
                                                {item.icon && <Icon iconNode={item.icon} className="h-5 w-5" />}
                                                <span>{item.title}</span>
                                            </Link>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>

                <div className="flex items-center">
                    <Link href="/" prefetch className="flex items-center space-x-2">
                        <AppLogo />
                    </Link>
                </div>

                {/* Mobile Search Button */}
                <div className="md:hidden">
                    <Link href="/search">
                        <Button variant="ghost" size="icon" className="h-9 w-9">
                            <Search className="h-5 w-5" />
                        </Button>
                    </Link>
                </div>

                {/* Desktop Search Bar in the middle */}
                <div className="hidden md:flex md:flex-1 md:justify-center md:px-4">
                    <div className="w-full max-w-md">
                        <SearchBar 
                            className="w-full" 
                            initialQuery={page.url.startsWith('/search') ? new URLSearchParams(window.location.search).get('query') || '' : ''}
                        />
                    </div>
                </div>

                <div className="flex items-center space-x-2">
                    {auth?.user ? (
                        <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                                <Button variant="ghost" className="size-10 rounded-full p-1">
                                    <Avatar className="size-8 overflow-hidden rounded-full">
                                        <AvatarImage src={auth.user.avatar} alt={auth.user.name} />
                                        <AvatarFallback className="rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                            {getInitials(auth.user.name)}
                                        </AvatarFallback>
                                    </Avatar>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent className="w-56" align="end">
                                <UserMenuContent user={auth.user} />
                            </DropdownMenuContent>
                        </DropdownMenu>
                    ) : (
                        <Link href="/login">
                            <Button variant="outline" size="sm">
                                Login
                            </Button>
                        </Link>
                    )}
                </div>
            </div>
        </div>
    );
}
