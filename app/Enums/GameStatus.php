<?php

namespace App\Enums;

enum GameStatus: string
{
    case PLAYING = 'playing';
    case BEATEN = 'beaten';
    case COMPLETED = 'completed';
    case ON_HOLD = 'on_hold';
    case ABANDONED = 'abandoned';

    /**
     * Get the display name for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::PLAYING => 'Playing',
            self::BEATEN => 'Beaten',
            self::COMPLETED => 'Completed',
            self::ON_HOLD => 'On Hold',
            self::ABANDONED => 'Abandoned',
        };
    }

    /**
     * Get all statuses as an array for forms, etc.
     */
    public static function options(): array
    {
        return [
            self::PLAYING->value => self::PLAYING->label(),
            self::BEATEN->value => self::BEATEN->label(),
            self::COMPLETED->value => self::COMPLETED->label(),
            self::ON_HOLD->value => self::ON_HOLD->label(),
            self::ABANDONED->value => self::ABANDONED->label(),
        ];
    }
}
