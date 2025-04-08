import React, { ReactNode } from 'react';

interface HeadingProps {
    title?: string;
    description?: string;
    children?: ReactNode;
}

export default function Heading({ title, description, children }: HeadingProps) {
    return (
        <div className="mb-8 space-y-0.5">
            <h2 className="text-xl font-semibold tracking-tight">{title || children}</h2>
            {description && <p className="text-muted-foreground text-sm">{description}</p>}
        </div>
    );
}
