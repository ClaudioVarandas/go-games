import { AppContent } from '@/components/app-content';
import { AppHeader } from '@/components/app-header';
import { AppNavigation } from '@/components/app-navigation';
import { AppShell } from '@/components/app-shell';
import { AppSidebar } from '@/components/app-sidebar';
import { Breadcrumbs } from '@/components/breadcrumbs';
import { type BreadcrumbItem } from '@/types';
import { type PropsWithChildren } from 'react';

export default function AppSidebarLayout({ children, breadcrumbs = [] }: PropsWithChildren<{ breadcrumbs?: BreadcrumbItem[] }>) {
    return (
        <AppShell variant="sidebar">
            <div className="flex h-full w-full flex-col">
                <AppHeader breadcrumbs={[]} />
                <AppNavigation />
                <main className="mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-4 p-4">
                    {breadcrumbs.length > 1 && (
                        <div className="border-sidebar-border/70 flex w-full border-b">
                            <div className="mx-auto flex h-12 w-full items-center justify-start px-4 text-neutral-500 md:max-w-7xl">
                                <Breadcrumbs breadcrumbs={breadcrumbs} />
                            </div>
                        </div>
                    )}
                    {children}
                </main>
            </div>
        </AppShell>
    );
}
