import React from 'react';
import { AddToListButton } from './AddToListButton';
import { GameStatusButton } from './GameStatusButton';

interface GameActionsBarProps {
  gameId: number;
  gameLists: { id: number; name: string }[];
  currentStatus?: string | null;
  variant?: 'default' | 'outline' | 'secondary' | 'ghost' | 'link';
  size?: 'default' | 'sm' | 'lg' | 'icon';
  className?: string;
}

export function GameActionsBar({
  gameId,
  gameLists,
  currentStatus,
  variant = 'default',
  size = 'default',
  className = ''
}: GameActionsBarProps) {
  return (
    <div className={`flex flex-wrap gap-2 ${className}`}>
      <AddToListButton
        gameId={gameId}
        gameLists={gameLists}
        variant={variant}
        size={size}
      />
      <GameStatusButton
        gameId={gameId}
        currentStatus={currentStatus}
        variant={variant}
        size={size}
      />
    </div>
  );
}
