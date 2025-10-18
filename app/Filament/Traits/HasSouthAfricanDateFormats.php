<?php

namespace App\Filament\Traits;

/**
 * Trait HasSouthAfricanDateFormats
 *
 * Provides standardized South African date and time formats for Filament resources.
 * All dates should follow DD/MM/YYYY format as per South African standards.
 *
 * Usage in Filament resources:
 *
 * use HasSouthAfricanDateFormats;
 *
 * // In table columns:
 * Tables\Columns\TextColumn::make('created_at')
 *     ->dateTime(self::saDateFormat())
 *
 * // For date-only:
 * Tables\Columns\TextColumn::make('due_date')
 *     ->date(self::saDateFormat())
 */
trait HasSouthAfricanDateFormats
{
    /**
     * Standard South African date format: DD/MM/YYYY
     * Example: 17/10/2025
     */
    public static function saDateFormat(): string
    {
        return 'd/m/Y';
    }

    /**
     * South African date with time format: DD/MM/YYYY HH:MM
     * Example: 17/10/2025 14:30
     */
    public static function saDateTimeFormat(): string
    {
        return 'd/m/Y H:i';
    }

    /**
     * South African date with full time: DD/MM/YYYY HH:MM:SS
     * Example: 17/10/2025 14:30:45
     */
    public static function saDateTimeFullFormat(): string
    {
        return 'd/m/Y H:i:s';
    }

    /**
     * South African short date format: DD/MM/YY
     * Example: 17/10/25
     */
    public static function saShortDateFormat(): string
    {
        return 'd/m/y';
    }

    /**
     * South African long date format: DD Month YYYY
     * Example: 17 October 2025
     */
    public static function saLongDateFormat(): string
    {
        return 'd F Y';
    }

    /**
     * South African medium date format: DD Mon YYYY
     * Example: 17 Oct 2025
     */
    public static function saMediumDateFormat(): string
    {
        return 'd M Y';
    }
}
