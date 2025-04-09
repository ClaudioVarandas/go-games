import { AppHeader } from '@/components/app-header';
import { AppNavigation } from '@/components/app-navigation';
import { Breadcrumbs } from '@/components/breadcrumbs';
import { SidebarInset } from '@/components/ui/sidebar';
import * as React from 'react';
import { type BreadcrumbItem } from '@/types';

interface AppContentProps extends React.ComponentProps<'main'> {
    variant?: 'header' | 'sidebar';
    breadcrumbs?: BreadcrumbItem[];
}

export function AppContent({ variant = 'header', children, breadcrumbs = [], ...props }: AppContentProps) {
    if (variant === 'sidebar') {
        return (
            <SidebarInset {...props}>
                <AppHeader breadcrumbs={[]} />
                <AppNavigation />
                {breadcrumbs.length > 1 && (
                    <div className="border-sidebar-border/70 flex w-full border-b">
                        <div className="mx-auto flex h-12 w-full items-center justify-start px-4 text-neutral-500 md:max-w-7xl">
                            <Breadcrumbs breadcrumbs={breadcrumbs} />
                        </div>
                    </div>
                )}
                {children}
            </SidebarInset>
        );
    }

    return (
        <main className="mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-4 rounded-xl" {...props}>
            {children}
        </main>
    );
}
