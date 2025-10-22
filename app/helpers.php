<?php

/**
 * Global Helper Functions for Commatix
 *
 * This file provides convenient global functions that wrap the various helper classes.
 * These functions follow South African UX standards as defined in DESIGN_SYSTEM.md
 *
 * To use these functions, ensure this file is autoloaded in composer.json:
 * "autoload": {
 *     "files": ["app/helpers.php"]
 * }
 */

use App\Helpers\SouthAfricanFormattingHelper;

if (! function_exists('format_za_currency')) {
    /**
     * Format currency in South African Rand (ZAR)
     *
     * @param  float|int|string  $amount  The amount to format
     * @param  bool  $compact  If true, omit the space (R1,250 instead of R 1,250.00)
     * @return string Formatted currency string
     *
     * @example format_za_currency(1250.00) // "R 1,250.00"
     */
    function format_za_currency(float|int|string $amount, bool $compact = false): string
    {
        return SouthAfricanFormattingHelper::formatCurrency($amount, $compact);
    }
}

if (! function_exists('format_za_date')) {
    /**
     * Format date in South African standard (DD/MM/YYYY)
     *
     * @param  Carbon\Carbon|string|null  $date  Date to format
     * @param  bool  $includeTime  Include time in format
     * @return string Formatted date string
     *
     * @example format_za_date(now()) // "15/10/2025"
     */
    function format_za_date(Carbon\Carbon|string|null $date, bool $includeTime = false): string
    {
        return SouthAfricanFormattingHelper::formatDate($date, $includeTime);
    }
}

if (! function_exists('format_za_date_long')) {
    /**
     * Format date in long South African format
     *
     * @param  Carbon\Carbon|string|null  $date  Date to format
     * @return string Formatted date string
     *
     * @example format_za_date_long(now()) // "15 October 2025"
     */
    function format_za_date_long(Carbon\Carbon|string|null $date): string
    {
        return SouthAfricanFormattingHelper::formatDateLong($date);
    }
}

if (! function_exists('format_za_phone')) {
    /**
     * Format South African phone number with spaces
     *
     * @param  string|null  $phone  Phone number to format
     * @return string Formatted phone number
     *
     * @example format_za_phone('+27123456789') // "+27 12 345 6789"
     */
    function format_za_phone(?string $phone): string
    {
        return SouthAfricanFormattingHelper::formatPhone($phone);
    }
}

if (! function_exists('format_za_number')) {
    /**
     * Format a number with South African thousand separators
     *
     * @param  float|int  $number  Number to format
     * @param  int  $decimals  Number of decimal places
     * @return string Formatted number
     *
     * @example format_za_number(1234567.89) // "1,234,567.89"
     */
    function format_za_number(float|int $number, int $decimals = 0): string
    {
        return SouthAfricanFormattingHelper::formatNumber($number, $decimals);
    }
}

if (! function_exists('format_za_percentage')) {
    /**
     * Format percentage for South African display
     *
     * @param  float|int  $value  Percentage value
     * @param  int  $decimals  Number of decimal places
     * @return string Formatted percentage
     *
     * @example format_za_percentage(0.125) // "12.5%"
     */
    function format_za_percentage(float|int $value, int $decimals = 1): string
    {
        return SouthAfricanFormattingHelper::formatPercentage($value, $decimals);
    }
}
